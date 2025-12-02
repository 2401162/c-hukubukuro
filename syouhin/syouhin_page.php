<?php require_once __DIR__ . '/../db-connect.php'; ?>
<?php
try {
    $pdo = new PDO($connect, USER, PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    echo '<p>DB接続エラー</p>';
    exit;
}

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare('SELECT * FROM product WHERE product_id=?');
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo '<p>商品が見つかりません。</p>';
    exit;
}

function resolve_image_path(array $item): string {
    $img = $item['image_path'] ?? '';
    if (!$img) return 'img/noimage.png';
    if (preg_match('#^(https?://|//|/)#i', $img)) return $img;
    if (strpos($img, 'uploads/') === 0) return $img;
    return 'uploads/' . ltrim($img, '/');
}

$avgStmt = $pdo->prepare('
    SELECT 
        AVG(r.rating) AS avg_rating,
        COUNT(r.review_id) AS review_count
    FROM review r
    JOIN order_item oi ON r.order_item_id = oi.order_item_id
    JOIN orders o ON oi.order_id = o.order_id
    WHERE oi.product_id = ? AND r.is_active = 1
');
$avgStmt->execute([$product['product_id']]);
$avgResult = $avgStmt->fetch();

$avgRating = floor($avgResult['avg_rating']);
$reviewCount = (int)$avgResult['review_count'];

$fullStars = $avgRating;
$emptyStars = 5 - $fullStars;
$starsDisplay = str_repeat('★', $fullStars) . str_repeat('☆', $emptyStars);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>福袋サイト  商品詳細</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php require __DIR__ . '/../header.php'; ?>

<div class="syouhin">
    
    <div class="product">
        <p><img alt="image" src="../image/<?= $product['product_id'] ?>.png"></p>
        <form action="../cart.php" method="post">
            <h3><?= htmlspecialchars($product['name'], ENT_QUOTES) ?></h3>
            <p class="price">￥<?= number_format($product['price']) ?><small>税込み</small></p>

            <p class="average-rating">
                <span class="stars"><?= $starsDisplay ?></span>
                <span class="review-count"><small>(<?= $reviewCount ?>件のレビュー)</small></span>
            </p>

            <?php if ($product['stock'] > 0): ?>
                個数:
                <select name="quantity">
                    <?php for ($i = 1; $i <= $product['stock']; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                    <?php endfor; ?>
                </select>
                <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                <input type="hidden" name="action" value="add">
                <p>
                    <button type="button" onclick="addToCart(<?= $product['product_id'] ?>)">
                        カートに追加
                    </button>
                </p>
            <?php else: ?>
                <p style="color:green;">在庫切れ</p>
            <?php endif; ?>
        </form>
    </div>

    <div class="description">
        <p>商品説明</p>
        <hr>
        <p><?= nl2br(htmlspecialchars($product['description'], ENT_QUOTES)) ?></p>
    </div>

    <?php
    // レビュー取得
    $sql = $pdo->prepare('
        SELECT 
            r.review_id,
            r.rating,
            r.comment,
            r.created_at,
            c.name AS customer_name
        FROM review r
        JOIN order_item oi ON r.order_item_id = oi.order_item_id
        JOIN orders o ON oi.order_id = o.order_id
        JOIN customer c ON o.customer_id = c.customer_id
        WHERE oi.product_id = ? AND r.is_active = 1
        ORDER BY r.created_at DESC
    ');
    $sql->execute([$product['product_id']]);
    $reviews = $sql->fetchAll();
    ?>

    <div class="description">
        <p>レビュー</p>
        <hr>
        <?php if (count($reviews) > 0): ?>
            <?php foreach ($reviews as $review): 
                $name = htmlspecialchars($review['customer_name'] ?? '匿名', ENT_QUOTES);
                $rating = max(0, min(5, (int)$review['rating']));
                $stars = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
                $comment = nl2br(htmlspecialchars($review['comment'], ENT_QUOTES));
                $date = htmlspecialchars($review['created_at'], ENT_QUOTES);
            ?>
                <div class="review-box">
                    <div class="review-header"><strong><?= $name ?></strong> <span class="stars"><?= $stars ?></span></div>
                    <p class="review-comment"><?= $comment ?></p>
                    <small class="review-date"><?= $date ?></small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="review-box no-reviews">
                <p>レビューはまだありません。</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function addToCart(productId) {
        // 選択された数量を取得
        const quantity = document.querySelector('select[name="quantity"]').value;

        fetch('/2025/prac/cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=add&product_id=' + productId + '&quantity=' + quantity
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('カートに追加しました');
                if (confirm('カートを確認しますか？')) {
                    window.location.href = '../cart.php';
                }
            } else {
                alert(data.message || 'カートに追加できませんでした');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('エラーが発生しました');
        });
    }
</script>

<?php require __DIR__ . '/style.php'; ?>
<?php require __DIR__ . '/../footer.php'; ?>
</body>
</html>
