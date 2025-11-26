<?php
session_start();
require_once 'db-connect.php';

$order_success = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name           = $_POST['name'] ?? '';
    $tel            = $_POST['tel'] ?? '';
    $email          = $_POST['email'] ?? '';
    $postal1        = $_POST['postal1'] ?? '';
    $postal2        = $_POST['postal2'] ?? '';
    $prefecture     = $_POST['prefecture'] ?? '';
    $destination    = $_POST['destination'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';
    $total_amount   = (int)($_POST['total_amount'] ?? 0);
    
    $postal_code = $postal1 . $postal2;
    $customer_id = $_SESSION['customer']['customer_id'] ?? null;
    
    try {
        $pdo = new PDO($connect, USER, PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        
        $pdo->beginTransaction();
        
        // カート内容を取得
        $cart_items = [];
        if ($customer_id) {
            $stmt = $pdo->prepare("
                SELECT ci.product_id, ci.quantity, p.price, p.stock
                FROM cart c
                JOIN cart_item ci ON c.cart_id = ci.cart_id
                JOIN product p ON ci.product_id = p.product_id
                WHERE c.customer_id = ? AND p.is_active = 1
            ");
            $stmt->execute([$customer_id]);
            $cart_items = $stmt->fetchAll();
        } else {
            // セッションから取得
            if (!empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $item) {
                    $stmt = $pdo->prepare("SELECT price, stock FROM product WHERE product_id = ? AND is_active = 1");
                    $stmt->execute([$item['product_id']]);
                    $product = $stmt->fetch();
                    if ($product) {
                        $cart_items[] = [
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                            'price' => $product['price'],
                            'stock' => $product['stock']
                        ];
                    }
                }
            }
        }
        
        if (empty($cart_items)) {
            throw new Exception('カートが空です');
        }
        
        // 在庫チェック
        foreach ($cart_items as $item) {
            if ($item['stock'] < $item['quantity']) {
                throw new Exception('在庫が不足しています');
            }
        }
        
        // 注文を作成
        $stmt = $pdo->prepare("
            INSERT INTO orders (customer_id, total_amount, payment_method, status, 
                               ship_postal_code, ship_prefecture, ship_city, ship_address_line, 
                               created_at, updated_at)
            VALUES (?, ?, ?, 'PAID', ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([
            $customer_id,
            $total_amount,
            $payment_method,
            $postal_code,
            $prefecture,
            '',
            $destination
        ]);
        
        $order_id = $pdo->lastInsertId();
        
        // 注文アイテムを作成 & 在庫を減らす
        foreach ($cart_items as $item) {
            $subtotal = $item['price'] * $item['quantity'];
            
            $stmt = $pdo->prepare("
                INSERT INTO order_item (order_id, product_id, quantity, unit_price, subtotal, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())
            ");
            $stmt->execute([
                $order_id,
                $item['product_id'],
                $item['quantity'],
                $item['price'],
                $subtotal
            ]);
            
            // 在庫を減らす
            $stmt = $pdo->prepare("UPDATE product SET stock = stock - ? WHERE product_id = ?");
            $stmt->execute([$item['quantity'], $item['product_id']]);
        }
        
        // カートをクリア
        if ($customer_id) {
            $stmt = $pdo->prepare("SELECT cart_id FROM cart WHERE customer_id = ?");
            $stmt->execute([$customer_id]);
            $cart = $stmt->fetch();
            
            if ($cart) {
                $stmt = $pdo->prepare("DELETE FROM cart_item WHERE cart_id = ?");
                $stmt->execute([$cart['cart_id']]);
                
                $stmt = $pdo->prepare("UPDATE cart SET product_count = 0, updated_at = NOW() WHERE cart_id = ?");
                $stmt->execute([$cart['cart_id']]);
            }
        } else {
            $_SESSION['cart'] = [];
        }
        
        $_SESSION['cart_count'] = 0;
        
        $pdo->commit();
        $order_success = true;
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Order complete error: " . $e->getMessage());
        $error_message = $e->getMessage();
    }
}
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

    .error-message {
      color: #dc3545;
      background: #f8d7da;
      padding: 16px;
      border-radius: 6px;
      margin-bottom: 24px;
    }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="complete-page">
    <?php if ($order_success): ?>
      <div class="complete-title">購入完了</div>
      <div class="complete-message">ご購入ありがとうございます。</div>

      <div class="complete-mark">
        <img src="image/ChatGPT Image 2025年10月31日 10_37_57.png" alt="完了">
      </div>

      <a href="top.php" class="complete-btn">トップ画面へ</a>
    <?php else: ?>
      <div class="complete-title">購入エラー</div>
      <div class="error-message">
        <?= htmlspecialchars($error_message ?: '購入処理中にエラーが発生しました', ENT_QUOTES, 'UTF-8') ?>
      </div>
      <a href="cart.php" class="complete-btn">カートに戻る</a>
    <?php endif; ?>
  </div>

  <?php include 'footer.php'; ?>
</body>
</html>
