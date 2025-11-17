<?php require 'admin-auth.php'; ?>
<?php require 'admin-header.php'; ?>
<?php require 'admin-menu.php'; ?>
<?php require 'admin-db-connect.php'; ?>

<?php
if (!$pdo) {
  echo "データベースに接続できません";
  exit;
}

$sql = $pdo->query('SELECT * FROM review WHERE is_active = 1 ORDER BY created_at DESC');
$data = [];
if ($sql->rowCount() > 0) {
  foreach ($sql as $row) {
    $data[] = $row;
  }
}
?>

<!-- JSONデータをVueに渡す -->
<script>
  const reviewData = <?php echo json_encode($data, JSON_UNESCAPED_UNICODE); ?>;
</script>

<!-- Vueアプリケーション -->
<div id="app" v-cloak>
  <div class="header-bar">
    <h1>レビュー管理</h1>
  </div>

  <!-- 検索UI -->
  <div class="search-bar" style="margin:10px 0;">
    <input type="text" v-model="searchQuery" placeholder="顧客ID・評価・コメントで検索" style="padding:6px;">
    <select v-model="searchField" style="margin-left:8px;padding:6px;">
      <option value="all">すべて</option>
      <option value="customer_id">顧客ID</option>
      <option value="rating">評価</option>
      <option value="comment">コメント</option>
    </select>
    <button @click="clearSearch" style="margin-left:8px;padding:6px;">クリア</button>
  </div>

  <h2>レビュー一覧</h2>
  <table border="1" cellpadding="6" cellspacing="0" style="width:100%;border-collapse:collapse;">
    <thead>
      <tr>
        <th>ID</th>
        <th>顧客ID</th>
        <th>評価</th>
        <th>コメント</th>
        <th>作成日時</th>
        <th>操作</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="item in filteredReviews" :key="item.review_id">
        <td>{{ item.review_id }}</td>
        <td>{{ item.customer_id }}</td>
        <td style="text-align:center;font-weight:bold;">{{ item.rating }}★</td>
        <td>{{ truncateText(item.comment, 50) }}</td>
        <td>{{ formatDate(item.created_at) }}</td>
        <td>
          <button @click="showDetail(item)" style="padding:4px 8px;margin-right:4px;">詳細</button>
          <button @click="deleteReview(item.review_id)" style="padding:4px 8px;background-color:#dc3545;color:white;">削除</button>
        </td>
      </tr>
      <tr v-if="filteredReviews.length === 0">
        <td colspan="6" style="text-align:center;">レビューがありません</td>
      </tr>
    </tbody>
  </table>

  <!-- 詳細表示モーダル -->
  <div v-if="detailModal" class="modal-overlay" style="position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.4);display:flex;align-items:center;justify-content:center;z-index:999;">
    <div class="modal-content" style="background:#fff;padding:16px;border-radius:4px;max-width:600px;width:90%;">
      <h2>レビュー詳細</h2>
      <div style="margin-bottom:12px;">
        <label style="font-weight:bold;">レビューID：</label>
        <p>{{ detailModal.review_id }}</p>
      </div>
      <div style="margin-bottom:12px;">
        <label style="font-weight:bold;">顧客ID：</label>
        <p>{{ detailModal.customer_id }}</p>
      </div>
      <div style="margin-bottom:12px;">
        <label style="font-weight:bold;">評価：</label>
        <p style="font-size:20px;color:#ff9800;">{{ detailModal.rating }}★</p>
      </div>
      <div style="margin-bottom:12px;">
        <label style="font-weight:bold;">コメント：</label>
        <p style="border:1px solid #ddd;padding:8px;border-radius:4px;background:#f9f9f9;white-space:pre-wrap;">{{ detailModal.comment }}</p>
      </div>
      <div style="margin-bottom:12px;">
        <label style="font-weight:bold;">作成日時：</label>
        <p>{{ detailModal.created_at }}</p>
      </div>
      <div style="margin-top:12px;text-align:right;">
        <button @click="detailModal = null" style="padding:8px 16px;background-color:#ccc;border-radius:4px;border:none;cursor:pointer;">閉じる</button>
      </div>
    </div>
  </div>

  <form action="admin-top.php" method="get" style="margin-top:12px;">
    <input type="submit" value="トップページへ" class="top-button" style="padding:8px 16px;">
  </form>

</div>

<!-- Vue CDN & 外部JS -->
<script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
<script src="review-vue.js"></script>

<?php require 'admin-footer.php'; ?>