<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

function back_with_error($msg, $redirect = '') {
  $_SESSION['login_error'] = $msg;
  $q = $redirect !== '' ? ('?redirect=' . rawurlencode($redirect)) : '';
  header('Location: rogin-input.php' . $q);
  exit;
}

$email    = trim($_POST['email']    ?? '');
$password =        $_POST['password'] ?? '';
$redirect = trim($_POST['redirect'] ?? ''); // 任意: ログイン後に戻す先

if ($email === '' || $password === '') {
  back_with_error('メールアドレスとパスワードを入力してください。', $redirect);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  back_with_error('メールアドレスの形式が正しくありません。', $redirect);
}

/** 1) まず DB 認証を試みる（db-connect.php がある場合） **/
$didDbAuth = false;
try {
  // db-connect.php 例：
  //   $connect = 'mysql:host=...;dbname=...;charset=utf8mb4';
  //   define('USER','xxx'); define('PASS','yyy');
  //   define('TABLE_MEMBERS','customer'); など
  @include_once 'db-connect.php';

  if (isset($connect) && defined('USER') && defined('PASS')) {
    $pdo = new PDO($connect, USER, PASS, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // テーブル名は db-connect.php 側の定数があれば使う。無ければ customer をデフォルトに
    $table = defined('TABLE_MEMBERS') ? TABLE_MEMBERS : 'customer';

    // email 一意想定
    $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && isset($user['password']) && password_verify($password, $user['password'])) {
      // ✅ 認証成功：セッションキーを header.php の仕様に合わせる
      $_SESSION['customer'] = [
        'id'        => $user['id']       ?? null,
        'email'     => $user['email']    ?? $email,
        'username'  => $user['username'] ?? ($user['name'] ?? 'ユーザー'),
        'name_sei'  => $user['name_sei'] ?? null,
        'name_mei'  => $user['name_mei'] ?? null,
      ];
      $didDbAuth = true;
    }
  }
} catch (Throwable $e) {
  // DBエラーはログに出すだけ。下のデモ認証にフォールバック。
  error_log('Login DB error: ' . $e->getMessage());
}

if ($didDbAuth) {
  // ログイン後の遷移先（指定があればそちらへ、無ければマイページ）
  $to = $redirect !== '' ? $redirect : 'mypage.php';
  header('Location: ' . $to);
  exit;
}

/** 2) ここに来たら DB 認証は未設定 or 失敗 → デモ認証を実行 **/
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

// どちらも失敗
back_with_error('メールアドレスまたはパスワードが正しくありません。', $redirect);

