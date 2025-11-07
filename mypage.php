<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (empty($_SESSION['customer'])) {
  // 元のページに戻したい場合は redirect パラメータを付与
  header('Location: rogin-input.php?redirect=' . rawurlencode('mypage.php'));
  exit;
}
function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="ja">
<head><meta charset="UTF-8"><title>マイページ</title></head>
<body>
  <?php include 'header.php'; ?>
  <div style="max-width:720px;margin:24px auto;">
    <h2>マイページ</h2>
    <p>ようこそ、<?= h($_SESSION['customer']['username'] ?? ($_SESSION['customer']['email'] ?? 'ゲスト')) ?> さん</p>
    <p><a href="rogout-input.php">ログアウト</a></p>
  </div>
</body>
</html>
