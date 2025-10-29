<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログインページ</title>
</head>
<body>
    <div class="header">     
    <h1>福袋販売サイト</h1>
    <img src="images/logo.png" alt="サイトロゴ">
    </div>
    <div class="rogin">
        <p>メールアドレス</p>
        <input type="email" name="email" size="40" maxlength="255">
        <p>パスワード</p>
        <input type="password" name="password" size="40" maxlength="255">
        <p>
        <input type="submit" value="ログイン">
        </p> 
        <p><a href="rogin-mail-input.php">パスワードを忘れた方はこちら</a></p>
        <p><a href="new-member-input.php">新規会員登録はこちら</a></p>
    </div>
    <style>
    .header {
        text-align: left;
        margin-bottom: 30px;
    }
    .rogin {
        margin-left: 20px;
    }
    </style>
</body>
</html>