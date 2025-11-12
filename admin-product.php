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

  <!-- 検索UI（小データ向け：クライアント側フィルタ） -->
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
        <td>{{ item.jenre_id }}</td>
        <td>{{ item.name }}</td>
        <td>{{ item.price }}</td>
        <td>{{ item.stock }}</td>
        <td>{{ item.description }}</td>
        <td>{{ item.is_active == 1 ? '公開中' : '非公開' }}</td>
        <td><button @click="startEdit(item)">編集</button></td>
      </tr>
      <tr v-if="filteredProducts.length === 0">
        <td colspan="8" style="text-align:center;">該当する商品がありません</td>
      </tr>
    </tbody>
  </table>

  <!-- 編集フォーム -->
  <div v-if="editProduct" class="modal-overlay" style="position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.4);display:flex;align-items:center;justify-content:center;">
    <div class="modal-content" style="background:#fff;padding:16px;border-radius:4px;max-width:600px;width:90%;">
      <h2>商品編集フォーム</h2>
      <form @submit.prevent="updateProduct">
        <div><label>商品名：</label><input v-model="editProduct.name" required></div>
        <div><label>ジャンルID：</label><input v-model="editProduct.jenre_id" type="number" required></div>
        <div><label>価格：</label><input v-model="editProduct.price" type="number" required></div>
        <div><label>在庫：</label><input v-model="editProduct.stock" type="number" required></div>
        <div><label>説明：</label><textarea v-model="editProduct.description" required></textarea></div>
        <div>
          <label>公開：</label>
          <select v-model="editProduct.is_active">
            <option value="1">公開</option>
            <option value="0">非公開</option>
          </select>
        </div>
        <div class="modal-buttons" style="margin-top:8px;">
          <button type="submit">更新</button>
          <button type="button" @click="editProduct = null">閉じる</button>
        </div>
      </form>
    </div>
  </div>

  <!-- 追加フォーム -->
  <div v-if="showForm" class="modal-overlay" style="position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.4);display:flex;align-items:center;justify-content:center;">
    <div class="modal-content" style="background:#fff;padding:16px;border-radius:4px;max-width:600px;width:90%;">
      <h2>商品追加フォーム</h2>
      <form @submit.prevent="addProduct">
        <div><label>商品名：</label><input v-model="newProduct.name" required></div>
        <div><label>ジャンルID：</label><input v-model="newProduct.jenre_id" type="number" required></div>
        <div><label>価格：</label><input v-model="newProduct.price" type="number" required></div>
        <div><label>在庫：</label><input v-model="newProduct.stock" type="number" required></div>
        <div><label>説明：</label><textarea v-model="newProduct.description" required></textarea></div>
        <div>
          <label>公開：</label>
          <select v-model="newProduct.is_active">
            <option value="1">公開</option>
            <option value="0">非公開</option>
          </select>
        </div>
        <div class="modal-buttons" style="margin-top:8px;">
          <button type="submit">追加</button>
          <button type="button" @click="showForm = false">閉じる</button>
        </div>
      </form>
    </div>
  </div>

  <form action="admin-top.php" method="get" style="margin-top:12px;">
    <input type="submit" value="トップページへ" class="top-button">
  </form>

</div>

<!-- Vue CDN & 外部JS -->
<script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
<script src="product-vue.js"></script>

<?php require 'admin-footer.php'; ?>
