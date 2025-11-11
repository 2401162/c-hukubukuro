<?php
// セッション開始（未開始なら開始）
// 注意: 他ファイルで既に出力が始まっていると headers が送信済みになり
// session_start() が警告を出すため、安全に開始できるか確認してから呼ぶ。
if (session_status() === PHP_SESSION_NONE) {
  if (!headers_sent()) {
    session_start();
  } else {
    // ヘッダー送信後にセッションを開始できないためログに記録（警告は出さない）
    error_log('Session not started in header.php: headers already sent');
  }
}

// ✅ ログイン判定（あなたのセッションキーに合わせてあります）
$isLoggedIn = !empty($_SESSION['customer']);

// ✅ マイページの遷移先を決定
$myPageUrl = $isLoggedIn ? "/mypage.php" : "/rogin-input.php";
?>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
  /* ヘッダー全体 */
  .site-header {
    background: #ec4c4cff;
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
    background: #fff;
    border: 2px solid rgba(0,0,0,.08);
  }

  .site-header .site-title {
    color: #fff;
    font-weight: 700;
    letter-spacing: .04em;
  }

  /* 右アイコン */
  .site-header .actions {
    display: flex;
    align-items: center;
    gap: 22px;
  }

  .site-header .actions a {
    color: #fff;
    font-size: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    gap: 8px;
    transition: opacity .15s;
    height: 56px;
    text-decoration: none;
  }

  .site-header .actions a:hover { opacity: .85; }

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

  @media (max-width: 480px) {
    .site-header .site-title { display: none; }
  }

  .site-header .actions a .label-text { display: none; }
</style>

<header class="site-header">
  <!-- 左：ロゴ -->
  <div class="brand">
    <a href="index.php" aria-label="ホームへ">
      <?php
        // 相対パスで画像を指定（ホスティングがサブディレクトリでも動くように）
        $logoFile = 'ChatGPT Image 2025年11月5日 11_38_53.png';
        $logoPath = 'images/' . rawurlencode($logoFile);
      ?>
      <img src="<?= htmlspecialchars($logoPath, ENT_QUOTES, 'UTF-8') ?>" alt="サイトロゴ" class="site-logo">
    </a>
    <span class="site-title">福袋販売サイト</span>
  </div>

  <!-- 右アイコン -->
  <nav class="actions">

    <!-- ✅ マイページ（ログインしてない場合はログインページへ） -->
    <a href="<?= htmlspecialchars($myPageUrl, ENT_QUOTES, 'UTF-8') ?>" aria-label="マイページ">
      <i class="fa-solid fa-user" aria-hidden="true"></i>
    </a>

    <!-- カート -->
    <a href="/cart.php" aria-label="カート">
      <i class="fa-solid fa-cart-shopping" aria-hidden="true"></i>
      <?php
        $cartCount = $_SESSION['cart_count'] ?? 0;
        if ($cartCount > 0) {
          echo '<span class="cart-badge">'.$cartCount.'</span>';
        }
      ?>
    </a>
  </nav>
</header>

