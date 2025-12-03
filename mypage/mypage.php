<?php
session_start();
require __DIR__ . '/../db-connect.php';

// ログインチェック
if (!isset($_SESSION['customer'])) {
    // 未ログインなら login.php にリダイレクト
    header('Location: /rogin-input.php');
    exit;
}

$customer_id = $_SESSION['customer']['customer_id'];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>福袋サイト　購入履歴</title>
</head>
<body>
<?php require __DIR__ . '/../header.php'; ?>
<h2>購入履歴</h2>
<div class="buy">
<div class="rireki">

<?php
try {
    $pdo = new PDO($connect, USER, PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    $sql = "
        SELECT 
            o.order_id,
            o.created_at AS order_date,
            oi.order_item_id,
            p.product_id,
            p.name AS product_name,
            oi.quantity,
            oi.unit_price,
            oi.subtotal,
            r.review_id
        FROM orders o
        JOIN order_item oi 
            ON o.order_id = oi.order_id
        JOIN product p 
            ON oi.product_id = p.product_id
        LEFT JOIN review r 
            ON r.order_item_id = oi.order_item_id
        WHERE o.customer_id = ?
        ORDER BY o.created_at DESC;
    ";

    

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$customer_id]);
    $orders = $stmt->fetchAll();

    $weekdays = ['日', '月', '火', '水', '木', '金', '土'];

    if (empty($orders)) {
        echo '<h3>購入履歴がありません</h3>';
    } else {
        foreach ($orders as $order) {
            echo '<div class="purchase-card">';
            echo '<div class="card-image">';
            echo '<img alt="image" src="image/'.$order['product_id'].'.png" class="order_image">';
            echo '</div>';

            echo '<div class="card-main">';
            echo '<p class="product-name">'.htmlspecialchars($order['product_name']).'</p>';

            $orderDate = new DateTime($order['order_date']);
            $formattedDate = $orderDate->format('Y年n月j日') . $weekdays[$orderDate->format('w')] . '曜日に購入済み';
            echo '<p class="order-date">'.htmlspecialchars($formattedDate).'</p>';

            echo '<p class="quantity">数量: '.htmlspecialchars($order['quantity']).'</p>';
            echo '</div>';

            echo '<div class="right">';
            echo '<h3>'.number_format($order['subtotal']).'円</h3>';

            if ($order['review_id']) {
                echo '<a href="/2025/prac/review.php?order_item_id=' . $order['order_item_id'] . '">レビュー再投稿</a>';
            } else {
                echo '<a href="/2025/prac/review.php?order_item_id=' . $order['order_item_id'] . '">レビューを書く</a>';
            }

            echo '</div>';
            echo '</div>';
        }
    }

} catch (PDOException $e) {
    echo '<p>DB接続エラー: '.$e->getMessage().'</p>';
}
?>

</div>
</div>

<?php require __DIR__ . '/style2.php'; ?>
<?php require __DIR__ . '/../footer.php'; ?>
</body>
</html>
