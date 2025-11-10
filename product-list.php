<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require 'header.php';

// 並び替えパラメータ
$sort = $_GET['sort'] ?? 'all';
$allowed = ['all','recommend','ranking'];
$notice = null;
if (!in_array($sort, $allowed, true)) {
  $notice = '指定された並び替えは無効のため、新着順で表示しています。';
  $sort = 'all';
}

// ダミーデータ（後でDBに差し替え）
$dummy = [
  ['id'=>1,'name'=>'福袋（レディース）','price'=>3980,'image'=>'images/sample1.jpg','reco'=>1,'total_sold'=>120],
  ['id'=>2,'name'=>'福袋（メンズ）'    ,'price'=>4280,'image'=>'images/sample2.jpg','reco'=>0,'total_sold'=>80 ],
  ['id'=>3,'name'=>'福袋（キッズ）'    ,'price'=>2580,'image'=>'images/sample3.jpg','reco'=>1,'total_sold'=>200],
];

// 並び替え（本番と同じ仕様）
if ($sort === 'recommend') {
  usort($dummy, fn($a,$b) => [$b['reco'],$b['total_sold'],$b['id']] <=> [$a['reco'],$a['total_sold'],$a['id']]);
} elseif ($sort === 'ranking') {
  usort($dummy, fn($a,$b) => [$b['total_sold'],$b['id']] <=> [$a['total_sold'],$a['id']]);
} else {
  usort($dummy, fn($a,$b) => $b['id'] <=> $a['id']); // 新着
}
$products = $dummy;

// ここからページ全体ラッパー（フッター固定用）
?>
<div class="page">
<main class="site-main">
<style>
/* ページ全体レイアウト（フッターを最下部に） */
html, body { height: 100%; }
.page { min-height: 100dvh; display: flex; flex-direction: column; }
.site-main { flex: 1 0 auto; }

/* パンくず */
.breadcrumb { max-width:1200px; margin:10px auto 0; padding:0 12px; color:#777; font-size:12px; }

/* 見出し＆ソート */
.list-header {
  display:flex; align-items:center; justify-content:space-between;
  gap:16px; max-width:1200px; padding:0 12px;
  margin:14px auto 28px; /* 下マージンを広めに */
}
.list-title { font-size:20px; font-weight:700; color:#333; }
.badge { display:inline-block; font-size:11px; padding:2px 8px; border-radius:999px; margin-left:6px; background:#ffecec; color:#ec4c4c; border:1px solid #ffd7d7; vertical-align:middle; }

.sort-tabs { display:flex; gap:16px; }          /* ← 間隔を広げる */
.sort-tabs a {
  display:inline-block; padding:8px 14px;       /* ← 余白も少し増量 */
  border:1px solid #ddd; border-radius:999px; text-decoration:none; font-size:13px; background:#fff;
}
.sort-tabs a.active { border-color:#ec4c4c; color:#ec4c4c; font-weight:700; }

/* コンテンツ */
.product-wrap { max-width:1200px; margin:0 auto 40px; padding:0 12px; }

/* 最大4列（均等） */
.product-grid { display:grid; grid-template-columns:1fr; gap:16px; align-items:stretch; justify-items:stretch; }
@media (min-width: 600px){ .product-grid { grid-template-columns: repeat(2,1fr); } }
@media (min-width: 900px){ .product-grid { grid-template-columns: repeat(3,1fr); } }
@media (min-width:1100px){ .product-grid { grid-template-columns: repeat(4,1fr); } }

.card { background:#fff; border:1px solid #eee; border-radius:12px; overflow:hidden; display:block; text-decoration:none; height:100%; }
.card .thumb { aspect-ratio:1/1; width:100%; object-fit:cover; display:block; background:#f6f6f6; }
.card .body { padding:10px 12px; display:flex; flex-direction:column; gap:6px; }
.name { font-size:14px; font-weight:600; color:#333; line-height:1.4; margin:0; }
.price { font-size:14px; color:#555; }

/* 軽量の通知/空状態メッセージ */
.notice, .empty {
  max-width:1200px; margin:0 auto 16px; padding:10px 12px;
  border-radius:10px; border:1px solid #ffd9d9; background:#fff0f0; color:#a33;
  font-size:13px;
}
.empty { border-color:#e5e7eb; background:#fafafa; color:#555; }
</style>

<div class="breadcrumb">ホーム &gt; 商品一覧</div>

<?php if ($notice): ?>
  <div class="notice"><?= htmlspecialchars($notice, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<div class="list-header">
  <div class="list-title">
    商品一覧
    <?php if($sort==='recommend'): ?><span class="badge">おすすめ順</span><?php endif; ?>
    <?php if($sort==='ranking'):   ?><span class="badge">ランキング順</span><?php endif; ?>
  </div>
  <div class="sort-tabs" role="tablist" aria-label="並び替え">
    <a href="?sort=all"       class="<?= $sort==='all' ? 'active':'' ?>"      role="tab" aria-selected="<?= $sort==='all' ? 'true':'false' ?>">新着</a>
    <a href="?sort=recommend" class="<?= $sort==='recommend' ? 'active':'' ?>" role="tab" aria-selected="<?= $sort==='recommend' ? 'true':'false' ?>">おすすめ</a>
    <a href="?sort=ranking"   class="<?= $sort==='ranking' ? 'active':'' ?>"   role="tab" aria-selected="<?= $sort==='ranking' ? 'true':'false' ?>">ランキング</a>
  </div>
</div>

<div class="product-wrap">
  <?php if (empty($products)): ?>
    <div class="empty">条件に合致する商品がありません。条件を変更して再度お試しください。</div>
  <?php else: ?>
    <div class="product-grid">
      <?php foreach ($products as $p): ?>
        <a class="card" href="#">
          <img class="thumb" src="<?= htmlspecialchars($p['image'], ENT_QUOTES) ?>" alt="">
          <div class="body">
            <div class="name"><?= htmlspecialchars($p['name'], ENT_QUOTES) ?></div>
            <div class="price">¥<?= number_format((int)$p['price']) ?></div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
</main>

<?php require 'footer.php'; /* ← フッターはこの位置 */

