<?php
session_start();
require_once __DIR__ . "/db-connect.php";

// ==============================
// DB 接続
// ==============================
try {
    $pdo = new PDO($connect, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB接続エラー: " . $e->getMessage());
}

// ==============================
// ログインチェック
// ==============================
$customer_id = $_SESSION['customer']['customer_id'] ?? null;

// ==============================
// AJAX リクエスト処理（カートに追加）
// ==============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'add') {
        $product_id = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);
        
        if ($product_id <= 0 || $quantity <= 0) {
            echo json_encode(['success' => false, 'message' => '不正なリクエストです']);
            exit;
        }
        
        // 商品情報を取得
        $stmt = $pdo->prepare("SELECT product_id, name, price, stock FROM product WHERE product_id = ? AND is_active = 1");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) {
            echo json_encode(['success' => false, 'message' => '商品が見つかりません']);
            exit;
        }
        
        if ($product['stock'] < $quantity) {
            echo json_encode(['success' => false, 'message' => '在庫が不足しています']);
            exit;
        }
        
        // ログインしている場合はDBに保存
        if ($customer_id) {
            // カートを取得または作成
            $stmt = $pdo->prepare("SELECT cart_id FROM cart WHERE customer_id = ?");
            $stmt->execute([$customer_id]);
            $cart = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$cart) {
                $stmt = $pdo->prepare("INSERT INTO cart (customer_id, product_count, created_at, updated_at) VALUES (?, 0, NOW(), NOW())");
                $stmt->execute([$customer_id]);
                $cart_id = $pdo->lastInsertId();
            } else {
                $cart_id = $cart['cart_id'];
            }
            
            // カートアイテムを追加または更新
            $stmt = $pdo->prepare("SELECT cart_item_id, quantity FROM cart_item WHERE cart_id = ? AND product_id = ?");
            $stmt->execute([$cart_id, $product_id]);
            $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($cart_item) {
                $new_quantity = $cart_item['quantity'] + $quantity;
                $stmt = $pdo->prepare("UPDATE cart_item SET quantity = ?, updated_at = NOW() WHERE cart_item_id = ?");
                $stmt->execute([$new_quantity, $cart_item['cart_item_id']]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO cart_item (cart_id, product_id, quantity, unit_price_snapshot, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
                $stmt->execute([$cart_id, $product_id, $quantity, $product['price']]);
            }
            
            // カートの商品数を更新
            $stmt = $pdo->prepare("UPDATE cart SET product_count = (SELECT SUM(quantity) FROM cart_item WHERE cart_id = ?), updated_at = NOW() WHERE cart_id = ?");
            $stmt->execute([$cart_id, $cart_id]);
        } else {
            // 未ログインの場合はセッションに保存
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'product_id' => $product_id,
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $quantity
                ];
            }
        }
        
        echo json_encode(['success' => true, 'message' => 'カートに追加しました']);
        exit;
    }
}

// ==============================
// 数量更新処理
// ==============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);
    
    if ($customer_id) {
        $stmt = $pdo->prepare("SELECT cart_id FROM cart WHERE customer_id = ?");
        $stmt->execute([$customer_id]);
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($cart) {
            $stmt = $pdo->prepare("UPDATE cart_item SET quantity = ?, updated_at = NOW() WHERE cart_id = ? AND product_id = ?");
            $stmt->execute([$quantity, $cart['cart_id'], $product_id]);
            
            // カートの商品数を更新
            $stmt = $pdo->prepare("UPDATE cart SET product_count = (SELECT SUM(quantity) FROM cart_item WHERE cart_id = ?), updated_at = NOW() WHERE cart_id = ?");
            $stmt->execute([$cart['cart_id'], $cart['cart_id']]);
        }
    } else {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        }
    }
    
    header('Location: cart.php');
    exit;
}

// ==============================
// 削除処理
// ==============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove'])) {
    $product_id = (int)($_POST['product_id'] ?? 0);
    
    if ($customer_id) {
        $stmt = $pdo->prepare("SELECT cart_id FROM cart WHERE customer_id = ?");
        $stmt->execute([$customer_id]);
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($cart) {
            $stmt = $pdo->prepare("DELETE FROM cart_item WHERE cart_id = ? AND product_id = ?");
            $stmt->execute([$cart['cart_id'], $product_id]);
            
            // カートの商品数を更新
            $stmt = $pdo->prepare("UPDATE cart SET product_count = (SELECT COALESCE(SUM(quantity), 0) FROM cart_item WHERE cart_id = ?), updated_at = NOW() WHERE cart_id = ?");
            $stmt->execute([$cart['cart_id'], $cart['cart_id']]);
        }
    } else {
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
    }
    
    header('Location: cart.php');
    exit;
}

function resolve_image_path(array $item): string {
    $img = $item['image_path'] ?? '';
    if (!$img) return 'img/noimage.png';
    if (preg_match('#^(https?://|//|/)#i', $img)) return $img;
    if (strpos($img, 'uploads/') === 0) return $img;
    return 'uploads/' . ltrim($img, '/');
}

// ==============================
// カート内容を取得
// ==============================
$cart_items = [];
$subtotal = 0;

if ($customer_id) {
    // DBから取得
    $stmt = $pdo->prepare("
        SELECT ci.cart_item_id, ci.product_id, ci.quantity, ci.unit_price_snapshot,
            p.name, p.price, p.stock, p.image_path
        FROM cart c
        JOIN cart_item ci ON c.cart_id = ci.cart_id
        JOIN product p ON ci.product_id = p.product_id
        WHERE c.customer_id = ? AND p.is_active = 1
        ORDER BY ci.created_at DESC
    ");
    $stmt->execute([$customer_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($cart_items as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
} else {
    // セッションから取得
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $cart_items[] = $item;
            $subtotal += $item['price'] * $item['quantity'];
        }
    }
}

$shipping = $subtotal > 0 ? 500 : 0;
$total = $subtotal + $shipping;

// カート数をセッションに保存
$_SESSION['cart_count'] = count($cart_items);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ショッピングカート</title>
    <style>
        body { margin:0; font-family:"Noto Sans JP",sans-serif; background:#fff; }
        .cart-wrapper { max-width: 1200px; margin: 40px auto; padding: 0 12px; }
        .cart-wrapper h2 { font-size: 24px; margin-bottom: 24px; color: #222; }
        .cart-flex { display: flex; gap: 24px; }
        .cart-items { flex: 3; display: flex; flex-direction: column; gap: 16px; }
        .cart-card { display: flex; border: 1px solid #d9d9d9; border-radius: 8px; overflow: hidden; background: #fff; padding: 16px; box-shadow: 0 0 0 1px #e5e5e5; }
        .cart-card img { width: 120px; height: 120px; object-fit: cover; border-radius: 6px; background: #f5f5f5; }
        .cart-info { display: flex; flex-direction: column; justify-content: space-between; flex: 1; padding-left: 16px; }
        .cart-info .name { font-weight: 700; margin: 0 0 8px; font-size: 16px; color: #222; }
        .cart-info .price { font-weight: 700; font-size: 20px; color: #e43131; margin: 8px 0; }
        .cart-info .date { font-size: 13px; color: #666; margin: 8px 0; }
        .cart-info .date .delivery-date { font-weight: 700; color: #222; }
        .bottom-row { display: flex; gap: 12px; align-items: center; margin-top: 12px; }
        .qty-form select { padding: 6px 10px; border: 1px solid #d9d9d9; border-radius: 4px; font-size: 14px; }
        .remove-form button { padding: 6px 16px; background: #333; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 13px; }
        .remove-form button:hover { background: #555; }
        .cart-summary { flex: 1; border: 1px solid #d9d9d9; border-radius: 8px; padding: 24px; background: #fff; height: fit-content; box-shadow: 0 0 0 1px #e5e5e5; }
        .cart-summary p { margin: 12px 0; font-size: 15px; color: #333; }
        .cart-summary .total { color: #e43131; font-size: 20px; font-weight: 700; margin-top: 16px; padding-top: 16px; border-top: 1px solid #d9d9d9; }
        .buy-btn { width: 100%; padding: 12px 0; background: #e43131; color: #fff; border: none; border-radius: 4px; font-size: 16px; font-weight: 700; cursor: pointer; margin-top: 16px; }
        .buy-btn:hover { background: #c72a2a; }
        .empty-cart { text-align: center; padding: 60px 20px; color: #666; }
        .empty-cart a { color: #e43131; text-decoration: none; font-weight: 700; }
        @media (max-width: 768px) {
            .cart-flex { flex-direction: column; }
            .cart-card { flex-direction: column; }
            .cart-card img { width: 100%; height: 200px; }
            .cart-info { padding-left: 0; padding-top: 12px; }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/header.php'; ?>

    <div class="cart-wrapper">
        <h2>ショッピングカート</h2>
        
        <?php if (empty($cart_items)): ?>
            <div class="empty-cart">
                <p>カートに商品がありません</p>
                <p><a href="product-list.php">商品一覧へ</a></p>
            </div>
        <?php else: ?>
            <div class="cart-flex">
                <div class="cart-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-card">
                            <img src="<?= htmlspecialchars($item['image_path']) ?>" 
                                 alt="<?= htmlspecialchars($item['name']) ?>"
                                 onerror="this.src='image/placeholder.png'">
                            <div class="cart-info">
                                <div>
                                    <p class="name"><?= htmlspecialchars($item['name']) ?></p>
                                    <p class="price">¥<?= number_format($item['price']) ?></p>
                                    <p class="date">
                                        <?php
                                        $deliveryDate = strtotime('+3 days');
                                        $weekDays = ['日', '月', '火', '水', '木', '金', '土'];
                                        $dayOfWeek = $weekDays[date('w', $deliveryDate)];
                                        $dateText = date('n月j日', $deliveryDate) . '(' . $dayOfWeek . ')';
                                        ?>
                                        <span class="delivery-date"><?= $dateText ?></span>にお届け予定
                                    </p>
                                </div>
                                <div class="bottom-row">
                                    <form method="post" class="qty-form">
                                        <select name="quantity" onchange="this.form.submit()">
                                            <?php for ($i = 1; $i <= min(10, $item['stock']); $i++): ?>
                                                <option value="<?= $i ?>" <?= $i == $item['quantity'] ? 'selected' : '' ?>>
                                                    <?= $i ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                        <input type="hidden" name="update" value="1">
                                    </form>
                                    <form method="post" class="remove-form">
                                        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                        <button type="submit" name="remove" value="1">削除</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="cart-summary">
                    <p>小計: ¥<?= number_format($subtotal) ?></p>
                    <p>送料: ¥<?= number_format($shipping) ?></p>
                    <p class="total">合計: ¥<?= number_format($total) ?></p>
                    <form action="order-input.php" method="get">
                        <button type="submit" class="buy-btn">購入手続きへ</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php
    $footerPath = __DIR__ . '/footer.php';
    if (is_file($footerPath)) { require $footerPath; }
    ?>
</body>
</html>
