<?php
// 完了画面: 確認(customer-input.php)からPOSTで到達
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>登録完了</title>
  <style>
    .wrapper{ display:flex; justify-content:center; align-items:center; min-height:80vh; }
    .content{ text-align:center; width:60%; border:1px solid #000; padding:20px; border-radius:6px; background:#fff; }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="wrapper">
    <div class="content">
      <?php
      if ($_SERVER["REQUEST_METHOD"] === "POST") {
          $name_sei = isset($_POST['name_sei']) ? htmlspecialchars($_POST['name_sei'], ENT_QUOTES, 'UTF-8') : '';
          $name_mei = isset($_POST['name_mei']) ? htmlspecialchars($_POST['name_mei'], ENT_QUOTES, 'UTF-8') : '';
          $email    = isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') : '';
          $pref     = isset($_POST['prefecture']) ? htmlspecialchars($_POST['prefecture'], ENT_QUOTES, 'UTF-8') : '';
          $city     = isset($_POST['city']) ? htmlspecialchars($_POST['city'], ENT_QUOTES, 'UTF-8') : '';
          $addr     = isset($_POST['address']) ? htmlspecialchars($_POST['address'], ENT_QUOTES, 'UTF-8') : '';

          echo "<h1>会員登録完了</h1>";
          echo "<p>以下の内容で登録を受け付けました。</p>";
          echo "<p><strong>氏名:</strong> {$name_sei} - {$name_mei}</p>";
          echo "<p><strong>メールアドレス:</strong> {$email}</p>";
          echo "<p><strong>住所:</strong> {$pref} {$city} {$addr}</p>";
          echo "<p>パスワードは安全のため表示していません。</p>";
          echo "<p><a href='rogin-input.php'>ログイン画面へ</a></p>";
      } else {
          echo "<h2>不正なアクセス</h2>";
          echo "<p>登録完了ページに直接アクセスしています。フォームから登録してください。</p>";
          echo "<p><a href='customer-newinput.php'>会員登録ページへ</a></p>";
      }
      ?>
    </div>
  </div>
</body>
</html>



