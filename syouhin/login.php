<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = !empty($_SESSION['customer']);
$myPageUrl = $isLoggedIn ? "/mypage.php" : "/rogin-input.php";
?>