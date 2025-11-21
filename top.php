<?php require 'header.php' ?>
<?php
$recommend = [
  ["name" => "お菓子福袋", "price" => 1200, "img" => "img/snack1.jpg"],
  ["name" => "ゲーム福袋", "price" => 2500, "img" => "img/game1.jpg"],
  ["name" => "ぬいぐるみ福袋", "price" => 1800, "img" => "img/toy1.jpg"],
  ["name" => "文房具福袋", "price" => 900, "img" => "img/stationery1.jpg"]
];

$ranking = [
  ["name" => "人気お菓子福袋", "price" => 1300, "img" => "img/snack2.jpg"],
  ["name" => "ゲームグッズ福袋", "price" => 2700, "img" => "img/game2.jpg"],
  ["name" => "ぬいぐるみセット", "price" => 1900, "img" => "img/toy2.jpg"],
  ["name" => "豪華文房具福袋", "price" => 1100, "img" => "img/stationery2.jpg"]
];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>福袋EC トップ</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
</head>
<body>

  <header>
    <div class="logo">福袋EC</div>
    <div class="icons">
      <a href="cart.php" aria-label="カート"><i class="fas fa-shopping-cart"></i></a>
      <a href="mypage.php" aria-label="マイページ"><i class="fas fa-user"></i></a>
    </div>
  </header>

  <div class="banner" role="img" aria-label="セールバナー"></div>

  <main>
    <!-- おすすめ -->
    <section class="section">
      <div class="product-section">
        <div class="section-top">
          <h2>おすすめ</h2>
          <a href="product-list.php" class="list-link">一覧へ <i class="fas fa-chevron-right small-icon" aria-hidden="true"></i></a>
        </div>

        <div class="product-list">
          <?php foreach($recommend as $item): ?>
            <div class="product">
              <img src="<?= htmlspecialchars($item['img'], ENT_QUOTES) ?>" alt="<?= htmlspecialchars($item['name'], ENT_QUOTES) ?>">
              <p class="product-name"><?= htmlspecialchars($item['name'], ENT_QUOTES) ?></p>
              <p class="product-price">¥<?= number_format($item['price']) ?></p>
              <button class="cart-btn" type="button">カートに追加</button>
            </div>
          <?php endforeach; ?>
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
          <?php foreach($ranking as $item): ?>
            <div class="product">
              <img src="<?= htmlspecialchars($item['img'], ENT_QUOTES) ?>" alt="<?= htmlspecialchars($item['name'], ENT_QUOTES) ?>">
              <p class="product-name"><?= htmlspecialchars($item['name'], ENT_QUOTES) ?></p>
              <p class="product-price">¥<?= number_format($item['price']) ?></p>
              <button class="cart-btn" type="button">カートに追加</button>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- さがす -->
    <section class="search-section">
      <h2>さがす</h2>

      <div class="search-group">
        <p>ジャンル</p>
        <div class="tags">
          <div class="tag">お菓子</div>
          <div class="tag">ゲーム福袋</div>
          <div class="tag">ぬいぐるみ</div>
        </div>
      </div>

      <div class="search-group">
        <p>価格</p>
        <div class="tags">
          <div class="tag">〜999</div>
          <div class="tag">1000〜2000</div>
          <div class="tag">2000〜3000</div>
          <div class="tag">3000〜5000</div>
        </div>
      </div>

      <button class="search-btn" type="button">さがす</button>
    </section>
  </main>

</body>
</html>
<?php require 'footer.php' ?>