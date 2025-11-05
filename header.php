<?php
// header.php
// 画面上部のヘッダー（ロゴ／ユーザー・カートアイコン）
// ※Bulma本体とFont Awesomeはレイアウト側（親のHTML）で読み込んでください。
//   例）
//   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
//   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<style>
  /* ヘッダー全体 */
  .site-header {
    background: #F37C7C;          /* スクショのようなピンク系 */
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

  /* 右：アイコン部分 */
  .site-header .actions {
    display: flex;
    align-items: center;
    gap: 18px;
  }
  .site-header .actions a {
    color: #fff;
    font-size: 20px;              /* アイコンの大きさ */
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: opacity .15s;
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
</style>

<header class="site-header">
  <!-- 左：ロゴ -->
  <div class="brand">
    <!-- ロゴ画像（例：/images/logo.png に丸い「福」アイコン等） -->
    <a href="/index.php" class="image is-48x48" aria-label="ホームへ">
      <img src="/images/ChatGPT Image 2025年11月5日 11_38_53.png" alt="サイトロゴ" class="site-logo">
    </a>
    <span class="site-title">福袋</span>
  </div>

  <!-- 右：人アイコン／カートアイコン（Bulma＋Font Awesome） -->
  <nav class="actions">
    <!-- マイページ or ログイン -->
    <a href="/mypage.php" aria-label="マイページ">
      <i class="fa-solid fa-user"></i>
      <span class="is-hidden-mobile">マイページ</span>
    </a>

    <!-- カート -->
    <a href="/cart.php" aria-label="カート">
      <i class="fa-solid fa-cart-shopping"></i>
      <?php
        // 必要ならセッションからカート点数を表示（なければ0）
        $cartCount = isset($_SESSION['cart_count']) ? (int)$_SESSION['cart_count'] : 0;
        if ($cartCount > 0) {
          echo '<span class="cart-badge" aria-label="カート内点数">'.$cartCount.'</span>';
        }
      ?>
      <span class="is-hidden-mobile">カート</span>
    </a>
  </nav>
</header>
