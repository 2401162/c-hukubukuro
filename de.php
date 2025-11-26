<?php require 'header.php'; ?>
<?php
const SERVER = 'mysql326.phy.lolipop.lan';
const DBNAME = 'LAA1607624-group';
const USER = 'LAA1607624';
const PASS = 'pass0726';

$connect = 'mysql:host=' . SERVER . ';dbname=' . DBNAME . ';charset=utf8';

try {
  $pdo = new PDO($connect, USER, PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]);
} catch (PDOException $e) {
  echo '接続エラー: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES);
  exit;
}

// おすすめ：4件だけ取得
$recommend_stmt = $pdo->query("
  SELECT p.*
  FROM recommended r
  JOIN product p ON r.product_id = p.product_id
  WHERE p.is_active = 1
  ORDER BY r.sort_order ASC
  LIMIT 4
");
$recommend = $recommend_stmt->fetchAll();

// ランキング：新着順で4件
$ranking_stmt = $pdo->query("
  SELECT * FROM product
  WHERE is_active = 1
  ORDER BY created_at DESC
  LIMIT 4
");
$ranking = $ranking_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>福袋EC トップ</title>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
  <style>
    .product-list {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 24px;
      padding: 20px 0;
    }

    .product {
      width: 220px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      overflow: hidden;
      text-align: center;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .product:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    .product img {
      width: 100%;
      height: 160px;
      object-fit: cover;
      display: block;
    }

    .product-name {
      font-size: 16px;
      font-weight: bold;
      margin: 12px 8px 4px;
      color: #333;
    }

    .product-price {
      font-size: 15px;
      color: #e60033;
      margin-bottom: 12px;
    }

    .cart-btn,
    .soldout {
      display: inline-block;
      padding: 8px 16px;
      font-size: 14px;
      border-radius: 20px;
      margin-bottom: 16px;
      transition: background-color 0.2s ease;
    }

    .cart-btn {
      background-color: #ff6600;
      color: #fff;
      border: none;
      cursor: pointer;
    }

    .cart-btn:hover {
      background-color: #e65c00;
    }

    .soldout {
      background-color: #ccc;
      color: #666;
      cursor: default;
    }

    .section-top {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: 0 20px;
    }

    .section-top h2 {
      font-size: 20px;
      margin: 0;
    }

    .list-link {
      font-size: 14px;
      color: #333;
      text-decoration: none;
    }

    .search-section {
      padding: 30px 20px;
      background-color: #f9f9f9;
    }

    .search-group {
      margin-bottom: 20px;
    }

    .tags {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 8px;
    }

    .tag {
      background-color: #eee;
      padding: 6px 12px;
      border-radius: 16px;
      text-decoration: none;
      color: #333;
      font-size: 14px;
    }

    .search-btn {
      display: block;
      margin: 0 auto;
      padding: 10px 24px;
      background-color: #e60033;
      color: #fff;
      border: none;
      border-radius: 24px;
      font-size: 16px;
      cursor: pointer;
    }

    .search-btn:hover {
      background-color: #cc002a;
    }
  </style>
</head>
<body>

  <div class="banner" role="img" aria-label="セールバナー"></div>

  <main>
    <?php
    function renderProductList($items) {
      foreach ($items as $item) {
        $image = !empty($item['image_path']) ? 'uploads/' . $item['image_path'] : 'img/noimage.png';
        $name = $item['name'] ?? '商品名不明';
        $price = $item['price'] ?? 0;
        $stock = $item['stock'] ?? 0;
        $id = $item['product_id'] ?? 0;
        ?>
        <div class="product">
          <img src="<?= htmlspecialchars($image, ENT_QUOTES) ?>" alt="<?= htmlspecialchars($name, ENT_QUOTES) ?>">
          <p class="product-name"><?= htmlspecialchars($name, ENT_QUOTES) ?></p>
          <p class="product-price">¥<?= number_format($price) ?></p>
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

    <!-- おすすめ -->
    <section class="section">
      <div class="product-section">
        <div class="section-top">
          <h2>おすすめ</h2>
          <a href="product-list.php" class="list-link">一覧へ <i class="fas fa-chevron-right small-icon" aria-hidden="true"></i></a>
        </div>
        <div class="product-list">
          <?php renderProductList($recommend); ?>
        </div>
      </div>
    </section>

    <!-- ランキング -->
    <section class="section">
      <div class="product-section">
        <div class="section-top">
          <h2>ランキング</h2>
          <a href="product-list.php" class="list-link">一覧へ <i class="fas fa-chevron-right small-icon" aria-hidden="true"></i></a>
        </div>
        <div class="product-list">
          <?php renderProductList($ranking); ?>
        </div>
      </div>
    </section>

    <!-- さがす -->
    <section class="search-section">
      <h2>さがす</h2>
      <div class="search-group">
        <p>ジャンル</p>
        <div class="tags">
          <div class="tag" data-q="お菓子">お菓子</div>
          <div class="tag" data-q="ゲーム">ゲーム福袋</div>
          <div class="tag" data-q="ぬいぐるみ">ぬいぐるみ</div>
        </div>
      </div>
      <div class="search-group">
        <p>価格</p>
        <div class="tags">
          <div class="tag" data-price="0-999">〜999</div>
          <div class="tag" data-price="1000-2000">1000〜2000</div>
          <div class="tag" data-price="2000-3000">2000〜3000</div>
          <div class="tag" data-price="3000-5000">3000〜5000</div>
        </div>
      </div>
      <button class="search-btn" type="button">さがす</button>
    </section>

    <script>
document.addEventListener('click', function(e) {
  const tag = e.target.closest('.tag');
  if (!tag) return;
  // ビルド検索パラメータ
  const params = new URLSearchParams();
  params.set('page', '1');
  params.set('per', '36');

  // q（キーワード）指定
  const q = tag.getAttribute('data-q');
  if (q) params.set('q', q);

  // 価格指定（min-max）
  const price = tag.getAttribute('data-price');
  if (price) {
    const parts = price.split('-');
    if (parts[0] !== '') params.set('price_min', parts[0]);
    if (parts[1] !== '') params.set('price_max', parts[1]);
  }

  // 遷移
  location.href = 'product-list.php?' + params.toString();
});
</script>
  </main>

</body>
</html>
<?php require 'footer.php'; ?>
