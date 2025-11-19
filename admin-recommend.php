<?php
// recommend.php
// 必要ファイル読み込み
require_once 'db-connect.php';
require_once 'header.php';


// 現在のおすすめ取得
$stmt = $pdo->query("SELECT product_id FROM recommended ORDER BY sort_order ASC");
$current = $stmt->fetchAll(PDO::FETCH_COLUMN);


// 商品一覧取得（候補）
$stmt2 = $pdo->query("SELECT product_id, name, jenre_id FROM product WHERE is_active = 1");
$products = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
.wrapper { width: 90%; margin: auto; }
.section-title { font-size: 2rem; margin: 30px 0 10px; }
.items { display: flex; gap: 40px; flex-wrap: wrap; }
.item-box { width: 200px; height: 200px; background: #f4c429; border-radius: 10px; display:flex; align-items:center; justify-content:center; cursor:grab; }
.item-box img { width: 100%; height: 100%; object-fit: contain; }
.drop-zone { min-height: 220px; border: 2px dashed #999; padding:20px; display:flex; gap:40px; }
.save-btn { margin-top: 30px; padding: 10px 20px; background:#6b58e0; color:white; border-radius:8px; text-decoration:none; }
</style>


<div class="wrapper">
<h2 class="section-title">現在のおすすめ</h2>
<div id="current" class="drop-zone" ondrop="drop(event)" ondragover="allowDrop(event)">
<?php foreach ($current as $id): ?>
<?php
$p = array_values(array_filter($products, fn($a) => $a['product_id'] == $id))[0];
?>
<div class="item-box" draggable="true" ondragstart="drag(event)" data-id="<?= $p['product_id'] ?>">
<img src="/image/<?= $p['jenre_id'] ?>.png">
</div>
<?php endforeach; ?>
</div>


<h2 class="section-title">おすすめ候補</h2>
<div id="candidates" class="drop-zone" ondrop="drop(event)" ondragover="allowDrop(event)">
<?php foreach ($products as $p): if (!in_array($p['product_id'], $current)): ?>
<div class="item-box" draggable="true" ondragstart="drag(event)" data-id="<?= $p['product_id'] ?>">
<img src="/image/<?= $p['jenre_id'] ?>.png">
</div>
<?php endif; endforeach; ?>
</div>


<form action="save_recommend.php" method="post">
<input type="hidden" name="order" id="orderInput">
<button class="save-btn" onclick="saveOrder()">保存する</button>
</form>
</div>


<script>
let dragItem = null;
function drag(ev){ dragItem = ev.target; }
function allowDrop(ev){ ev.preventDefault(); }
function drop(ev){ ev.preventDefault(); ev.target.closest('.drop-zone').appendChild(dragItem); }
function saveOrder(){
const ids = [...document.querySelectorAll('#current .item-box')].map(b => b.dataset.id);
document.getElementById('orderInput').value = JSON.stringify(ids);
}
</script>


<?php require_once 'footer.php'; ?>