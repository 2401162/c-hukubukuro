<?php
session_start();
require_once __DIR__ . '/db-connect.php';

$pdo = new PDO($connect, USER, PASS);

if (!empty($_POST['id']) && !empty($_POST['name']) && !empty($_POST['price']) && !empty($_POST['count'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = (int)$_POST['price'];
    $count = (int)$_POST['count'];

    // すでにカートにある場合は個数を加算
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['count'] += $count;
    } else {
        $_SESSION['cart'][$id] = [
            'id' => $id,
            'name' => $name,
            'price' => $price,
            'count' => $count,
        ];
    }

    // 追加後はカートページへリダイレクト
    header('Location: cart.php');
    exit;
}

if (!empty($_POST['update']) && !empty($_POST['update_id']) && !empty($_POST['update_count'])) {
    $id = $_POST['update_id'];
    $count = (int)$_POST['update_count'];
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['count'] = $count;
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (!empty($_POST['remove'])) {
    $id = $_POST['remove'];
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$subtotal = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $subtotal += $item['price'] * $item['count'];
    }
}
$shipping = $subtotal > 0 ? 500 : 0;
$total = $subtotal + $shipping;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>カート画面</title>
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>

<div class="cart-wrapper">
    <h2>ショッピングカート</h2>
    <div class="cart-flex">
        <!-- 左：商品一覧 -->
        <div class="cart-items">
            <?php
            if (empty($_SESSION['cart'])) {
                echo "<p>カートに商品がありません。</p>";
            } else {
                foreach ($_SESSION['cart'] as $id => $item) {
            ?>
            <div class="cart-card">
                <img src="image/<?= htmlspecialchars($item['id']) ?>.png" alt="<?= htmlspecialchars($item['name']) ?>">
                <div class="cart-info">
                    <!-- 上段：商品名と金額 -->
                    <div class="top-row">
                        <p class="name"><?= htmlspecialchars($item['name']) ?></p>
                        <h3 class="price"><?= number_format($item['price']) ?>円</h3>
                    </div>

                    <p class="date">
                        <?php
                        $deliveryDate = strtotime('+3 days');
                        $weekDays = ['日', '月', '火', '水', '木', '金', '土'];
                        $dayOfWeek = $weekDays[date('w', $deliveryDate)];
                        $dateText = date('n月j日', $deliveryDate) . '(' . $dayOfWeek . ')';
                        ?>
                        <span class="delivery-date"><?= $dateText ?></span>にお届け予定
                    </p>

                    <!-- 下段：個数と削除ボタン -->
                    <div class="bottom-row">
                        <form method="post" class="qty-form">
                            <select name="update_count" onchange="this.form.submit()">
                                <?php
                                for ($i = 1; $i <= 10; $i++) {
                                    $selected = $i == $item['count'] ? 'selected' : '';
                                    echo "<option value='$i' $selected>$i</option>";
                                }
                                ?>
                            </select>
                            <input type="hidden" name="update_id" value="<?= $id ?>">
                            <input type="hidden" name="update" value="1">
                        </form>

                        <form method="post" class="remove-form">
                            <button type="submit" name="remove" value="<?= $id ?>">削除する</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php
                }
            }
            ?>
        </div>

        <!-- 右：合計 -->
        <div class="cart-summary">
            <p>小計: <?= number_format($subtotal) ?>円</p>
            <p>送料: <?= number_format($shipping) ?>円</p>
            <p class="total"><strong>合計: <?= number_format($total) ?>円</strong></p>
            <form action="checkout.php" method="post">
                <button type="submit" class="buy-btn">購入に進む</button>
            </form>
        </div>
    </div>
</div>

<style>
.cart-wrapper {
    max-width: 1000px;
    margin: 40px;
}
.cart-flex {
    display: flex;
    gap: 20px;
}
.cart-items {
    flex: 3;
    display: flex;
    flex-direction: column;
    gap: 20px;
}
.cart-card {
    display: flex;
    border: 1px solid #ddd;
    border-radius: 10px;
    overflow: hidden;
    background: #ffffffff;
    padding: 10px;
}
.cart-card img {
    width: 180px;
    height: 180px;
    object-fit: cover;
    border-radius: 8px;
}
.cart-info {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex: 1;
    padding: 5px;
}

.stock-ok {
    color: #6affb2ff;
}

.stock-ng {
    color: #ff0000;
}

.cart-info .date {
    margin-left: 8px;
}

.cart-info .date .delivery-date {
    font-weight: bold;
}

/* 上段：商品名と金額を横並び */
.top-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.cart-info .name {
    font-weight: bold;
    margin: 0;
}

.top-row .price {
    font-weight: bold;
    font-size: 24px;
}

/* 下段：個数と削除ボタン */
.bottom-row {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 10px;
}

.qty-form select {
    padding: 4px;
}

.cart-summary {
    flex: 1;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 20px;
    background: #fff;
    height: fit-content;
}

.cart-summary .total {
    color: #ff4a4a;
    font-size: 20px;
}

.buy-btn {
    width: 100%;
    padding: 10px 0;
    background: #ff8c8c;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
}

.buy-btn:hover {
    background: #ff6b6b;
}
</style>

<?php include __DIR__ . '/footer.php'; ?>
</body>    
</html>