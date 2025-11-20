<?php
session_start();

$name        = $_POST['name']        ?? '';
$tel         = $_POST['tel']         ?? '';
$email       = $_POST['email']       ?? '';
$postal1     = $_POST['postal1']     ?? '';
$postal2     = $_POST['postal2']     ?? '';
$prefecture  = $_POST['prefecture']  ?? '';
$destination = $_POST['destination'] ?? '';
$card_number = $_POST['card_number'] ?? '';
$payment_method = $_POST['payment_method'] ?? '';

$address = $prefecture; // 必要なら市区町村など足してOK

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ご購入内容確認</title>
  <style>
    body { margin:0; font-family:"Noto Sans JP",sans-serif; background:#fff; }
    
    .confirm-page {
      max-width: 720px;
      margin: 40px auto 60px;
      padding: 0 12px;
      box-sizing: border-box;
    }

    .confirm-block {
      border: 1px solid #d9d9d9;
      background: #fff;
      padding: 18px 22px 20px;
      box-sizing: border-box;
      margin-bottom: 20px;
      border-radius: 8px;
      box-shadow: 0 0 0 1px #e5e5e5;
    }

    .confirm-title {
      font-size: 16px;
      font-weight: 700;
      margin-bottom: 12px;
      color: #222;
    }

    .confirm-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 13px;
    }

    .confirm-table th,
    .confirm-table td {
      padding: 4px 4px;
      text-align: left;
      vertical-align: top;
    }

    .confirm-table th {
      width: 120px;
      font-weight: 700;
      color: #444;
    }

    .purchase-section-title {
      font-size: 14px;
      font-weight: 700;
      margin-bottom: 4px;
      color: #222;
    }

    .purchase-row {
      font-size: 13px;
      margin-bottom: 2px;
      color: #333;
    }

    .purchase-total {
      text-align: right;
      font-size: 14px;
      font-weight: 700;
      margin-top: 10px;
    }

    .purchase-total span.amount {
      color: #e43131;
    }

    .confirm-buttons {
      display: flex;
      justify-content: center;
      gap: 24px;
      margin-top: 24px;
    }

    .btn-back,
    .btn-complete {
      min-width: 120px;
      padding: 8px 0;
      font-size: 14px;
      border: none;
      cursor: pointer;
      border-radius: 4px;
    }

    .btn-back {
      background: #333;
      color: #fff;
    }

    .btn-complete {
      background: #e43131;
      color: #fff;
    }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="confirm-page">
    <form action="order-complete.php" method="post">
      <input type="hidden" name="name"        value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="tel"         value="<?= htmlspecialchars($tel, ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="email"       value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="postal1"     value="<?= htmlspecialchars($postal1, ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="postal2"     value="<?= htmlspecialchars($postal2, ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="prefecture"  value="<?= htmlspecialchars($prefecture, ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="destination" value="<?= htmlspecialchars($destination, ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="card_number" value="<?= htmlspecialchars($card_number, ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="payment_method" value="<?= htmlspecialchars($payment_method, ENT_QUOTES, 'UTF-8') ?>">

      <div class="confirm-block">
        <div class="confirm-title">入力内容確認</div>
        <table class="confirm-table">
          <tr>
            <th>氏名</th>
            <td><?= htmlspecialchars($name !== '' ? $name : '〇〇〇〇〇', ENT_QUOTES, 'UTF-8') ?></td>
          </tr>
          <tr>
            <th>電話番号</th>
            <td><?= htmlspecialchars($tel !== '' ? $tel : '〇〇〇〇〇', ENT_QUOTES, 'UTF-8') ?></td>
          </tr>
          <tr>
            <th>メールアドレス</th>
            <td><?= htmlspecialchars($email !== '' ? $email : '〇〇〇〇〇', ENT_QUOTES, 'UTF-8') ?></td>
          </tr>
          <tr>
            <th>郵便番号</th>
            <td><?= htmlspecialchars(($postal1 || $postal2) ? $postal1 . '-' . $postal2 : '〇〇〇〇〇', ENT_QUOTES, 'UTF-8') ?></td>
          </tr>
          <tr>
            <th>住所</th>
            <td><?= htmlspecialchars($address !== '' ? $address : '〇〇〇〇〇', ENT_QUOTES, 'UTF-8') ?></td>
          </tr>
          <tr>
            <th>お支払方法</th>
            <td>クレジットカード（<?= htmlspecialchars($payment_method !== '' ? $payment_method : 'visa 他', ENT_QUOTES, 'UTF-8') ?>）</td>
          </tr>
        </table>
      </div>

      <div class="confirm-block">
        <div class="confirm-title">購入内容確認</div>

        <div class="purchase-section-title">【商品】</div>
        <div class="purchase-row">〇〇〇〇福袋　　数量：1</div>
        <div class="purchase-row">〇〇〇〇福袋　　数量：1</div>

        <div class="purchase-section-title" style="margin-top:10px;">【送料】</div>
        <div class="purchase-row">〇〇〇〇円</div>

        <div class="purchase-total">
          合計：<span class="amount">〇〇〇〇円</span>
        </div>
      </div>

      <div class="confirm-buttons">
        <button type="button" class="btn-back" onclick="history.back()">戻る</button>
        <button type="submit" class="btn-complete">完了</button>
      </div>
    </form>
  </div>

  <?php include 'footer.php'; ?>
</body>
</html>
