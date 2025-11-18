<?php
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ご購入情報入力</title>
  <style>
    .checkout-page {
      max-width: 720px;
      margin: 40px auto 60px;
      padding: 0 12px;
      box-sizing: border-box;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    .checkout-card {
      border: 1px solid #000;
      background: #fff;
      padding: 24px 28px 32px;
      box-sizing: border-box;
    }

    .checkout-section-title {
      font-size: 18px;
      font-weight: 700;
      margin-bottom: 18px;
    }

    .checkout-form-group {
      margin-bottom: 18px;
    }

    .checkout-label {
      display: block;
      font-size: 13px;
      margin-bottom: 4px;
    }

    .checkout-input,
    .checkout-select,
    .checkout-textarea {
      width: 100%;
      border: 1px solid #000;
      padding: 8px 10px;
      font-size: 14px;
      box-sizing: border-box;
    }

    .checkout-input::placeholder {
      color: #999;
      font-size: 13px;
    }

    .checkout-row {
      display: flex;
      gap: 8px;
      align-items: center;
    }

    .checkout-row .postal-part {
      flex: 0 0 100px;
    }

    .checkout-row span.hyphen {
      flex: 0 0 auto;
      font-size: 14px;
    }

    .checkout-select {
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      background: #fff;
    }

    .payment-section {
      margin-top: 32px;
      border-top: 1px solid #000;
      padding-top: 24px;
    }

    .payment-sub {
      font-size: 13px;
      margin-bottom: 10px;
    }

    .payment-brands {
      font-size: 13px;
      margin-bottom: 14px;
    }

    .payment-brands span + span {
      margin-left: 24px;
    }

    .checkout-button-area {
      text-align: center;
      margin-top: 28px;
    }

    .checkout-submit {
      display: inline-block;
      background: #ff0000;
      color: #fff;
      font-size: 14px;
      font-weight: 700;
      padding: 10px 40px;
      border: none;
      cursor: pointer;
    }

    .checkout-note {
      margin-top: 6px;
      font-size: 11px;
    }

    @media (max-width: 480px) {
      .checkout-card {
        padding: 20px 16px 26px;
      }
      .checkout-row {
        flex-wrap: wrap;
      }
      .checkout-row .postal-part {
        flex: 1 1 46%;
      }
    }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="checkout-page">
    <form action="order-confirm.php" method="post">
      <div class="checkout-card">
        <div class="checkout-section-title">お届け先</div>

        <div class="checkout-form-group">
          <label class="checkout-label">郵便先</label>
          <input type="text" name="destination" class="checkout-input">
        </div>

        <div class="checkout-form-group">
          <label class="checkout-label">氏名（フルネーム）</label>
          <input type="text" name="name" class="checkout-input">
        </div>

        <div class="checkout-form-group">
          <label class="checkout-label">電話番号</label>
          <input type="tel" name="tel" class="checkout-input" placeholder="例：000-0000-0000">
        </div>

        <div class="checkout-form-group">
          <label class="checkout-label">メールアドレス</label>
          <input type="email" name="email" class="checkout-input">
        </div>

        <div class="checkout-form-group">
          <label class="checkout-label">郵便番号</label>
          <div class="checkout-row">
            <input type="text" name="postal1" class="checkout-input postal-part" placeholder="例：000">
            <span class="hyphen">－</span>
            <input type="text" name="postal2" class="checkout-input postal-part" placeholder="例：0000">
          </div>
        </div>

        <div class="checkout-form-group">
          <label class="checkout-label">都道府県</label>
          <select name="prefecture" class="checkout-select">
            <option value="">都道府県を選択する</option>
            <option value="北海道">北海道</option>
            <option value="青森県">青森県</option>
            <option value="岩手県">岩手県</option>
            <option value="宮城県">宮城県</option>
            <option value="秋田県">秋田県</option>
            <option value="山形県">山形県</option>
            <option value="福島県">福島県</option>
            <option value="茨城県">茨城県</option>
            <option value="栃木県">栃木県</option>
            <option value="群馬県">群馬県</option>
            <option value="埼玉県">埼玉県</option>
            <option value="千葉県">千葉県</option>
            <option value="東京都">東京都</option>
            <option value="神奈川県">神奈川県</option>
            <option value="新潟県">新潟県</option>
            <option value="富山県">富山県</option>
            <option value="石川県">石川県</option>
            <option value="福井県">福井県</option>
            <option value="山梨県">山梨県</option>
            <option value="長野県">長野県</option>
            <option value="岐阜県">岐阜県</option>
            <option value="静岡県">静岡県</option>
            <option value="愛知県">愛知県</option>
            <option value="三重県">三重県</option>
            <option value="滋賀県">滋賀県</option>
            <option value="京都府">京都府</option>
            <option value="大阪府">大阪府</option>
            <option value="兵庫県">兵庫県</option>
            <option value="奈良県">奈良県</option>
            <option value="和歌山県">和歌山県</option>
            <option value="鳥取県">鳥取県</option>
            <option value="島根県">島根県</option>
            <option value="岡山県">岡山県</option>
            <option value="広島県">広島県</option>
            <option value="山口県">山口県</option>
            <option value="徳島県">徳島県</option>
            <option value="香川県">香川県</option>
            <option value="愛媛県">愛媛県</option>
            <option value="高知県">高知県</option>
            <option value="福岡県">福岡県</option>
            <option value="佐賀県">佐賀県</option>
            <option value="長崎県">長崎県</option>
            <option value="熊本県">熊本県</option>
            <option value="大分県">大分県</option>
            <option value="宮崎県">宮崎県</option>
            <option value="鹿児島県">鹿児島県</option>
            <option value="沖縄県">沖縄県</option>
          </select>
        </div>

        <div class="payment-section">
          <div class="checkout-section-title">お支払い方法</div>
          <div class="payment-sub">クレジットカード決済</div>
          <div class="payment-brands">
            <span>visa</span>
            <span>楽天カード</span>
            <span>他</span>
          </div>

          <div class="checkout-form-group">
            <label class="checkout-label">カード番号</label>
            <input type="text" name="card_number" class="checkout-input">
          </div>
        </div>

        <div class="checkout-button-area">
          <button type="submit" class="checkout-submit">確認画面に進む</button>
          <div class="checkout-note">注文内容確認画面へ</div>
        </div>
      </div>
    </form>
  </div>

  <?php include 'footer.php'; ?>
</body>
</html>
