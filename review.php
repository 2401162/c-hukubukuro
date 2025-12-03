<?php
session_start();
require_once __DIR__ . '/db-connect.php'; // DB接続情報

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
// order_item_id 取得（URL）
// 例：review.php?order_item_id=123
// ==============================
$order_item_id = isset($_GET['order_item_id']) ? (int)$_GET['order_item_id'] : 0;
if ($order_item_id === 0) {
    exit("不正なアクセス：order_item_id がありません");
}

// ==============================
// 注文明細と商品情報取得
// ==============================
$sql = "SELECT 
          oi.order_item_id,
          p.product_id,
          p.name,
          p.image_path,
          p.description
        FROM order_item oi
        JOIN product p ON oi.product_id = p.product_id
        WHERE oi.order_item_id = :oid";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':oid', $order_item_id, PDO::PARAM_INT);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    exit('この注文詳細は存在しません。');
}

// 画像パス
$image_src = htmlspecialchars($order['image_path'], ENT_QUOTES, 'UTF-8');

// ==============================
// レビュー投稿処理
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
            // 既存レビューがあれば削除
            $sql = "DELETE FROM review WHERE order_item_id = :oid";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':oid', $order_item_id, PDO::PARAM_INT);
            $stmt->execute();

            // 新しいレビューを挿入
            $sql = "INSERT INTO review (order_item_id, rating, comment, is_active, created_at, updated_at)
                    VALUES (:oid, :rating, :comment, 1, NOW(), NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':oid', $order_item_id, PDO::PARAM_INT);
            $stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
            $stmt->bindValue(':comment', $comment, PDO::PARAM_STR);
            $stmt->execute();

            $posted = true;
            $error = 'レビューを更新しました。';
        } catch (PDOException $e) {
            $error = 'レビューの登録に失敗しました：' . $e->getMessage();
        }
    }
}

// ==============================
// レビュー一覧取得（この注文商品のレビューだけ）
// ==============================
$sql = "SELECT r.review_id, r.order_item_id, r.rating, r.comment, r.is_active, r.created_at
        FROM review r
        JOIN order_item oi ON r.order_item_id = oi.order_item_id
        WHERE oi.product_id = :pid AND r.is_active = 1
        ORDER BY r.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':pid', $order['product_id'], PDO::PARAM_INT);
$stmt->execute();
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/header.php';
?>

<style>
/* ここは既存のCSSをそのまま使用 */
.review-wrapper {max-width: 900px; margin: 40px auto 80px; font-family: "Noto Sans JP", sans-serif; }
.review-main { display: flex; gap: 60px; align-items: flex-start; }
.review-image-box { width: 260px; height: 260px; background: #6f6666; color: #fff; display: flex; justify-content: center; align-items: center; font-size: 18px; }
.review-image-box img { width: 100%; height: 100%; object-fit: cover; }
.review-right { flex: 1; }
.review-question { font-size: 18px; margin-bottom: 20px; }
.product-name { font-size: 20px; font-weight: bold; margin-bottom: 10px; }
.star-rating { font-size: 32px; color: #ffcc00; cursor: pointer; }
.star-rating span { margin-right: 5px; }
.review-label { margin-top: 40px; margin-bottom: 10px; font-size: 16px; }
.review-textarea { width: 100%; height: 220px; border: 1px solid #ccc; padding: 10px; font-size: 14px; resize: vertical; box-sizing: border-box; }
.review-submit-area { text-align: right; margin-top: 30px; }
.review-submit-btn { background: #ffcc00; border: none; padding: 10px 50px; border-radius: 3px; font-size: 16px; cursor: pointer; }
.review-message { margin-bottom: 20px; padding: 10px 15px; background: #e6ffe6; border: 1px solid #8fd88f; border-radius: 4px; font-size: 14px; }
.review-error { margin-bottom: 20px; padding: 10px 15px; background: #ffe6e6; border: 1px solid #ff8f8f; border-radius: 4px; font-size: 14px; }
.review-list { margin-top: 40px; }
.review-list h3 { margin-bottom: 10px; }
.review-item { border-top: 1px solid #ddd; padding: 15px 0; }
.review-item:first-child { border-top: none; }
.review-item-rating { color: #ffcc00; margin-bottom: 5px; font-size: 18px; }
.review-item-date { font-size: 12px; color: #666; margin-bottom: 5px; }
.review-item-comment { white-space: pre-wrap; font-size: 14px; }
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

  <form action="review.php?order_item_id=<?= $order_item_id ?>" method="post">
    <div class="review-main">
      <div class="review-image-box">
        <?php if (!empty($order['image_path'])): ?>
          <img src="<?= $image_src ?>" alt="商品画像">
        <?php else: ?>
          商品画像
        <?php endif; ?>
      </div>

      <div class="review-right">
        <div class="product-name">
          <?= htmlspecialchars($order['name'], ENT_QUOTES, 'UTF-8'); ?>
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

    <div class="review-label">レビューを書く</div>
    <textarea name="comment" class="review-textarea"></textarea>

    <div class="review-submit-area">
      <button type="submit" class="review-submit-btn">投稿</button>
    </div>
  </form>

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
const stars = document.querySelectorAll('#star-rating span');
const ratingInput = document.getElementById('rating-value');

stars.forEach(star => {
  star.addEventListener('click', () => {
    const value = Number(star.dataset.value);
    ratingInput.value = value;
    stars.forEach(s => s.textContent = Number(s.dataset.value) <= value ? '★' : '☆');
  });
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
