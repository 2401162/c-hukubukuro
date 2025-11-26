<?php
session_start();

// ログイン情報を取得
$isLoggedIn = !empty($_SESSION['customer']);
$customerData = [];

if ($isLoggedIn) {
    try {
        require_once 'db-connect.php';
        $pdo = new PDO($connect, USER, PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        
        $stmt = $pdo->prepare("SELECT name, email, phone, postal_code, prefecture, city, address_line FROM customer WHERE customer_id = :customer_id LIMIT 1");
        $stmt->execute([':customer_id' => $_SESSION['customer']['customer_id']]);
        $customerData = $stmt->fetch() ?: [];
        
        // 郵便番号を分割
        if (!empty($customerData['postal_code'])) {
            $postal = $customerData['postal_code'];
            $customerData['postal1'] = substr($postal, 0, 3);
            $customerData['postal2'] = substr($postal, 3);
        }
    } catch (Throwable $e) {
        error_log("Order input DB Error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ご購入情報入力</title>
  <style>
    body { margin:0; font-family:"Noto Sans JP",sans-serif; background:#fff; }
    
    .checkout-page {
      max-width: 720px;
      margin: 40px auto 60px;
      padding: 0 12px;
      box-sizing: border-box;
    }

    .checkout-card {
      border: 1px solid #d9d9d9;
      background: #fff;
      padding: 24px 28px 32px;
      box-sizing: border-box;
      border-radius: 8px;
      box-shadow: 0 0 0 1px #e5e5e5;
    }

    .checkout-section-title {
      font-size: 18px;
      font-weight: 700;
      margin-bottom: 18px;
      color: #222;
    }

    .checkout-form-group {
      margin-bottom: 18px;
    }

    .checkout-label {
      display: block;
      font-size: 12px;
      margin-bottom: 6px;
      color: #444;
    }

    .checkout-input,
    .checkout-select,
    .checkout-textarea {
      width: 100%;
      border: 1px solid #d9d9d9;
      padding: 8px 10px;
      font-size: 14px;
      box-sizing: border-box;
      border-radius: 2px;
      height: 34px;
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
      border-top: 1px solid #d9d9d9;
      padding-top: 24px;
    }

    .payment-sub {
      font-size: 13px;
      margin-bottom: 14px;
      color: #444;
    }

    .payment-options {
      margin-bottom: 18px;
    }

    .payment-option {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
      padding: 10px;
      border: 1px solid #d9d9d9;
      border-radius: 4px;
      cursor: pointer;
      transition: background 0.2s;
    }

    .payment-option:hover {
      background: #f9f9f9;
    }

    .payment-option input[type="radio"] {
      margin-right: 10px;
    }

    .payment-option label {
      cursor: pointer;
      font-size: 14px;
      flex: 1;
    }

    .card-input-group {
      display: none;
      margin-top: 14px;
    }

    .card-input-group.active {
      display: block;
    }

    .checkout-button-area {
      text-align: center;
      margin-top: 28px;
    }

    .checkout-submit {
      display: inline-block;
      background: #e43131;
      color: #fff;
      font-size: 14px;
      font-weight: 700;
      padding: 10px 40px;
      border: none;
      cursor: pointer;
      border-radius: 4px;
    }

    .checkout-note {
      margin-top: 6px;
      font-size: 11px;
      color: #666;
    }

    .edit-info-btn {
      display: inline-block;
      background: #333;
      color: #fff;
      font-size: 12px;
      padding: 6px 16px;
      border: none;
      cursor: pointer;
      border-radius: 4px;
      margin-bottom: 12px;
    }

    .info-display {
      background: #f9f9f9;
      padding: 12px;
      border-radius: 4px;
      margin-bottom: 12px;
      font-size: 14px;
      line-height: 1.6;
    }

    .info-display.hidden {
      display: none;
    }

    .form-fields.hidden {
      display: none;
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
    <form action="order-confirm.php" method="post" id="orderForm">
      <div class="checkout-card">
        <div class="checkout-section-title">お届け先</div>

        <?php if ($isLoggedIn && !empty($customerData)): ?>
          <button type="button" class="edit-info-btn" id="editBtn">変更する</button>
          
          <div id="infoDisplay" class="info-display">
            <div><strong>氏名：</strong><?= htmlspecialchars($customerData['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
            <div><strong>電話番号：</strong><?= htmlspecialchars($customerData['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
            <div><strong>メールアドレス：</strong><?= htmlspecialchars($customerData['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
            <div><strong>郵便番号：</strong><?= htmlspecialchars(($customerData['postal1'] ?? '') . '-' . ($customerData['postal2'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
            <div><strong>住所：</strong><?= htmlspecialchars(($customerData['prefecture'] ?? '') . ' ' . ($customerData['city'] ?? '') . ' ' . ($customerData['address_line'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
          </div>
        <?php endif; ?>

        <div id="formFields" class="form-fields <?= ($isLoggedIn && !empty($customerData)) ? 'hidden' : '' ?>">
          <div class="checkout-form-group">
            <label class="checkout-label">郵便先</label>
            <input type="text" name="destination" class="checkout-input" value="<?= htmlspecialchars($customerData['address_line'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          </div>

          <div class="checkout-form-group">
            <label class="checkout-label">氏名（フルネーム）</label>
            <input type="text" name="name" class="checkout-input" value="<?= htmlspecialchars($customerData['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          </div>

          <div class="checkout-form-group">
            <label class="checkout-label">電話番号</label>
            <input type="tel" name="tel" class="checkout-input" placeholder="例：000-0000-0000" value="<?= htmlspecialchars($customerData['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          </div>

          <div class="checkout-form-group">
            <label class="checkout-label">メールアドレス</label>
            <input type="email" name="email" class="checkout-input" value="<?= htmlspecialchars($customerData['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          </div>

          <div class="checkout-form-group">
            <label class="checkout-label">郵便番号</label>
            <div class="checkout-row">
              <input type="text" name="postal1" class="checkout-input postal-part" placeholder="例：000" value="<?= htmlspecialchars($customerData['postal1'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              <span class="hyphen">－</span>
              <input type="text" name="postal2" class="checkout-input postal-part" placeholder="例：0000" value="<?= htmlspecialchars($customerData['postal2'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
          </div>

          <div class="checkout-form-group">
            <label class="checkout-label">都道府県</label>
            <select name="prefecture" class="checkout-select">
              <option value="">都道府県を選択する</option>
              <?php
              $prefs = ['北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県','茨城県','栃木県','群馬県','埼玉県','千葉県','東京都','神奈川県','新潟県','富山県','石川県','福井県','山梨県','長野県','岐阜県','静岡県','愛知県','三重県','滋賀県','京都府','大阪府','兵庫県','奈良県','和歌山県','鳥取県','島根県','岡山県','広島県','山口県','徳島県','香川県','愛媛県','高知県','福岡県','佐賀県','長崎県','熊本県','大分県','宮崎県','鹿児島県','沖縄県'];
              $selectedPref = $customerData['prefecture'] ?? '';
              foreach($prefs as $p) {
                $selected = ($p === $selectedPref) ? ' selected' : '';
                echo "<option value='$p'$selected>$p</option>";
              }
              ?>
            </select>
          </div>
        </div>

        <div class="payment-section">
          <div class="checkout-section-title">お支払い方法</div>
          <div class="payment-sub">クレジットカード決済を選択してください</div>

          <div class="payment-options">
            <div class="payment-option">
              <input type="radio" name="payment_method" id="visa" value="visa" required>
              <label for="visa">Visa</label>
            </div>
            <div class="payment-option">
              <input type="radio" name="payment_method" id="rakuten" value="rakuten">
              <label for="rakuten">楽天カード</label>
            </div>
            <div class="payment-option">
              <input type="radio" name="payment_method" id="other" value="other">
              <label for="other">その他のクレジットカード</label>
            </div>
          </div>

          <div class="card-input-group" id="cardInputGroup">
            <div class="checkout-form-group">
              <label class="checkout-label">カード番号</label>
              <input type="text" name="card_number" class="checkout-input" placeholder="0000-0000-0000-0000">
            </div>
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

  <script>
    // 変更ボタンの動作
    const editBtn = document.getElementById('editBtn');
    const infoDisplay = document.getElementById('infoDisplay');
    const formFields = document.getElementById('formFields');

    if (editBtn) {
      editBtn.addEventListener('click', function() {
        if (formFields.classList.contains('hidden')) {
          // 編集モードに切り替え
          infoDisplay.classList.add('hidden');
          formFields.classList.remove('hidden');
          editBtn.textContent = '表示に戻す';
        } else {
          // 表示モードに戻す
          formFields.classList.add('hidden');
          infoDisplay.classList.remove('hidden');
          editBtn.textContent = '変更する';
        }
      });
    }

    // カード番号入力欄の表示制御
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    const cardInputGroup = document.getElementById('cardInputGroup');

    paymentRadios.forEach(radio => {
      radio.addEventListener('change', function() {
        if (this.checked) {
          cardInputGroup.classList.add('active');
        }
      });
    });
  </script>
</body>
</html>
