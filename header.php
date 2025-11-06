<?php


if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<!-- Font Awesome をここで明示的に読み込む（親が未読込でもアイコンが表示されるように） -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  /* ヘッダー全体 */
  .site-header {
    background: #ec4c4cff;          /* スクショのようなピンク系 */
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 12px;
    position: sticky;
    top: 0;
    z-index: 100;
  }
  /* 左：ロゴ部分 */
  .site-header .brand {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .site-header .site-logo {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    background: #fff;             /* 画像が透過でも白く見えるように */
    border: 2px solid rgba(0,0,0,.08);
  }
  .site-header .site-title {
    color: #fff;
    font-weight: 700;
    letter-spacing: .04em;
  }
.site-header .actions {
  display: flex;
  align-items: center;
  gap: 22px; /* ← 少し広めに間隔を確保 */
}
.site-header .actions a {
  color: #fff;
  font-size: 35px;        /* 大きさ維持 */
  display: flex;           /* inline-flex → flexに変更 */
  align-items: center;     /* 縦中央寄せ */
  justify-content: center; /* 横中央寄せ */
  line-height: 1;          /* ベースラインのズレ防止 */
  gap: 8px;
  transition: opacity .15s;
  height: 56px;            /* ヘッダーと同じ高さにして中央固定 */
}

.site-header .actions a:hover { opacity: .85; }

  /* カート個数のバッジ（必要なら） */
  .cart-badge {
    display: inline-block;
    min-width: 18px;
    padding: 0 5px;
    font-size: 11px;
    line-height: 18px;
    text-align: center;
    color: #F37C7C;
    background: #fff;
    border-radius: 10px;
    font-weight: 700;
  }

  /* スマホでタイトル非表示（スクショに合わせてシンプルに） */
  @media (max-width: 480px) {
    .site-header .site-title { display: none; }
  }

  /* アイコンのみ表示にする（テキストラベルを隠す） */
  .site-header .actions a .label-text { display: none; }
</style>

<header class="site-header">
  <!-- 左：ロゴ -->
  <div class="brand">
    <!-- ロゴ画像（例：/images/logo.png に丸い「福」アイコン等） -->
    <a href="/index.php" class="image is-48x48" aria-label="ホームへ">
      <!-- ファイル名に空白が含まれる場合があるため URL エンコードしたパスを使う -->
      <img src="/images/ChatGPT%20Image%202025年11月5日%2011_38_53.png" alt="サイトロゴ" class="site-logo">
    </a>
    <span class="site-title">福袋販売サイト</span>
  </div>

  <!-- 右：人アイコン／カートアイコン（Bulma＋Font Awesome） -->
  <nav class="actions">
    <!-- マイページ or ログイン -->
    <a href="/mypage.php" aria-label="マイページ">
      <i class="fa-solid fa-user" aria-hidden="true"></i>
      <span class="label-text">マイページ</span>
    </a>

    <!-- カート -->
    <a href="/cart.php" aria-label="カート">
      <i class="fa-solid fa-cart-shopping" aria-hidden="true"></i>
      <?php
        // 必要ならセッションからカート点数を表示（なければ0）
        $cartCount = isset($_SESSION['cart_count']) ? (int)$_SESSION['cart_count'] : 0;
        if ($cartCount > 0) {
          echo '<span class="cart-badge" aria-label="カート内点数">'.$cartCount.'</span>';
        }
      ?>
      <span class="label-text">カート</span>
    </a>
  </nav>
</header>
