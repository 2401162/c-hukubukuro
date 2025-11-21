<?php
// admin-recommend.php
require_once 'admin-db-connect.php';
require_once 'admin-header.php';
require 'admin-menu.php';

// 現在のおすすめ（recommended）を取得
$stmt = $pdo->query("SELECT product_id FROM recommended ORDER BY sort_order ASC");
$current = $stmt->fetchAll(PDO::FETCH_COLUMN);

// 商品一覧（候補）取得
$stmt2 = $pdo->query("SELECT product_id, name, jenre_id FROM product WHERE is_active = 1 ORDER BY product_id ASC");
$products = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// products を product_id をキーにした配列にもしておくと便利
$productsById = [];
foreach ($products as $p) {
    $productsById[$p['product_id']] = $p;
}
?>

<style>
/* 最低限のスタイル（必要なら header.css に統合して） */
.wrapper { width: 90%; margin: 30px auto; }
.section-title { font-size: 2.4rem; margin: 30px 0 10px; }
.drop-zone { min-height: 220px; border: 2px dashed #bbb; padding:20px; display:flex; gap:24px; flex-wrap:wrap; align-items:flex-start; background:#fff; }
.item-box { width: 180px; height: 180px; background: #f4c429; border-radius: 6px; display:flex; align-items:center; justify-content:center; cursor:grab; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
.item-box img { width: 100%; height: 100%; object-fit: cover; border-radius:4px; }
.save-btn { margin-top: 24px; padding: 10px 18px; background:#6b58e0; color:white; border:none; border-radius:8px; cursor:pointer; font-size:16px; }
</style>

<body>
<div class="wrapper">
  <h2 class="section-title">現在のおすすめ</h2>
  <div id="current" class="drop-zone" ondragover="allowDrop(event)" ondrop="drop(event)">
    <?php foreach ($current as $id): 
      if (!isset($productsById[$id])) continue;
      $p = $productsById[$id];
    ?>
      <div class="item-box" draggable="true" ondragstart="drag(event)" data-id="<?= htmlspecialchars($p['product_id']) ?>">
        <img src="image/<?= htmlspecialchars($p['jenre_id']) ?>.png" alt="<?= htmlspecialchars($p['name']) ?>">
      </div>
    <?php endforeach; ?>
  </div>

  <h2 class="section-title">おすすめ候補</h2>
  <div id="candidates" class="drop-zone" ondragover="allowDrop(event)" ondrop="drop(event)">
    <?php foreach ($products as $p): if (!in_array($p['product_id'], $current)): ?>
      <div class="item-box" draggable="true" ondragstart="drag(event)" data-id="<?= htmlspecialchars($p['product_id']) ?>">
        <img src="image/<?= htmlspecialchars($p['jenre_id']) ?>.png" alt="<?= htmlspecialchars($p['name']) ?>">
      </div>
    <?php endif; endforeach; ?>
  </div>

  <form action="admin-save_recommend.php" method="post" onsubmit="prepareSubmit(event)">
    <input type="hidden" name="order" id="orderInput" value="">
    <button type="submit" class="save-btn">保存する</button>
  </form>
</div>

<script>
let dragItem = null;
function drag(ev){
  // .item-box がドラッグ開始対象
  dragItem = ev.target.closest('.item-box');
}
function allowDrop(ev){
  ev.preventDefault();
}
function drop(ev){
  ev.preventDefault();
  const zone = ev.target.closest('.drop-zone');
  if (!zone || !dragItem) return;
  // ドロップ位置は単純に zone の最後に追加（必要なら座標で挿入も可能）
  zone.appendChild(dragItem);
  dragItem = null;
}

// 送信前に current 内の順序を取得して hidden に入れる
function prepareSubmit(e){
  const ids = Array.from(document.querySelectorAll('#current .item-box')).map(n => n.dataset.id);
  document.getElementById('orderInput').value = JSON.stringify(ids);
  // 送信続行
}
</script>
<form action="admin-top.php" method="get">
    <button type="submit" class="top-button">トップページへ</button>
</form>
<?php require_once 'admin-footer.php'; ?>
</body>
</html>