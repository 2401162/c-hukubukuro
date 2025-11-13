<?php require 'admin-header.php'; ?>
<?php require 'admin-db-connect.php'; ?>
<?php require 'admin-menu.php'; ?>

<?php
if (!$pdo) {
  echo "データベースに接続できません";
  exit;
}

$sql = $pdo->query('SELECT * FROM product');
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
  <div class="search-bar" style="margin:10px 0;">
    <input type="text" v-model="searchQuery" placeholder="商品名・ジャンルID・説明で検索" style="padding:6px;">
    <select v-model="searchField" style="margin-left:8px;padding:6px;">
      <option value="all">すべて</option>
      <option value="name">商品名</option>
      <option value="jenre_id">ジャンルID</option>
    </select>
    <button @click="clearSearch" style="margin-left:8px;padding:6px;">クリア</button>
  </div>

  <h2>商品一覧</h2>
  <table border="1" cellpadding="6" cellspacing="0" style="width:100%;border-collapse:collapse;">
    <thead>
      <tr>
        <th>商品ID</th>
        <th>画像</th>
        <th>ジャンルID</th>
        <th>商品名</th>
        <th>価格</th>
        <th>在庫</th>
        <th>説明</th>
        <th>公開状態</th>
        <th>操作</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="item in filteredProducts" :key="item.product_id">
        <td>{{ item.product_id }}</td>
        <td>
          <!-- 画像表示ボタン -->
          <button v-if="item.image_path" @click="showImageModal(item)" style="padding:4px 8px;">画像表示</button>
          <span v-else style="color:#999;">なし</span>
        </td>
        <td>{{ item.jenre_id }}</td>
        <td>{{ item.name }}</td>
        <td>{{ item.price }}</td>
        <td>{{ item.stock }}</td>
        <td>{{ item.description }}</td>
        <td>{{ item.is_active == 1 ? '公開中' : '非公開' }}</td>
        <td><button @click="startEdit(item)">編集</button></td>
      </tr>
      <tr v-if="filteredProducts.length === 0">
        <td colspan="9" style="text-align:center;">該当する商品がありません</td>
      </tr>
    </tbody>
  </table>

  <!-- 画像表示モーダル -->
  <div v-if="imageModal" class="modal-overlay" style="position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.6);display:flex;align-items:center;justify-content:center;z-index:999;">
    <div class="modal-content" style="background:#fff;padding:16px;border-radius:4px;max-width:800px;width:90%;text-align:center;">
      <h2>{{ imageModal.name }}</h2>
      <img :src="imageModal.image_path" style="max-width:100%;max-height:600px;border:1px solid #ddd;margin:12px 0;">
      <div style="margin-top:12px;">
        <button @click="imageModal = null">閉じる</button>
      </div>
    </div>
  </div>

  <!-- 編集フォーム -->
  <div v-if="editProduct" class="modal-overlay" style="position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.4);display:flex;align-items:center;justify-content:center;">
    <div class="modal-content" style="background:#fff;padding:16px;border-radius:4px;max-width:600px;width:90%;">
      <h2>商品編集フォーム</h2>
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
            <img :src="editProduct.image_path" style="max-width:160px;max-height:120px;border:1px solid #ddd;">
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
          <button type="button" @click="editProduct = null" style="padding:8px 16px;flex:1;">閉じる</button>
        </div>
      </form>
    </div>
  </div>

  <!-- 追加フォーム -->
  <div v-if="showForm" class="modal-overlay" style="position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.4);display:flex;align-items:center;justify-content:center;">
    <div class="modal-content" style="background:#fff;padding:16px;border-radius:4px;max-width:600px;width:90%;">
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
          <button type="button" @click="showForm = false" style="padding:8px 16px;flex:1;">閉じる</button>
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