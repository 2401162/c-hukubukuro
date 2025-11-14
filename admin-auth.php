<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// 管理者ログイン済みでなければログイン画面へ
if (!isset($_SESSION['admin_id'])) {
  header('Location: admin-login-input.php');
  exit;
}
?>