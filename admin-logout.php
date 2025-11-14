<?php
session_start();
$_SESSION = []; // セッション変数を空にする
session_destroy(); // セッションを破棄

// ログインページへリダイレクト（例：admin-login.php）
header('Location: admin-login-input.php');
exit;
?>
