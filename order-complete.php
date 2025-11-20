<?php
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>購入完了</title>
  <style>
    body { margin:0; font-family:"Noto Sans JP",sans-serif; background:#fff; }
    
    .complete-page {
      max-width: 720px;
      margin: 60px auto 80px;
      padding: 0 12px;
      box-sizing: border-box;
      text-align: center;
    }

    .complete-title {
      font-size: 22px;
      font-weight: 700;
      margin-bottom: 12px;
      color: #222;
    }

    .complete-message {
      font-size: 14px;
      margin-bottom: 28px;
      color: #333;
    }

    .complete-mark {
      width: 160px;
      height: 160px;
      margin: 0 auto 30px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .complete-mark img {
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
    }

    .complete-btn {
      display: inline-block;
      background: #e43131;
      color: #fff;
      padding: 10px 40px;
      font-size: 14px;
      font-weight: 700;
      text-decoration: none;
      border: none;
      cursor: pointer;
      border-radius: 4px;
    }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="complete-page">
    <div class="complete-title">購入完了</div>
    <div class="complete-message">ご購入ありがとうございます。</div>

    <div class="complete-mark">
      <img src="image/ChatGPT Image 2025年10月31日 10_37_57.png" alt="完了">
    </div>

    <a href="top.php" class="complete-btn">トップ画面へ</a>
  </div>

  <?php include 'footer.php'; ?>
</body>
</html>
