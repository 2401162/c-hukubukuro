<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

// 戻り先（指定がなければ index.php）
$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'index.php';

// セッション情報をクリア（header.php の仕様に合わせて customer を削除）
$_SESSION['customer'] = null;
unset($_SESSION['customer']);

// ついでにカート数などもクリアしたい場合（任意）
// unset($_SESSION['cart_count']);

// 完全にセッションを破棄したい場合（任意）:
// $_SESSION = [];
// if (ini_get("session.use_cookies")) {
//   $params = session_get_cookie_params();
//   setcookie(session_name(), '', time()-42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
// }
// session_destroy();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ログアウトしました</title>
  <style>
    body { margin:0; font-family:"Noto Sans JP",sans-serif; background:#fff; }
    .content { max-width:420px; margin:40px auto; padding:28px 22px; border:1px solid #ddd; border-radius:8px; background:#fff; box-shadow:0 6px 18px rgba(0,0,0,.06); text-align:center; }
    .title { font-size:22px; font-weight:700; margin-bottom:12px; }
    .desc { color:#333; margin-bottom:18px; }
    .actions { display:flex; gap:12px; }
    .button { flex:1; padding:10px 18px; font-size:15px; border-radius:4px; cursor:pointer; text-decoration:none; display:inline-block; text-align:center; }
    .btn-top { background:#333; color:#fff; }
    .btn-login { background:#e43131; color:#fff; }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="content">
    <h1 class="title">ログアウトしました</h1>
    <p class="desc">ご利用ありがとうございました。</p>

    <div class="actions">
      <a class="button btn-top" href="<?= h($redirect) ?>">トップへ戻る</a>
      <a class="button btn-login" href="rogin-input.php">ログインページへ</a>
    </div>
  </div>
</body>
</html>

