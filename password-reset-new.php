<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
// 将来ここでトークンをチェックする（今はスキップ）
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

    <form action="password-reset-complete.php" method="post" class="reset-form">
        <p>新しいパスワード</p>
        <input type="password" name="password" class="text-input" required>

        <p>新しいパスワード（確認用）</p>
        <input type="password" name="password2" class="text-input" required>

        <input type="submit" value="完了" class="btn-red">
    </form>
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
    padding:6px; font-size:16px;
}
.btn-red {
    width:100%; padding:12px;
    background:#ff3c3c; color:#fff;
    border:none; border-radius:4px;
    cursor:pointer; font-size:16px;
}
</style>

</body>
</html>
