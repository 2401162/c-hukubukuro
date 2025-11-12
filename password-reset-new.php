<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// トークンチェック（GET パラメータから取得して DB で検証）
require_once 'db-connect.php';

$raw_token = $_GET['token'] ?? null;
$reset_email = null;
$is_valid = false;

if ($raw_token) {
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
        $token_hash = hash('sha256', $raw_token);
        $stmt = $pdo->prepare("SELECT pr.customer_id, pr.expires_at, c.email FROM password_resets pr LEFT JOIN customer c ON pr.customer_id = c.customer_id WHERE pr.token_hash = :token_hash LIMIT 1");
        $stmt->execute([':token_hash' => $token_hash]);
        $row = $stmt->fetch();
        if ($row && strtotime($row['expires_at']) > time() && $row['email']) {
            $is_valid = true;
            $reset_email = $row['email'];
        }
    } catch (PDOException $e) {
        error_log('Password reset token check error: ' . $e->getMessage());
    }
}
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

    <?php if ($is_valid): ?>
        <form action="password-reset-complete.php" method="post" class="reset-form">
            <p>新しいパスワード</p>
            <input type="password" name="password" class="text-input" required minlength="8">

            <p>新しいパスワード（確認用）</p>
            <input type="password" name="password2" class="text-input" required minlength="8">

            <input type="hidden" name="token" value="<?= htmlspecialchars($raw_token, ENT_QUOTES, 'UTF-8') ?>">

            <input type="submit" value="完了" class="btn-red">
        </form>
    <?php else: ?>
        <p style="color: #c33; margin: 20px 0;">
            パスワードのリセットリンクが有効期限切れまたは無効です。<br>
            <a href="password-reset-mail-input.php">パスワード再設定を最初からやり直す</a>
        </p>
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
.text-input {
    width:80%; height:40px; margin-bottom:20px;
    padding:6px; font-size:16px; box-sizing: border-box;
}
.btn-red {
    width:100%; padding:12px;
    background:#ff3c3c; color:#fff;
    border:none; border-radius:4px;
    cursor:pointer; font-size:16px;
}
a { color: #e43131; }
@media (max-width: 640px) {
    .content { width: 90%; padding: 30px 20px; }
    .text-input { width: 100%; }
}
</style>

</body>
</html>
