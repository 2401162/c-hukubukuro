<?php
session_start();
require 'db-connect.php'; 

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

try {
    $pdo = new PDO($connect, USER, PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);


    $sql = "SELECT * FROM admin WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && $password === $admin['password_hash']) {
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_name'] = $admin['name'];

        header("Location: admin-top.php");
        exit();
    } else {

        echo "<p style='text-align:center;color:red;'>ログインに失敗しました。メールアドレスまたはパスワードが正しくありません。</p>";
        echo "<p style='text-align:center;'><a href='admin-login-input.php'>戻る</a></p>";
    }

} catch (PDOException $e) {
    echo "<p style='color:red;'>データベースエラー: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
