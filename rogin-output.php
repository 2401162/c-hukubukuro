<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$redirect = trim($_POST['redirect'] ?? '');

function back_with_error($msg, $redirect = '') {
    $_SESSION['login_error'] = $msg;
    $q = $redirect !== '' ? ('?redirect=' . rawurlencode($redirect)) : '';
    header('Location: rogin-input.php' . $q);
    exit;
}

if ($email === '' || $password === '') {
    back_with_error('メールアドレスとパスワードを入力してください。', $redirect);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    back_with_error('メールアドレスの形式が正しくありません。', $redirect);
}

$didDbAuth = false;

try {
    require_once 'db-connect.php';
    $pdo = new PDO($connect, USER, PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    // customer テーブルから検索
    // カラム: customer_id (PK), email (UNI), password_hash, name, phone, postal_code, prefecture, city, address_line, is_active, created_at, updated_at
    $stmt = $pdo->prepare("SELECT customer_id, email, password_hash, name, is_active FROM customer WHERE email = :email AND is_active = 1 LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && isset($user['password_hash']) && password_verify($password, $user['password_hash'])) {
        // 名前を分割（スペースで分割、なければ全体を使用）
        $name_parts = explode(' ', $user['name'], 2);
        $name_sei = $name_parts[0] ?? '';
        $name_mei = $name_parts[1] ?? '';
        
        // セッションにユーザー情報をセット（既存コード互換のため二形態を保持）
        $_SESSION['customer'] = [
            'customer_id' => $user['customer_id'],
            'id'          => $user['customer_id'],  // 互換性のため
            'email'       => $user['email'],
            'username'    => $user['name'] ?? 'ユーザー',
            'name_sei'    => $name_sei,
            'name_mei'    => $name_mei,
        ];
        // 既存のマイページなどが参照するトップレベルのキーもセットしておく
        $_SESSION['customer_id'] = $user['customer_id'];
        $didDbAuth = true;
        
        // ログイン時刻を更新
        try {
            $updateStmt = $pdo->prepare("UPDATE customer SET last_login_at = CURRENT_TIMESTAMP WHERE customer_id = :customer_id");
            $updateStmt->execute([':customer_id' => $user['customer_id']]);
        } catch (Throwable $e) {
            // ログイン時刻更新失敗は無視
        }
    }
} catch (Throwable $e) {
    error_log("Login DB Error: " . $e->getMessage());
}

if ($didDbAuth) {
    $to = $redirect !== '' ? $redirect : 'mypage/mypage.php';
    header('Location: ' . $to);
    exit;
}

// デモユーザーでのテスト（開発用）
$DEMO_USER = [
    'email'    => 'test@example.com',
    'password' => '1234',
    'username' => 'テストユーザー',
];

if ($email === $DEMO_USER['email'] && $password === $DEMO_USER['password']) {
    $_SESSION['customer'] = [
        'email'    => $DEMO_USER['email'],
        'username' => $DEMO_USER['username'],
    ];
    // デモユーザー用にトップレベルの customer_id を設定（0 を割り当てる）
    $_SESSION['customer_id'] = 0;
    $to = $redirect !== '' ? $redirect : 'mypage/mypage.php';
    header('Location: ' . $to);
    exit;
}

back_with_error('メールアドレスまたはパスワードが正しくありません。', $redirect);
?>
