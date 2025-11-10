<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$email = $_POST['email'] ?? '*****';
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

    <p class="note">
        <?= htmlspecialchars($email) ?> に新しいパスワードを設定するためのURLを送信しました。<br>
        ご確認の上、新しいパスワードに変更してください。
    </p>

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
.note { line-height:1.8; color:#444; }
</style>

</body>
</html>
