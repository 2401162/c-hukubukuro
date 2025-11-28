<?php require 'header.php' ?>
<?php require_once __DIR__ . '/db-connect.php';

try {
  $pdo = new PDO($connect, USER, PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]);
} catch (PDOException $e) {
  echo '<p>DB接続エラー</p>';
  exit;
}

// おすすめ：4件だけ取得
$recommend_stmt = $pdo->prepare("
  SELECT p.*
  FROM recommended r
  JOIN product p ON r.product_id = p.product_id
  WHERE p.is_active = 1
  ORDER BY r.sort_order ASC
  LIMIT 4
");
$recommend_stmt->execute();
$recommend = $recommend_stmt->fetchAll();

// ランキング：新着順で4件
$ranking_stmt = $pdo->prepare("
  SELECT * FROM product
  WHERE is_active = 1
  ORDER BY created_at DESC
  LIMIT 4
");
$ranking_stmt->execute();
$ranking = $ranking_stmt->fetchAll();

// 画像パスを決めるヘルパー
function resolve_image_path(array $item): string {
  $img = $item['image_path'] ?? '';
  if (!$img) return 'img/noimage.png';
  // 絶対URLまたは先頭が / の場合はそのまま
  if (preg_match('#^(https?://|//|/)#i', $img)) return $img;
  // 既に uploads/ が含まれる場合はそのまま
  if (strpos($img, 'uploads/') === 0) return $img;
  return 'uploads/' . ltrim($img, '/');
}

// 出力関数（リンクを product-detail.php に接続）
function renderProductList($items) {
  foreach ($items as $item) {
    $image = htmlspecialchars(resolve_image_path($item), ENT_QUOTES, 'UTF-8');
    $name = htmlspecialchars($item['name'] ?? '商品名不明', ENT_QUOTES, 'UTF-8');
    $price = isset($item['price']) ? number_format((int)$item['price']) : '-';
    $stock = isset($item['stock']) ? (int)$item['stock'] : 0;
    $id = (int)($item['product_id'] ?? 0);
    ?>
    <div class="product">
      <a href="syouhin/syouhin_page.php?id=<?= $id ?>" style="display:block">
        <img src="<?= $image ?>" alt="<?= $name ?>">
      </a>
      <p class="product-name"><a href="syouhin/syouhin_page.php?id=<?= $id ?>"><?= $name ?></a></p>
      <p class="product-price">¥<?= $price ?></p>
      <?php if ($stock > 0): ?>
        <button class="cart-btn" data-id="<?= $id ?>">カートに追加</button>
      <?php else: ?>
        <span class="soldout">売り切れ</span>
      <?php endif; ?>
    </div>
    <?php
  }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>福袋EC トップ</title>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
  <style>
    .product-list { display:flex; flex-wrap:wrap; justify-content:center; gap:24px; padding:20px 0; }
    .product { width:220px; background:#fff; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.08); overflow:hidden; text-align:center; transition:transform .2s; }
    .product:hover{ transform:translateY(-4px); }
    .product img{ width:100%; height:160px; object-fit:cover; display:block; }
    .product-name{ font-size:16px; font-weight:700; margin:12px 8px 4px; color:#333; }
    .product-price{ font-size:15px; color:#e60033; margin-bottom:12px; }
    .cart-btn, .soldout { display:inline-block; padding:8px 16px; font-size:14px; border-radius:20px; margin-bottom:16px; }
    .cart-btn{ background:#ff6600; color:#fff; border:none; cursor:pointer; }
    .cart-btn:hover{ background:#e65c00; }
    .soldout{ background:#ccc; color:#666; }
    .section-top{ display:flex; justify-content:space-between; align-items:center; margin:0 20px; }
    .section-top h2{ font-size:20px; margin:0; }
  </style>
</head>
<body>

  

  <div class="banner" role="img" aria-label="セールバナー"></div>

  <main>
    <section class="section">
      <div class="product-section">
        <div class="section-top">
          <h2>おすすめ</h2>
          <a href="product-list.php" class="list-link">一覧へ</a>
        </div>
        <div class="product-list">
          <?php renderProductList($recommend); ?>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="product-section">
        <div class="section-top">
          <h2>ランキング</h2>
          <a href="product-list.php" class="list-link">一覧へ</a>
        </div>
        <div class="product-list">
          <?php renderProductList($ranking); ?>
        </div>
      </div>
    </section>
  </main>

<?php require 'footer.php'; ?>
</body>
<<<<<<< HEAD
</html>
=======
</html>

>>>>>>> 8b65446df7a4c54c8a0ceadd914236ea4d55a169
