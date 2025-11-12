<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'db-connect.php';

$email = trim($_POST['email'] ?? '');
$message = '';
$is_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($email)) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'メールアドレスの形式が正しくありません。';
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
            
            // customer テーブルからメールアドレスが存在するか確認
            $stmt = $pdo->prepare("SELECT customer_id FROM customer WHERE email = :email AND is_active = 1 LIMIT 1");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();
            
            if ($user) {
                // トークン生成（SHA256ハッシュ）
                $token = bin2hex(random_bytes(32));
                $token_hash = hash('sha256', $token);
                $expires_at = date('Y-m-d H:i:s', time() + 3600); // 1時間有効
                
                // トークンをDBに保存
                $pdo->beginTransaction();
                // password_resets テーブルがなければ作成
                $pdo->exec("CREATE TABLE IF NOT EXISTS password_resets (
                    id BIGINT PRIMARY KEY AUTO_INCREMENT,
                    customer_id BIGINT,
                    token_hash VARCHAR(255) NOT NULL,
                    expires_at DATETIME NOT NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

                $insert = $pdo->prepare("INSERT INTO password_resets (customer_id, token_hash, expires_at) VALUES (:customer_id, :token_hash, :expires_at)");
                $insert->execute([
                    ':customer_id' => $user['customer_id'],
                    ':token_hash' => $token_hash,
                    ':expires_at' => $expires_at,
                ]);
                $pdo->commit();

                // メール送信 (mail 関数を使用)
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $path = rtrim(dirname($_SERVER['REQUEST_URI']), '\\/');
                $link = $protocol . '://' . $host . $path . '/password-reset-new.php?token=' . rawurlencode($token);

                $subject = '【サイト名】パスワード再設定のご案内';
                $body = "以下のリンクからパスワード再設定ページへアクセスしてください。\n\n" . $link . "\n\n有効期限: 1時間\n\nこのメールに心当たりがない場合は破棄してください。";
                $from = 'no-reply@' . ($host);
                $headers = "From: " . $from . "\r\n" .
                           "Reply-To: " . $from . "\r\n" .
                           "Content-Type: text/plain; charset=UTF-8\r\n";

                // @ を付けて失敗しても処理を続ける（ホスティング環境によっては mail が無効）
                @mail($email, $subject, $body, $headers);

                $is_success = true;
                $message = 'ご登録済みのメールアドレスにパスワード再設定用のURLをお送りしました。';
            } else {
                // セキュリティ上、メールアドレスが存在しない場合も「送信しました」と返す
                $is_success = true;
                $message = 'ご登録済みのメールアドレスにパスワード再設定用のURLをお送りしました。';
            }
        } catch (PDOException $e) {
            error_log("Password Reset DB Error: " . $e->getMessage());
            $message = 'エラーが発生しました。もう一度お試しください。';
        }
    }
} else {
    $message = '不正なアクセスです。';
}

function h($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
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
        <?= h($message) ?><br>
        <a href="rogin-input.php" style="margin-top: 16px; display: inline-block; color: #e43131; text-decoration: none; font-weight: 600;">ログイン画面に戻る</a>
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
a { color: #e43131; }
@media (max-width: 640px) {
    .content { width: 90%; padding: 30px 20px; }
}
</style>

</body>
</html>
