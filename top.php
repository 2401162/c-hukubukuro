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
// トップに表示するジャンルを指定の順で取得（ゲーム, アニメ, お菓子, ぬいぐるみ, 文房具）
$genres = [];
$desired = ['ゲーム','アニメ','お菓子','ぬいぐるみ','文房具'];
try {
  $placeholders = implode(',', array_fill(0, count($desired), '?'));
  $sql = "SELECT genre_id AS id, genre_name AS name FROM genre WHERE genre_name IN ($placeholders) ORDER BY FIELD(genre_name, 'ゲーム','アニメ','お菓子','ぬいぐるみ','文房具')";
  $gstmt = $pdo->prepare($sql);
  $gstmt->execute($desired);
  $genres = $gstmt->fetchAll();
} catch (PDOException $e) {
  // 無視
}

// 画像パスを決めるヘルパー
function resolve_image_path(array $item): string {
  $img = $item['image_path'] ?? '';
  if (!$img) return 'img/noimage.png';
  if (preg_match('#^(https?://|//|/)#i', $img)) return $img;
  if (strpos($img, 'uploads/') === 0) return $img;
  return 'uploads/' . ltrim($img, '/');
}

// 商品カード描画
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
        <button class="cart-btn" data-id="<?= $id ?>" onclick="addToCart(<?= $id ?>)">カートに追加</button>
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
    /* 探す（トップ） */
    .search-section{max-width:1200px;margin:18px auto;padding:12px;display:flex;flex-direction:column;gap:10px}
    .search-title{display:flex;justify-content:space-between;align-items:center}
    .tag-group{display:flex;flex-wrap:wrap;gap:8px}
    .tag{display:inline-block;padding:8px 12px;border-radius:999px;border:1px solid #ddd;background:#f0f0f0;color:#666;cursor:pointer;font-size:14px}
    .tag.active{background:#ec4c4c;color:#fff;border-color:#ec4c4c}
    .search-btn{padding:10px 16px;border-radius:8px;background:#ec4c4c;color:#fff;border:none;cursor:pointer}
    .search-btn:hover{opacity:.95}
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
  
      <section class="section">
        <div class="search-section" aria-label="商品を探す">
          <div class="search-title">
            <h2>探す</h2>
            <a href="product-list.php" class="list-link">一覧へ</a>
          </div>
          <div>
            <div style="margin-bottom:6px;font-size:13px;color:#555">ジャンル（選択すると絞り込み）</div>
            <div class="tag-group" id="genreTags">
              <?php if (!empty($genres)): ?>
                <?php foreach($genres as $g): ?>
                  <button type="button" class="tag genre" data-id="<?= htmlspecialchars($g['id'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($g['name'], ENT_QUOTES, 'UTF-8') ?></button>
                <?php endforeach; ?>
              <?php else: ?>
                <!-- ジャンルが未登録の場合のプレースホルダ -->
                <span style="color:#999">ジャンルが登録されていません</span>
              <?php endif; ?>
            </div>
          </div>
          <div>
            <div style="margin-bottom:6px;font-size:13px;color:#555">価格で絞る</div>
            <div class="tag-group" id="priceTags">
              <button type="button" class="tag price" data-range="0-1000">～1,000円</button>
              <button type="button" class="tag price" data-range="1000-3000">1,000〜3,000円</button>
              <button type="button" class="tag price" data-range="3000-5000">3,000〜5,000円</button>
              <button type="button" class="tag price" data-range="5000-">5,000円〜</button>
            </div>
          </div>
          <div>
            <button id="searchBtn" class="search-btn" type="button">探す</button>
          </div>
        </div>
      </section>
  </main>

  <script>
    const CART_URL = <?php echo json_encode($basePath . 'cart.php'); ?>;
    function addToCart(productId) {
        // 数量は1固定（トップページでは数量選択なし）
        const quantity = 1;

      fetch(CART_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'action=add&product_id=' + encodeURIComponent(productId) + '&quantity=' + encodeURIComponent(quantity)
      })
      .then(res => {
        if (!res.ok) {
          throw new Error('HTTP ' + res.status);
        }
        return res.json().catch(() => { throw new Error('JSON parse error'); });
      })
      .then(data => {
        if (data && data.success) {
          alert('カートに追加しました');

          if (typeof updateCartBadge === 'function') {
            updateCartBadge(data.cart_count ?? 0);
          }

          if (confirm('カートを確認しますか？')) {
            window.location.href = CART_URL;
          }
        } else {
          alert((data && data.message) ? data.message : 'カートに追加できませんでした');
        }
      })
      .catch(error => {
        console.error('Add to cart error:', error);
        alert('カート追加でエラーが発生しました。画面を再読み込みしてお試しください。');
      });
    }

    // カートバッジ更新関数
    function updateCartBadge(count) {
        const badge = document.querySelector('.cart-badge');
        if (count > 0) {
            if (badge) {
                badge.textContent = count;
            } else {
                // バッジが存在しない場合は作成
                const cartLink = document.querySelector('a[href*="cart.php"]');
                if (cartLink) {
                    const newBadge = document.createElement('span');
                    newBadge.className = 'cart-badge';
                    newBadge.textContent = count;
                    cartLink.appendChild(newBadge);
                }
            }
        }
    }
  </script>

<?php require 'footer.php'; ?>
</body>
</html>
