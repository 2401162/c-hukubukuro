<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// トークンチェック（セッションから取得）
$token_hash = $_SESSION['password_reset_token'] ?? null;
$reset_email = $_SESSION['password_reset_email'] ?? null;
$expires_at = $_SESSION['password_reset_expires'] ?? null;

$is_valid = $token_hash && $reset_email && $expires_at && (time() < strtotime($expires_at));
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>パスワードの再設定</title>
</head>
<body>

<?php include 'header.php'; ?>

<div class="content">
    <h2>パスワードの再設定</h2>

    <?php if ($is_valid): ?>
        <form action="password-reset-complete.php" method="post" class="reset-form">
            <p>新しいパスワード</p>
            <input type="password" name="password" class="text-input" required minlength="8">

            <p>新しいパスワード（確認用）</p>
            <input type="password" name="password2" class="text-input" required minlength="8">

            <input type="hidden" name="email" value="<?= htmlspecialchars($reset_email, ENT_QUOTES, 'UTF-8') ?>">

            <input type="submit" value="完了" class="btn-red">
        </form>
    <?php else: ?>
        <p style="color: #c33; margin: 20px 0;">
            パスワードのリセットリンクが有効期限切れまたは無効です。<br>
            <a href="password-reset-mail-input.php">パスワード再設定を最初からやり直す</a>
        </p>
    <?php endif; ?>
</div>

<style>
body { margin:0; min-height:100vh; display:flex; flex-direction:column; }
.content {
    margin:auto;
    width:50%;
    background:#fff;
    border:1px solid #ccc;
    border-radius:6px;
    padding:40px 30px;
    text-align:center;
}
.text-input {
    width:80%; height:40px; margin-bottom:20px;
    padding:6px; font-size:16px; box-sizing: border-box;
}
.btn-red {
    width:100%; padding:12px;
    background:#ff3c3c; color:#fff;
    border:none; border-radius:4px;
    cursor:pointer; font-size:16px;
}
a { color: #e43131; }
@media (max-width: 640px) {
    .content { width: 90%; padding: 30px 20px; }
    .text-input { width: 100%; }
}
</style>

</body>
</html>
