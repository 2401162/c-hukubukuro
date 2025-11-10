<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
// 将来ここで DB に新しいパスワードを更新する処理
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>パスワード再設定完了</title>
</head>
<body>

<?php include 'header.php'; ?>

<div class="content">

    <h2>パスワードの再設定完了</h2>

    <div class="icon">
        ✅
    </div>

    <a href="rogin-input.php" class="btn-red">ログイン画面</a>

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
.icon {
    font-size:80px;
    color:#ff7c7c;
    margin:25px 0;
}
.btn-red {
    display:inline-block;
    padding:12px 20px;
    background:#ff3c3c;
    color:#fff;
    text-decoration:none;
    border-radius:6px;
    font-size:16px;
}
</style>

</body>
</html>
