<?php
// セッション開始（未開始なら開始）
if (session_status() === PHP_SESSION_NONE) {
  if (!headers_sent()) {
    session_start();
  } else {
    error_log('Session not started in header.php: headers already sent');
  }
}

// ✅ ログイン判定
$isLoggedIn = !empty($_SESSION['customer']);

// サブディレクトリを含めたパス（サーバ上の配置に合わせる）
$basePath = '/2025/Github/c-hukubukuro/';

$myPageUrl = $isLoggedIn ? $basePath . 'mypage/mypage.php' : $basePath . 'rogin-input.php';
?>

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
    color: #000000;
    font-size: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    gap: 8px;
    transition: opacity .15s;
    height: 56px;
    text-decoration: none;
  }

  .site-header .actions a svg {
    width: 36px;
    height: 36px;
    display: block;
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

  /* ✅ ドロップダウンメニュー */
  .user-menu {
    position: relative;
  }

  .user-menu-toggle {
    cursor: pointer;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 40px;
    transition: opacity .15s;
  }

  .user-menu-toggle:hover {
    opacity: .85;
  }

  .user-menu-toggle svg {
    width: 36px;
    height: 36px;
    display: block;
  }

  .user-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    background: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,.12);
    min-width: 110px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-8px);
    transition: all .2s ease;
    margin-top: 4px;
  }

  .user-menu:hover .user-dropdown,
  .user-menu-toggle:focus + .user-dropdown,
  .user-dropdown:hover {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
  }

  .user-dropdown a {
    display: block;
    padding: 2px;
    color: #000;
    text-decoration: none;
    font-size: 12px;
    font-weight: 400;
    transition: background .15s;
    text-align: center;
  }

  .user-dropdown a:last-child {
    border-top: 1px solid #eee;
  }

  .user-dropdown a:hover {
    background: #a8a8a8ff;
    opacity: 1;
  }

  @media (max-width: 480px) {
    .site-header .site-title { display: none; }
  }
</style>

<header class="site-header">
  <!-- 左：ロゴ -->
  <div class="brand">
    <a href="<?= $basePath ?>top.php" aria-label="ホームへ">
      <?php
        $logoFile = 'ChatGPT Image 2025年11月5日 11_38_53.png';
        $logoPath = $basePath . 'image/' . rawurlencode($logoFile);
      ?>
      <img src="<?= htmlspecialchars($logoPath, ENT_QUOTES, 'UTF-8') ?>" alt="サイトロゴ" class="site-logo">
    </a>
    <span class="site-title">福袋販売サイト</span>
  </div>

  <!-- 右アイコン -->
  <nav class="actions">

    <!-- ✅ ユーザーメニュー（ドロップダウン） -->
    <?php if ($isLoggedIn): ?>
      <!-- ログイン済み：ドロップダウンメニュー -->
      <div class="user-menu">
        <div class="user-menu-toggle" tabindex="0" aria-label="ユーザーメニュー">
          <svg width="36" height="36" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
            <path d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5z" fill="#fff"/>
            <path d="M4 20c0-4.418 3.582-8 8-8s8 3.582 8 8v1H4v-1z" fill="#fff"/>
          </svg>
        </div>
        <div class="user-dropdown">
          <a href="<?= htmlspecialchars($basePath . 'mypage/mypage.php', ENT_QUOTES, 'UTF-8') ?>">
            マイページ
          </a>
          <a href="<?= htmlspecialchars($basePath . 'rogout-input.php', ENT_QUOTES, 'UTF-8') ?>">
            ログアウト
          </a>
        </div>
      </div>
    <?php else: ?>
      <!-- 未ログイン：ログインページへ直接リンク -->
      <a href="<?= htmlspecialchars($basePath . 'rogin-input.php', ENT_QUOTES, 'UTF-8') ?>" aria-label="ログイン">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
          <path d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5z" fill="#ffffffff"/>
          <path d="M4 20c0-4.418 3.582-8 8-8s8 3.582 8 8v1H4v-1z" fill="#fff"/>
        </svg>
      </a>
    <?php endif; ?>

    <!-- カート -->
    <a href="<?= $basePath ?>cart.php" aria-label="カート">
      <svg width="36" height="36" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
        <path d="M7 4h-2l-1 2v2h2l3.6 7.59-1.35 2.45C8.89 18.76 9.5 20 11 20h8v-2h-7.42c-.14 0-.25-.11-.25-.25l.03-.12L12.1 15h5.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49-.02-.02c.07-.14.08-.3.08-.46 0-.55-.45-1-1-1H6.21l-.94-2H1v2h2l3.6 7.59L6.5 16h12.02" fill="#fff"/>
      </svg>
      <?php
        $cartCount = $_SESSION['cart_count'] ?? 0;
        if ($cartCount > 0) {
          echo '<span class="cart-badge">'.$cartCount.'</span>';
        }
      ?>
    </a>
  </nav>
</header>
