<?php
session_start();
// ここで必要なら商品IDをGETでもらう例：$product_id = $_GET['id'] ?? 1;
?>

<?php include __DIR__ . 'header.php'; ?>

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

.review-right {
  flex: 1;
}

.review-question {
  font-size: 18px;
  margin-bottom: 20px;
}

/* 星評価 */
.star-rating {
  font-size: 32px;
  color: #ffcc00; /* 枠だけに見えるフォントの星を使うイメージ */
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
</style>

<div class="review-wrapper">
  <form action="" method="post">
    <div class="review-main">
      <!-- 左：商品画像 -->
      <div class="review-image-box">
        商品画像
      </div>

      <!-- 右：星評価 -->
      <div class="review-right">
        <div class="review-question">商品はいかがでしたか？</div>

        <div class="star-rating" id="star-rating">
          <!-- data-value に1〜5をセット -->
          <span data-value="1">☆</span>
          <span data-value="2">☆</span>
          <span data-value="3">☆</span>
          <span data-value="4">☆</span>
          <span data-value="5">☆</span>
        </div>

        <!-- 選択された星の数を送信用に保持 -->
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
</div>

<script>
// 星クリックで評価をセット
const stars = document.querySelectorAll('#star-rating span');
const ratingInput = document.getElementById('rating-value');

stars.forEach(star => {
  star.addEventListener('click', () => {
    const value = Number(star.dataset.value);
    ratingInput.value = value;

    // 選択した星までを「塗りつぶし風」に
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
