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
    $table = 'customer';
    $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && isset($user['password']) && password_verify($password, $user['password'])) {
        $_SESSION['customer'] = [
            'id'        => $user['id']        ?? null,
            'email'     => $user['email']     ?? $email,
            'username'  => $user['username']  ?? ($user['name'] ?? 'ユーザー'),
            'name_sei'  => $user['name_sei']  ?? null,
            'name_mei'  => $user['name_mei']  ?? null,
        ];
        $didDbAuth = true;
    }
} catch (Throwable $e) {
}

if ($didDbAuth) {
    $to = $redirect !== '' ? $redirect : 'mypage.php';
    header('Location: ' . $to);
    exit;
}

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
    $to = $redirect !== '' ? $redirect : 'mypage.php';
    header('Location: ' . $to);
    exit;
}

back_with_error('メールアドレスまたはパスワードが正しくありません。', $redirect);
?>
