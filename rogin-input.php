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
    <div class="content">
                <div class="rogin">
                    <form method="post" action="rogin-output.php">
                        <p>メールアドレス</p>
                        <input type="email" name="email" size="40" maxlength="255" class="email" required>
                        <p>パスワード</p>
                        <input type="password" name="password" size="40" maxlength="255" class="password" required>
                        <p>
                        <input type="submit" value="ログイン" class="button">
                    </form>
                </div>
        </p> 
        <div class="password-reset">
        <p><a href="rogin-mail-input.php">パスワードを忘れた方はこちら</a></p>
        <p><a href="new-member-input.php">新規会員登録はこちら</a></p>
        </div>
    </div>
    <style>
    .header {
        display: flex;
        align-items: center;
        justify-content: center; 
        gap: 12px; 
        margin: 20px auto;
    }
    .header h1 {
        margin: 0;
        font-size: 1.6rem;
    }
    .header img {
        width: 60px;
        height: auto;
        display: block;
    }
    .content {
        text-align: center;
        margin: 0 auto 30px auto; 
        border-color: #000;
        border-style: solid;
        border-width: 1px;
        border-radius: 4px;
        width: 50%;
        padding: 20px;
        box-sizing: border-box;
    }
    .rogin {
        text-align: center;
        width: 100%;
    }
    
    .rogin p {
        text-align: left;
        width: 80%;
        margin: 8px auto 6px auto; 
    }
    .button {
        background-color: #000;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        width: 102%;   
    }
    .password, .email {
        margin-bottom: 15px;
        height: 40px;
        width: 80%;
        text-align: left;
    }
    </style>
</body>
</html>