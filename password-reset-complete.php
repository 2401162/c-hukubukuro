<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'db-connect.php';

$error_message = '';
$is_success = false;

// POST リクエスト時の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $email = trim($_POST['email'] ?? '');
    
    // バリデーション
    if (empty($password) || empty($password2) || empty($email)) {
        $error_message = 'すべてのフィールドを入力してください。';
    } elseif ($password !== $password2) {
        $error_message = 'パスワードが一致しません。';
    } elseif (strlen($password) < 8) {
        $error_message = 'パスワードは8文字以上で設定してください。';
    } else {
        try {
            $pdo = new PDO(
                $connect,
                USER,
                PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
            
            // customer テーブル: password_hash, updated_at を更新
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            
            $stmt = $pdo->prepare(
                "UPDATE customer 
                 SET password_hash = :password_hash, updated_at = CURRENT_TIMESTAMP 
                 WHERE email = :email AND is_active = 1"
            );
            
            $stmt->execute([
                ':password_hash' => $password_hash,
                ':email' => $email,
            ]);
            
            if ($stmt->rowCount() > 0) {
                $is_success = true;
                // セッションをクリア
                unset($_SESSION['password_reset_token']);
                unset($_SESSION['password_reset_email']);
                unset($_SESSION['password_reset_expires']);
            } else {
                $error_message = 'メールアドレスが見つかりません。';
            }
        } catch (PDOException $e) {
            error_log("Password Reset Complete DB Error: " . $e->getMessage());
            $error_message = 'データベースエラーが発生しました。';
        }
    }
}

function h($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
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

    <h2>パスワードの再設定</h2>

    <?php if ($is_success): ?>
        <div class="icon">✅</div>
        <p style="color: #28a745; font-weight: 600;">パスワードが正常に更新されました。</p>
        <a href="rogin-input.php" class="btn-red">ログイン画面へ</a>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="error" style="color: #c33; margin: 16px 0;">
            <?= h($error_message) ?>
        </div>
        <p style="margin-top: 16px;">
            <a href="password-reset-mail-input.php">パスワード再設定を最初からやり直す</a>
        </p>
    <?php else: ?>
        <p>フォームを送信してください。</p>
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
a { color: #e43131; }
@media (max-width: 640px) {
    .content { width: 90%; padding: 30px 20px; }
}
</style>

</body>
</html>
