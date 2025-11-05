<?php
// member-register.php
// customer-newinput.php から POST された確認済みデータを受け取り、members テーブルに保存します。
require_once 'db-connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo '<p>不正なアクセスです。</p>';
    echo '<p><a href="customer-input.php">会員登録ページへ</a></p>';
    exit;
}

// 受け取り
$name_sei = isset($_POST['name_sei']) ? trim($_POST['name_sei']) : '';
$name_mei = isset($_POST['name_mei']) ? trim($_POST['name_mei']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$tel = isset($_POST['tel']) ? trim($_POST['tel']) : '';
$postal_code1 = isset($_POST['postal_code1']) ? trim($_POST['postal_code1']) : '';
$postal_code2 = isset($_POST['postal_code2']) ? trim($_POST['postal_code2']) : '';
$prefecture = isset($_POST['prefecture']) ? trim($_POST['prefecture']) : '';
$city = isset($_POST['city']) ? trim($_POST['city']) : '';
$address = isset($_POST['address']) ? trim($_POST['address']) : '';
$building = isset($_POST['building']) ? trim($_POST['building']) : '';

$errors = [];
if ($name_sei === '' || $name_mei === '') $errors[] = '氏名は必須です。';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = '正しいメールアドレスを入力してください。';
if ($password === '') $errors[] = 'パスワードは必須です。';
if ($postal_code1 === '' || $postal_code2 === '') $errors[] = '郵便番号は必須です。';

if (!empty($errors)) {
    echo '<h2>エラー</h2>';
    echo '<ul style="color:#c00;">';
    foreach ($errors as $err) echo '<li>' . htmlspecialchars($err, ENT_QUOTES, 'UTF-8') . '</li>';
    echo '</ul>';
    echo '<p><a href="javascript:history.back()">戻る</a></p>';
    exit;
}

// パスワードが既にハッシュ化されているか判定（bcrypt かどうか）
$store_password = $password;
if (!preg_match('/^\$2y\$|^\$2a\$|^\$argon2/i', $password)) {
    // ハッシュ化して保存
    $store_password = password_hash($password, PASSWORD_DEFAULT);
}

try {
    $pdo = new PDO($connect, USER, PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    // 重複メールチェック
    $stmt = $pdo->prepare('SELECT id FROM ' . TABLE_MEMBERS . ' WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        echo '<p style="color:#c00;">そのメールアドレスは既に登録されています。</p>';
        echo '<p><a href="javascript:history.back()">戻る</a></p>';
        exit;
    }

    // 挿入
    $sql = 'INSERT INTO ' . TABLE_MEMBERS . ' (name_sei, name_mei, email, password, tel, postal_code1, postal_code2, prefecture, city, address, building, created_at) VALUES (:name_sei, :name_mei, :email, :password, :tel, :postal_code1, :postal_code2, :prefecture, :city, :address, :building, NOW())';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name_sei' => $name_sei,
        ':name_mei' => $name_mei,
        ':email' => $email,
        ':password' => $store_password,
        ':tel' => $tel,
        ':postal_code1' => $postal_code1,
        ':postal_code2' => $postal_code2,
        ':prefecture' => $prefecture,
        ':city' => $city,
        ':address' => $address,
        ':building' => $building,
    ]);

    // 登録成功 -> 確認用ページを表示（customer_done.php がPOST を期待しているので、ここは同じ POST を用いて処理する）
    require 'customer_done.php';
    exit;

} catch (PDOException $e) {
    error_log('DB error: ' . $e->getMessage());
    echo '<h2>登録エラー</h2>';
    echo '<p>登録中にエラーが発生しました。時間をおいて再度お試しください。</p>';
    echo '<p><a href="customer-input.php">会員登録ページへ</a></p>';
    exit;
}
?>