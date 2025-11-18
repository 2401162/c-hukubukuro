<?php
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>購入完了</title>
  <style>
    .complete-page {
      max-width: 720px;
      margin: 60px auto 80px;
      padding: 0 12px;
      box-sizing: border-box;
      text-align: center;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    .complete-title {
      font-size: 22px;
      font-weight: 700;
      margin-bottom: 12px;
    }

    .complete-message {
      font-size: 14px;
      margin-bottom: 28px;
    }

    .complete-mark {
      width: 160px;
      height: 160px;
      border-radius: 50%;
      border: 10px solid #f8a4a4;
      margin: 0 auto 30px;
      position: relative;
      box-sizing: border-box;
    }

    .complete-mark::before {
      content: "";
      position: absolute;
      left: 32%;
      top: 40%;
      width: 22%;
      height: 8px;
      border-radius: 4px;
      background: #f8a4a4;
      transform: rotate(40deg);
    }

    .complete-mark::after {
      content: "";
      position: absolute;
      left: 46%;
      top: 32%;
      width: 32%;
      height: 8px;
      border-radius: 4px;
      background: #f8a4a4;
      transform: rotate(-40deg);
    }

    .complete-btn {
      display: inline-block;
      background: #ff0000;
      color: #fff;
      padding: 10px 40px;
      font-size: 14px;
      font-weight: 700;
      text-decoration: none;
      border: none;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="complete-page">
    <div class="complete-title">購入完了</div>
    <div class="complete-message">ご購入ありがとうございます。</div>

    <div class="complete-mark"></div>

    <a href="top.php" class="complete-btn">トップ画面へ</a>
  </div>

  <?php include 'footer.php'; ?>
</body>
</html>
