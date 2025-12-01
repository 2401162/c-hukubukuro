<?php
session_start();
require_once __DIR__ . 'db-connect.php'; // DB接続情報

// ==============================
// DB接続
// ==============================
try {
    $pdo = new PDO($connect, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit('DB接続エラー: ' . $e->getMessage());
}

// ==============================
// 商品ID取得（URL）
// 例：review.php?product_id=1
// ==============================
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 1;

// ==============================
// 商品情報取得（product テーブル）
// ==============================
$sql = "SELECT product_id, jenre_id, name, price, stock, description, is_active, created_at, image_path
        FROM product
        WHERE product_id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $product_id, PDO::PARAM_INT);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    exit('指定された商品が見つかりませんでした。');
}

// 画像パス（image_path をそのまま使う想定）
$image_src = htmlspecialchars($product['image_path'], ENT_QUOTES, 'UTF-8');

// ==============================
// レビュー投稿処理（review テーブルに INSERT）
// ==============================
$posted = false;
$error  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating  = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

    if ($rating < 1 || $rating > 5) {
        $error = '星を選択してください。';
    } else {
        try {
            // order_item_id は、今は未連携なので NULL を入れておく
            $sql = "INSERT INTO review (order_item_id, rating, comment, is_active, created_at, updated_at)
                    VALUES (:order_item_id, :rating, :comment, 1, NOW(), NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':order_item_id', null, PDO::PARAM_NULL); // 注文と紐付けるならここを変更
            $stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
            $stmt->bindValue(':comment', $comment, PDO::PARAM_STR);
            $stmt->execute();
            $posted = true;
        } catch (PDOException $e) {
            $error = 'レビューの登録に失敗しました：' . $e->getMessage();
        }
    }
}

// ==============================
// レビュー一覧取得
// ※ このテーブルには product_id がないので、
//    今は「この商品に対するレビュー」という絞り込みができない。
//    ひとまず is_active=1 の全レビューを新しい順で表示する形。
//    → 後で review テーブルに product_id カラムを追加すると綺麗に紐付け可能。
// ==============================
$sql = "SELECT review_id, order_item_id, rating, comment, is_active, created_at, updated_at
        FROM review
        WHERE is_active = 1
        ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . 'header.php';
?>

<style>
.review-wrapper {
  max-width: 900px;
  margin: 40px auto 80px;
  font-family: "Noto Sans JP", sans-serif;
}

.review-main {
  display: flex;
  gap: 60px;
  align-items: flex-start;
}

.review-image-box {
  width: 260px;
  height: 260px;
  background: #6f6666;
  color: #fff;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 18px;
}

.review-image-box img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.review-right {
  flex: 1;
}

.review-question {
  font-size: 18px;
  margin-bottom: 20px;
}

.product-name {
  font-size: 20px;
  font-weight: bold;
  margin-bottom: 10px;
}

/* 星評価 */
.star-rating {
  font-size: 32px;
  color: #ffcc00;
  cursor: pointer;
}

.star-rating span {
  margin-right: 5px;
}

/* レビュー入力 */
.review-label {
  margin-top: 40px;
  margin-bottom: 10px;
  font-size: 16px;
}

.review-textarea {
  width: 100%;
  height: 220px;
  border: 1px solid #ccc;
  padding: 10px;
  font-size: 14px;
  resize: vertical;
  box-sizing: border-box;
}

/* 送信ボタン */
.review-submit-area {
  text-align: right;
  margin-top: 30px;
}

.review-submit-btn {
  background: #ffcc00;
  border: none;
  padding: 10px 50px;
  border-radius: 3px;
  font-size: 16px;
  cursor: pointer;
}

/* メッセージ */
.review-message {
  margin-bottom: 20px;
  padding: 10px 15px;
  background: #e6ffe6;
  border: 1px solid #8fd88f;
  border-radius: 4px;
  font-size: 14px;
}

.review-error {
  margin-bottom: 20px;
  padding: 10px 15px;
  background: #ffe6e6;
  border: 1px solid #ff8f8f;
  border-radius: 4px;
  font-size: 14px;
}

/* レビュー一覧 */
.review-list {
  margin-top: 40px;
}

.review-list h3 {
  margin-bottom: 10px;
}

.review-item {
  border-top: 1px solid #ddd;
  padding: 15px 0;
}

.review-item:first-child {
  border-top: none;
}

.review-item-rating {
  color: #ffcc00;
  margin-bottom: 5px;
  font-size: 18px;
}

.review-item-date {
  font-size: 12px;
  color: #666;
  margin-bottom: 5px;
}

.review-item-comment {
  white-space: pre-wrap;
  font-size: 14px;
}
</style>

<div class="review-wrapper">

  <?php if ($posted): ?>
    <div class="review-message">
      レビューを投稿しました。ありがとうございます。
    </div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="review-error">
      <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
    </div>
  <?php endif; ?>

  <form action="review.php?product_id=<?= htmlspecialchars($product_id, ENT_QUOTES, 'UTF-8') ?>" method="post">
    <div class="review-main">
      <!-- 左：商品画像 -->
      <div class="review-image-box">
        <?php if (!empty($product['image_path'])): ?>
          <img src="<?= $image_src ?>" alt="商品画像">
        <?php else: ?>
          商品画像
        <?php endif; ?>
      </div>

      <!-- 右：商品名＆星評価 -->
      <div class="review-right">
        <div class="product-name">
          <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>
        </div>

        <div class="review-question">商品はいかがでしたか？</div>

        <div class="star-rating" id="star-rating">
          <span data-value="1">☆</span>
          <span data-value="2">☆</span>
          <span data-value="3">☆</span>
          <span data-value="4">☆</span>
          <span data-value="5">☆</span>
        </div>

        <input type="hidden" name="rating" id="rating-value" value="0">
      </div>
    </div>

    <!-- レビュー本文 -->
    <div class="review-label">レビューを書く</div>
    <textarea name="comment" class="review-textarea"></textarea>

    <!-- 投稿ボタン -->
    <div class="review-submit-area">
      <button type="submit" class="review-submit-btn">投稿</button>
    </div>
  </form>

  <!-- レビュー一覧 -->
  <div class="review-list">
    <h3>みんなのレビュー</h3>

    <?php if (empty($reviews)): ?>
      <p>まだレビューはありません。</p>
    <?php else: ?>
      <?php foreach ($reviews as $r): ?>
        <div class="review-item">
          <div class="review-item-rating">
            <?php
              $stars = str_repeat('★', (int)$r['rating']) . str_repeat('☆', 5 - (int)$r['rating']);
              echo $stars;
            ?>
          </div>
          <div class="review-item-date">
            <?= htmlspecialchars($r['created_at'], ENT_QUOTES, 'UTF-8'); ?>
          </div>
          <div class="review-item-comment">
            <?= nl2br(htmlspecialchars($r['comment'], ENT_QUOTES, 'UTF-8')); ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<script>
// 星クリックで評価セット
const stars = document.querySelectorAll('#star-rating span');
const ratingInput = document.getElementById('rating-value');

stars.forEach(star => {
  star.addEventListener('click', () => {
    const value = Number(star.dataset.value);
    ratingInput.value = value;

    stars.forEach(s => {
      if (Number(s.dataset.value) <= value) {
        s.textContent = '★';
      } else {
        s.textContent = '☆';
      }
    });
  });
});
</script>

<?php include __DIR__ . 'footer.php'; ?>
