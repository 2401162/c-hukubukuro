<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

// ログアウト後の戻り先（未指定なら index.php）
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ログアウト確認</title>
  <style>
    body { margin:0; font-family:"Noto Sans JP",sans-serif; background:#fff; }
    .content {
      max-width: 420px; margin: 40px auto; padding: 28px 22px;
      border: 1px solid #ddd; border-radius: 8px; background:#fff;
      box-shadow: 0 6px 18px rgba(0,0,0,.06);
    }
    .title { font-size: 22px; font-weight: 700; text-align: center; margin-bottom: 18px; }
    .desc  { text-align: center; color:#333; margin-bottom: 18px; }

    /* ボタン並びを完全均等にする */
    .actions { display:flex; gap:16px; margin-top:20px; }
    .btn-form { flex:1; margin:0; }            /* ← 各ボタンの外側ボックスを均等化 */
    .button {
      width:100%; padding:12px 0; font-size:15px; border-radius:6px;
      cursor:pointer; border:none; display:block; text-align:center;
    }
    .btn-cancel { background:#333; color:#fff; }
    .btn-logout { background:#e43131; color:#fff; }

    /* モバイル時は縦並びに */
    @media (max-width: 420px){
      .actions { flex-direction: column; }
    }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="content">
    <h1 class="title">ログアウトしますか？</h1>
    <p class="desc">
      <?= !empty($_SESSION['customer']) ? '現在ログイン中です。ログアウトしてよろしいですか？' : 'ログインしていません。トップへ戻れます。' ?>
    </p>

    <div class="actions">
      <!-- キャンセル（GETで戻り先へ） -->
      <form action="<?= h($redirect) ?>" method="get" class="btn-form">
        <button type="submit" class="button btn-cancel">キャンセル</button>
      </form>

      <!-- ログアウト（POSTで処理へ） -->
      <form method="post" action="rogout-output.php" class="btn-form">
        <input type="hidden" name="redirect" value="<?= h($redirect) ?>">
        <button type="submit" class="button btn-logout">ログアウト</button>
      </form>
    </div>
  </div>
</body>
</html>

