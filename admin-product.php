<?php require 'admin-auth.php'; ?>
<?php require 'admin-header.php'; ?>
<?php require 'admin-db-connect.php'; ?>
<?php require 'admin-menu.php'; ?>

<?php
if (!$pdo) {
  echo "データベースに接続できません";
  exit;
}

// order_item テーブルの存在チェック
$check = $pdo->prepare("SELECT COUNT(*) AS cnt FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'order_item'");
$check->execute();
$hasOrderItem = (int)$check->fetchColumn() > 0;

if ($hasOrderItem) {
  // 商品ごとの販売数・売上金額を集計
  $sql = $pdo->query("
    SELECT 
      p.*,
      COALESCE(SUM(oi.quantity), 0) AS total_sold,
      COALESCE(SUM(oi.quantity * oi.unit_price), 0) AS total_revenue
    FROM product p
    LEFT JOIN order_item oi ON oi.product_id = p.product_id
    GROUP BY p.product_id, p.name, p.price, p.stock, p.description, p.image_path, p.is_active, p.jenre_id, p.created_at
    ORDER BY total_sold DESC, p.created_at DESC
  ");
} else {
  // order_item が無い場合はフォールバック
  $sql = $pdo->query("
    SELECT p.*, 0 AS total_sold, 0 AS total_revenue
    FROM product p
    ORDER BY p.created_at DESC
  ");
}

$data = [];
if ($sql->rowCount() > 0) {
  foreach ($sql as $row) {
    $data[] = $row;
  }
}
?>

<!-- JSONデータをVueに渡す -->
<script>
  const productData = <?php echo json_encode($data, JSON_UNESCAPED_UNICODE); ?>;
</script>

<!-- Vueアプリケーション -->
<div id="app" v-cloak>
  <div class="header-bar">
    <h1>商品管理</h1>
    <button class="add-button" @click="showForm = !showForm">商品追加</button>
  </div>

  <!-- 検索UI -->
  <div class="search-bar" style="margin:10px 0;padding:12px;background:#f5f5f5;border-radius:4px;">
    <div style="margin-bottom:8px;">
      <input type="text" v-model="searchQuery" placeholder="商品名・説明で検索" style="padding:6px;width:300px;">
      <select v-model="searchField" style="margin-left:8px;padding:6px;">
        <option value="all">すべて</option>
        <option value="name">商品名</option>
        <option value="description">説明</option>
      </select>
      <button @click="clearSearch" style="margin-left:8px;padding:6px;">クリア</button>
    </div>

    <!-- ソート -->
    <div>
      <label>並び替え：</label>
      <select v-model="sortBy" style="margin-left:8px;padding:6px;">
        <option value="sold_desc">売上数（多い順）</option>
        <option value="sold_asc">売上数（少ない順）</option>
        <option value="revenue_desc">売上金額（多い順）</option>
        <option value="new">新着順</option>
      </select>
    </div>
  </div>

  <h2>商品一覧（{{ filteredProducts.length }}件）</h2>
  <table border="1" cellpadding="6" cellspacing="0" style="width:100%;border-collapse:collapse;font-size:13px;">
    <thead>
      <tr style="background:#f0f0f0;">
        <th>商品ID</th>
        <th>画像</th>
        <th>ジャンルID</th>
        <th>商品名</th>
        <th>価格</th>
        <th>在庫</th>
        <th style="background:#fff9e6;"><strong>売上数</strong></th>
        <th style="background:#fff9e6;"><strong>売上金額</strong></th>
        <th>公開状態</th>
        <th>操作</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="item in sortedProducts" :key="item.product_id" :style="{ background: item.total_sold > 0 ? '#f0f9ff' : '#fff' }">
        <td>{{ item.product_id }}</td>
        <td>
          <button v-if="item.image_path" @click="showImageModal(item)" style="padding:4px 8px;">表示</button>
          <span v-else style="color:#999;">なし</span>
        </td>
        <td>{{ item.jenre_id }}</td>
        <td><strong>{{ item.name }}</strong></td>
        <td>¥{{ Number(item.price).toLocaleString() }}</td>
        <td style="text-align:center;">{{ item.stock }}</td>
        <td style="background:#fff9e6;text-align:center;font-weight:700;color:#ff9800;">{{ item.total_sold }}</td>
        <td style="background:#fff9e6;text-align:right;font-weight:700;color:#ff9800;">¥{{ Number(item.total_revenue).toLocaleString() }}</td>
        <td>
          <span v-if="item.is_active == 1" style="color:green;font-weight:700;">●公開中</span>
          <span v-else style="color:red;font-weight:700;">●非公開</span>
        </td>
        <td><button @click="startEdit(item)">編集</button></td>
      </tr>
      <tr v-if="sortedProducts.length === 0">
        <td colspan="10" style="text-align:center;color:#999;">該当する商品がありません</td>
      </tr>
    </tbody>
  </table>

  <!-- 画像表示モーダル -->
  <div v-if="imageModal" class="modal-overlay" style="position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.6);display:flex;align-items:center;justify-content:center;z-index:999;">
    <div class="modal-content" style="background:#fff;padding:16px;border-radius:4px;max-width:800px;width:90%;text-align:center;">
      <h2>{{ imageModal.name }}</h2>
      <p style="color:#777;font-size:14px;margin:8px 0;">売上: <strong>{{ imageModal.total_sold }}個</strong> / 売上金額: <strong>¥{{ Number(imageModal.total_revenue).toLocaleString() }}</strong></p>
      <img :src="resolveImagePath(imageModal.image_path)" style="max-width:100%;max-height:600px;border:1px solid #ddd;margin:12px 0;" onerror="this.src='img/noimage.png'">
      <div style="margin-top:12px;">
        <button @click="imageModal = null">閉じる</button>
      </div>
    </div>
  </div>

  <!-- 編集フォーム -->
  <div v-if="editProduct" class="modal-overlay" style="position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.4);display:flex;align-items:center;justify-content:center;">
    <div class="modal-content" style="background:#fff;padding:16px;border-radius:4px;max-width:600px;width:90%;max-height:90vh;overflow-y:auto;">
      <h2>商品編集フォーム</h2>
      <p v-if="editProduct.total_sold > 0" style="background:#fffbea;padding:8px;border-radius:4px;margin-bottom:12px;color:#666;font-size:12px;">
        <strong>売上情報：</strong> 売上数 <strong>{{ editProduct.total_sold }}</strong>個 / 売上金額 <strong>¥{{ Number(editProduct.total_revenue).toLocaleString() }}</strong>
      </p>
      <form @submit.prevent="updateProduct">
        <div style="margin-bottom:8px;">
          <label>商品名：</label>
          <input v-model="editProduct.name" required style="width:100%;padding:6px;">
        </div>
        <div style="margin-bottom:8px;">
          <label>ジャンルID：</label>
          <input v-model="editProduct.jenre_id" type="number" required style="width:100%;padding:6px;">
        </div>
        <div style="margin-bottom:8px;">
          <label>価格：</label>
          <input v-model="editProduct.price" type="number" required style="width:100%;padding:6px;">
        </div>
        <div style="margin-bottom:8px;">
          <label>在庫：</label>
          <input v-model="editProduct.stock" type="number" required style="width:100%;padding:6px;">
        </div>
        <div style="margin-bottom:8px;">
          <label>説明：</label>
          <textarea v-model="editProduct.description" required style="width:100%;padding:6px;min-height:80px;"></textarea>
        </div>

        <div style="margin-top:8px;margin-bottom:8px;">
          <label>現在の画像：</label>
          <div v-if="editProduct.image_path" style="margin:6px 0;">
            <img :src="resolveImagePath(editProduct.image_path)" style="max-width:160px;max-height:120px;border:1px solid #ddd;" onerror="this.src='img/noimage.png'">
          </div>
          <label>画像を変更：</label>
          <input type="file" accept="image/*" @change="onFileChange($event, true)" style="width:100%;padding:6px;">
          <div v-if="editProduct.preview" style="margin-top:8px;">
            <img :src="editProduct.preview" style="max-width:160px;max-height:120px;border:1px solid #ddd;">
          </div>
        </div>

        <div style="margin-bottom:8px;">
          <label>公開状態：</label>
          <select v-model="editProduct.is_active" style="width:100%;padding:6px;">
            <option value="1">公開</option>
            <option value="0">非公開</option>
          </select>
        </div>

        <div class="modal-buttons" style="margin-top:12px;display:flex;gap:8px;">
          <button type="submit" style="padding:8px 16px;flex:1;">更新</button>
          <button type="button" @click="editProduct = null" style="padding:8px 16px;flex:1;">キャンセル</button>
        </div>
      </form>
    </div>
  </div>

  <!-- 追加フォーム -->
  <div v-if="showForm" class="modal-overlay" style="position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.4);display:flex;align-items:center;justify-content:center;">
    <div class="modal-content" style="background:#fff;padding:16px;border-radius:4px;max-width:600px;width:90%;max-height:90vh;overflow-y:auto;">
      <h2>商品追加フォーム</h2>
      <form @submit.prevent="addProduct">
        <div style="margin-bottom:8px;">
          <label>商品名：</label>
          <input v-model="newProduct.name" required style="width:100%;padding:6px;">
        </div>
        <div style="margin-bottom:8px;">
          <label>ジャンルID：</label>
          <input v-model="newProduct.jenre_id" type="number" required style="width:100%;padding:6px;">
        </div>
        <div style="margin-bottom:8px;">
          <label>価格：</label>
          <input v-model="newProduct.price" type="number" required style="width:100%;padding:6px;">
        </div>
        <div style="margin-bottom:8px;">
          <label>在庫：</label>
          <input v-model="newProduct.stock" type="number" required style="width:100%;padding:6px;">
        </div>
        <div style="margin-bottom:8px;">
          <label>説明：</label>
          <textarea v-model="newProduct.description" required style="width:100%;padding:6px;min-height:80px;"></textarea>
        </div>

        <div style="margin-top:8px;margin-bottom:8px;">
          <label>画像：</label>
          <input type="file" accept="image/*" @change="onFileChange($event, false)" style="width:100%;padding:6px;">
          <div v-if="newProduct.preview" style="margin-top:8px;">
            <img :src="newProduct.preview" style="max-width:160px;max-height:120px;border:1px solid #ddd;">
          </div>
        </div>

        <div style="margin-bottom:8px;">
          <label>公開状態：</label>
          <select v-model="newProduct.is_active" style="width:100%;padding:6px;">
            <option value="1">公開</option>
            <option value="0">非公開</option>
          </select>
        </div>

        <div class="modal-buttons" style="margin-top:12px;display:flex;gap:8px;">
          <button type="submit" style="padding:8px 16px;flex:1;">追加</button>
          <button type="button" @click="showForm = false" style="padding:8px 16px;flex:1;">キャンセル</button>
        </div>
      </form>
    </div>
  </div>

  <form action="admin-top.php" method="get" style="margin-top:12px;">
    <input type="submit" value="トップページへ" class="top-button" style="padding:8px 16px;">
  </form>

</div>

<!-- Vue CDN & 外部JS -->
<script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
<script src="product-vue.js"></script>

<?php require 'admin-footer.php'; ?>