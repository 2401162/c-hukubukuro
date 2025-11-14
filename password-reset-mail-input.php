<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
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
        ご登録済みのメールアドレスをご入力ください。<br>
        パスワードの再設定を行うためのメールをお送りします。
    </p>

    <form action="password-reset-mail-sent.php" method="post" class="reset-form">
        <p>メールアドレス</p>
        <input type="email" name="email" class="text-input" required>

        <input type="submit" value="メールを送信する" class="btn-black">
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
h2 { margin-top:0; }
.note { color:#555; font-size:14px; margin-bottom:30px; line-height:1.6; }
.text-input {
    width:80%; height:40px; margin-bottom:20px;
    font-size:16px; padding:6px;
}
.btn-black {
    width:100%; padding:12px;
    background:#000; color:#fff; border:none; cursor:pointer;
    border-radius:4px; font-size:16px;
}
</style>

</body>
</html>
