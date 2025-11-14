<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログインページ</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="content">
                <div class="rogin">
                    <form method="post" action="rogin-output.php">
                        <p>メールアドレス</p>
                        <input type="email" name="email" size="40" maxlength="255" class="email" required>
                        <p>パスワード</p>
                        <input type="password" name="password" size="40" maxlength="255" class="password" required>
                        <p>
                        <input type="submit" value="rogin" class="button">
                    </form>
                </div>
        </p> 
        <div class="password-reset">
        <p><a href="password-reset-mail-input.php">パスワードを忘れた方はこちら</a></p>
        <p><a href="customer-newinput.php">新規会員登録はこちら</a></p>
        </div>
    </div>
 <style>
body {
    margin: 0;
    min-height: 100vh;
    display: flex;
    flex-direction: column; 
}
.header {
    display: flex;
    align-items: center;
    justify-content: center; 
    gap: 12px; 
    margin: 20px auto;
}
.content {
    text-align: center;
    margin: auto; 
    border: 1px solid #000;
    border-radius: 4px;
    width: 50%;
    padding: 40px 30px; 
    box-sizing: border-box;
    background-color: #fff;
}
.rogin {
    text-align: center;
    width: 100%;
}
.rogin p {
    text-align: left;
    width: 80%;
    margin: 14px auto 10px auto; 
}
.password, .email {
    margin-bottom: 25px;
    height: 40px;
    width: 80%;
    text-align: left;
}
.button {
    background-color: #000;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    width: 100%; 
    margin-top: 20px;
}
.password-reset {
    margin-top: 30px; 
}

</style>

</body>
</html>