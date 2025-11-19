<?php
// ここに空白・改行を絶対入れない
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = !empty($_SESSION['customer']);
$myPageUrl = $isLoggedIn ? "/mypage.php" : "/rogin-input.php";
?>