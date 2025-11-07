<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require 'header.php';
require 'menu.php';
require 'db-connect.php';

$sort = $_GET['sort'] ?? 'all';
$allowed = ['all','recommend','ranking'];
if (!in_array($sort, $allowed, true)) { $sort = 'all'; }

$orderMap = [
  'all'        => 'p.id DESC',
  'recommend'  => 'reco DESC, total_sold DESC, p.id DESC',
  'ranking'    => 'total_sold DESC, p.id DESC',
];
$orderSql = $orderMap[$sort];

$sql = $pdo->query("
  SELECT
    p.id,
    p.name,
    p.price,
    p.image,
    COALESCE(p.recommend, 0) AS reco,
    COALESCE(SUM(od.quantity), 0) AS total_sold
  FROM product p
  LEFT JOIN order_detail od ON od.product_id = p.id
  GROUP BY p.id, p.name, p.price, p.image, p.recommend
  ORDER BY {$orderSql}
");
$products = $sql->fetchAll(PDO::FETCH_ASSOC);
?>
<style>
.list-header { display:flex; align-items:center; justify-content:space-between; gap:12px; margin:12px auto 4px; max-width:980px; padding:0 12px;}
.list-title { font-size:20px; font-weight:700; color:#333; }
.sort-tabs { display:flex; gap:8px; }
.sort-tabs a { display:inline-block; padding:6px 12px; border:1px solid #ddd; border-radius:999px; text-decoration:none; font-size:13px; background:#fff; }
.sort-tabs a.active { border-color:#ec4c4c; color:#ec4c4c; font-weight:700; }
.product-wrap { max-width:980px; margin:8px auto 40px; padding:0 12px; }
.product-grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(180px,1fr)); gap:16px; }
.card { background:#fff; border:1px solid #eee; border-radius:12px; overflow:hidden; }
.card .thumb { aspect-ratio:1/1; width:100%; object-fit:cover; display:block; background:#f6f6f6; }
.card .body { padding:10px 12px; }
.name { font-size:14px; font-weight:600; color:#333; line-height:1.4; margin:0 0 6px; }
.price { font-size:14px; color:#555; }
.badge { display:inline-block; font-size:11px; padding:2px 8px; border-radius:999px; margin-left:6px; background:#ffecec; color:#ec4c4c; border:1px solid #ffd7d7; vertical-align:middle; }
.breadcrumb { max-width:980px; margin:8px auto 0; padding:0 12px; color:#777; font-size:12px;}
</style>

<div class="breadcrumb">ホーム &gt; 商品一覧</div>

<div class="list-header">
  <div class="list-title">
    商品一覧
    <?php if($sort==='recommend'): ?>
      <span class="badge">おすすめ順</span>
    <?php elseif($sort==='ranking'): ?>
      <span class="badge">ランキング順</span>
    <?php endif; ?>
  </div>
  <div class="sort-tabs">
    <a href="?sort=all"        class="<?= $sort==='all' ? 'active':'' ?>">新着</a>
    <a href="?sort=recommend"  class="<?= $sort==='recommend' ? 'active':'' ?>">おすすめ</a>
    <a href="?sort=ranking"    class="<?= $sort==='ranking' ? 'active':'' ?>">ランキング</a>
  </div>
</div>

<div class="product-wrap">
  <div class="product-grid">
    <?php foreach ($products as $p): ?>
      <a class="card" href="product-detail.php?id=<?= htmlspecialchars($p['id'], ENT_QUOTES) ?>">
        <img class="thumb" src="<?= htmlspecialchars($p['image'] ?: 'images/noimage.png', ENT_QUOTES) ?>" alt="">
        <div class="body">
          <div class="name"><?= htmlspecialchars($p['name'], ENT_QUOTES) ?></div>
          <div class="price">¥<?= number_format((int)$p['price']) ?></div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</div>

<?php require 'footer.php'; ?>
