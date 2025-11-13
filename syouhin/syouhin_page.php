<?php require 'db-connect.php'; ?>
<?php require 'login.php'; ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>福袋サイト</title>
</head>
<body>
<?php require 'header.php'; ?>
<div class="syouhin">
<?php
$pdo = new PDO($connect, USER, PASS);

$stmt = $pdo->prepare('SELECT * FROM product WHERE product_id=?');
$stmt->execute([$_REQUEST['product_id']]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

$avgStmt = $pdo->prepare('
    SELECT 
        AVG(r.rating) AS avg_rating,
        COUNT(r.review_id) AS review_count
    FROM review r
    JOIN orders_item oi ON r.order_item_id = oi.order_item_id
    JOIN orders o ON oi.order_id = o.order_id
    WHERE oi.order_datetime = ? AND r.is_active = 1
');
$avgStmt->execute([$product['product_id']]);
$avgResult = $avgStmt->fetch(PDO::FETCH_ASSOC);

$avgRating = floor($avgResult['avg_rating']);
$reviewCount = (int)$avgResult['review_count'];

$fullStars = $avgRating;
$emptyStars = 5 - $fullStars;

$starsDisplay = str_repeat('★', $fullStars) . str_repeat('☆', $emptyStars);

if ($product) {
    echo '<div class="product">';
    echo '<p><img alt="image" src="image/'.$product['product_id'].'.png"></p>';
    echo '<form action="cart.php" method="post">';
    echo '<h3>'.$product['name'].'</h3>';
    echo '<p>￥'.$product['price'].'<small>税込み</small></p>';

    echo '<p class="average-rating">';
    echo '<span class="stars">'.$starsDisplay.'</span> ';
    echo '<span class="review-count">(<small>'.$reviewCount.'</small>件のレビュー)</span>';
    echo '</p>';

    if ($product['stock'] > 0) {
        echo '個数<br><select name="count">';
        for ($i = 1; $i <= $product['stock']; $i++) {
            echo '<option value="'.$i.'">'.$i.'</option>';
        }
        echo '</select>';
        echo '<p><input type="submit" value="カートに追加"></p>';
    } else {
        echo '<p style="color:green;">在庫切れ</p>';
    }

    echo '<input type="hidden" name="id" value="'.$product['product_id'].'">';
    echo '<input type="hidden" name="name" value="'.$product['name'].'">';
    echo '<input type="hidden" name="price" value="'.$product['price'].'">';
    echo '</form>';
    echo '</div>';

    echo '<div class="description">';
    echo '<p>商品説明</p><hr>';
    echo '<p>'.$product['description'].'</p>';
    echo '</div>';

    $sql = $pdo->prepare('
    SELECT 
        r.review_id,
        r.rating,
        r.comment,
        r.created_at,
        c.name AS customer_name
    FROM review r
    JOIN orders_item oi ON r.order_item_id = oi.order_item_id
    JOIN orders o ON oi.order_id = o.order_id
    JOIN customer c ON o.customer_id = c.customer_id
    WHERE oi.order_datetime = ? AND r.is_active = 1
    ORDER BY r.created_at DESC
');
$sql->execute([$product['product_id']]);
$reviews = $sql->fetchAll();



    echo '<div class="description">';
    echo '<p>レビュー</p><hr>';

    if (count($reviews) > 0) {
        foreach ($reviews as $review) {
            $name = htmlspecialchars($review['customer_name'] ?? '匿名', ENT_QUOTES);
            $rating = (int)$review['rating'];
            $rating = max(0, min(5, $rating));
            $stars = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
            $comment = nl2br(htmlspecialchars($review['comment'], ENT_QUOTES));
            $date = htmlspecialchars($review['created_at'], ENT_QUOTES);

            echo '<div class="review-box">';
            echo '<div class="review-header"><strong>'.$name.'</strong>';
            echo '<span class="stars">'.$stars.'</span></div>';
            echo '<p class="review-comment">'.$comment.'</p>';
            echo '<small class="review-date">'.$date.'</small>';
            echo '</div>';
        }
    } else {
        echo '<div class="review-box no-reviews">';
        echo '<p>レビューはまだありません。</p>';
        echo '</div>';
    }
    echo '</div>';
} 
?>
</div>
<?php require 'style.php'; ?>
</body>
</html>
