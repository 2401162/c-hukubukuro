<?php
// 簡易ログイン処理（デモ）：実運用ではデータベースとパスワードハッシュ照合を行ってください。
session_start();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン結果</title>
</head>
<body>
    <div class="header">
        <h1>福袋販売サイト</h1>
        <img src="images/logo.png" alt="サイトロゴ">
    </div>
    <div class="content">
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    $errors = [];
    if ($email === '' || $password === '') {
        $errors[] = 'メールアドレスとパスワードを入力してください。';
    }
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'メールアドレスの形式が正しくありません。';
    }

    if (!empty($errors)) {
        echo "<h2>ログインに失敗しました</h2>";
        echo "<ul style='color:#c00;'>";
        foreach ($errors as $err) echo '<li>' . htmlspecialchars($err, ENT_QUOTES, 'UTF-8') . '</li>';
        echo "</ul>";
        echo '<p><a href="rogin-input.php">ログイン画面へ戻る</a></p>';
    } else {
        // デモ用の簡易チェック。実運用の認証は DB と password_verify を使うこと。
        $demo_email = 'demo@example.com';
        $demo_password = 'demo123';

        if ($email === $demo_email && $password === $demo_password) {
            // 認証成功
            $_SESSION['user_email'] = $email;
            echo "<h2>ログイン成功</h2>";
            echo "<p>ようこそ: " . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "</p>";
            echo '<p><a href="index.html">トップへ</a></p>';
        } else {
            echo "<h2>ログイン失敗</h2>";
            echo "<p>メールアドレスかパスワードが正しくありません。</p>";
            echo '<p><a href="rogin-input.php">ログイン画面へ戻る</a></p>';
            echo '<p>デモアカウント: demo@example.com / demo123</p>';
        }
    }
} else {
    echo "<h2>不正なアクセス</h2>";
    echo '<p><a href="rogin-input.php">ログインページへ</a></p>';
}
?>
    </div>
    <style>
    .header { display:flex; align-items:center; justify-content:center; gap:12px; margin:20px auto; }
    .content { text-align:center; margin:0 auto; width:60%; border:1px solid #000; padding:20px; border-radius:6px; }
    </style>
</body>
</html>
