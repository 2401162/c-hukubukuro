<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'header.php';
require_once 'db-connect.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = null;
$reviews = [];
$error_message = '';

if ($product_id <= 0) {
    $error_message = '不正な商品IDです。';
} else {
    try {
        $pdo = new PDO(
            $connect,
            USER,
            PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
        
        // product テーブル: product_id (PK), jenre_id, name, price, stock, description, is_active
    $stmt = $pdo->prepare(
        "SELECT p.product_id, p.name, p.price, p.stock, p.description, p.is_active, g.genre_name,
            COUNT(r.review_id) AS review_count, ROUND(AVG(r.rating), 1) AS avg_rating
         FROM product p
         LEFT JOIN genre g ON p.jenre_id = g.genre_id
         LEFT JOIN order_item oi ON oi.product_id = p.product_id
         LEFT JOIN review r ON r.order_item_id = oi.order_item_id AND r.is_active = 1
         WHERE p.product_id = :product_id AND p.is_active = 1
         GROUP BY p.product_id, p.name, p.price, p.stock, p.description, p.is_active, g.genre_name"
    );
        $stmt->execute([':product_id' => $product_id]);
        $product = $stmt->fetch();
        
        if (!$product) {
            $error_message = '商品が見つかりません。';
        } else {
            // レビュー一覧取得
            $reviewStmt = $pdo->prepare(
                "SELECT r.review_id, r.rating, r.comment, r.created_at, oi.quantity, o.customer_id
                 FROM review r
                 JOIN order_item oi ON r.order_item_id = oi.order_item_id
                 JOIN orders o ON oi.order_id = o.order_id
                 WHERE oi.product_id = :product_id AND r.is_active = 1
                 ORDER BY r.created_at DESC
                 LIMIT 10"
            );
            $reviewStmt->execute([':product_id' => $product_id]);
            $reviews = $reviewStmt->fetchAll();
        }
    } catch (PDOException $e) {
        error_log("Product Detail DB Error: " . $e->getMessage());
        $error_message = 'データベースエラーが発生しました。';
    }
}

function h($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

// 商品IDがあるなら画像番号を振り分け
$image_num = $product_id > 0 ? (($product_id - 1) % 3) + 1 : 1;
$image_path = 'images/sample' . $image_num . '.jpg';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $product ? h($product['name']) : '商品詳細' ?></title>
    <style>
        .breadcrumb { max-width: 1200px; margin: 8px auto 0; padding: 0 12px; color: #777; font-size: 12px; }
        .container { max-width: 1200px; margin: 24px auto; padding: 0 12px; }
        .detail-wrap { display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 40px; }
        .thumb { width: 100%; aspect-ratio: 1/1; object-fit: cover; border-radius: 8px; background: #f5f5f5; }
        .info h1 { margin: 0 0 16px; font-size: 24px; font-weight: 700; color: #333; }
        .genre { font-size: 12px; color: #777; margin-bottom: 8px; }
        .price { font-size: 28px; font-weight: 700; color: #e43131; margin: 16px 0; }
        .rating { font-size: 14px; color: #f90; margin-bottom: 16px; }
        .stock { margin: 16px 0; font-size: 14px; }
        .stock.in-stock { color: #28a745; }
        .stock.low-stock { color: #ffc107; }
        .stock.out-of-stock { color: #dc3545; }
        .description { margin: 24px 0; line-height: 1.6; color: #333; padding: 16px; background: #f9f9f9; border-radius: 6px; }
        .actions { display: flex; gap: 12px; margin-top: 24px; }
        .btn { padding: 12px 24px; border: none; border-radius: 6px; cursor: pointer; font-size: 15px; font-weight: 600; }
        .btn-primary { background: #e43131; color: #fff; flex: 1; }
        .btn-secondary { background: #333; color: #fff; flex: 1; }
        .reviews { margin-top: 48px; border-top: 2px solid #eee; padding-top: 24px; }
        .review-item { margin: 16px 0; padding: 16px; background: #f9f9f9; border-radius: 6px; border-left: 3px solid #e43131; }
        .review-rating { font-size: 14px; color: #f90; font-weight: 700; }
        .review-comment { margin-top: 8px; color: #333; }
        .review-date { font-size: 12px; color: #777; margin-top: 8px; }
        .error { color: #dc3545; font-size: 16px; padding: 16px; background: #f8d7da; border-radius: 6px; }
        @media (max-width: 768px) {
            .detail-wrap { grid-template-columns: 1fr; gap: 24px; }
            .price { font-size: 24px; }
            .actions { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="breadcrumb">ホーム &gt; <a href="product-list.php">商品一覧</a> &gt; <?= $product ? h($product['name']) : '商品詳細' ?></div>

    <div class="container">
        <?php if ($error_message): ?>
            <div class="error"><?= h($error_message) ?></div>
            <p style="text-align: center; margin-top: 24px;"><a href="product-list.php">商品一覧に戻る</a></p>
        <?php elseif ($product): ?>
            <div class="detail-wrap">
                <div>
                    <img class="thumb" src="<?= h($image_path) ?>" alt="<?= h($product['name']) ?>">
                </div>

                <div class="info">
                    <?php if ($product['genre_name']): ?>
                        <div class="genre">ジャンル: <?= h($product['genre_name']) ?></div>
                    <?php endif; ?>
                    
                    <h1><?= h($product['name']) ?></h1>

                    <?php if ($product['review_count'] > 0): ?>
                        <div class="rating">
                            ★<?= h($product['avg_rating']) ?> (<?= h($product['review_count']) ?>件のレビュー)
                        </div>
                    <?php endif; ?>

                    <div class="price">¥<?= number_format($product['price']) ?></div>

                    <div class="stock <?= $product['stock'] > 10 ? 'in-stock' : ($product['stock'] > 0 ? 'low-stock' : 'out-of-stock') ?>">
                        <?php if ($product['stock'] > 0): ?>
                            在庫あり: <?= h($product['stock']) ?>個
                        <?php else: ?>
                            在庫なし
                        <?php endif; ?>
                    </div>

                    <?php if ($product['description']): ?>
                        <div class="description">
                            <?= nl2br(h($product['description'])) ?>
                        </div>
                    <?php endif; ?>

                    <div class="actions">
                        <?php if ($product['stock'] > 0): ?>
                            <button class="btn btn-primary" onclick="addToCart(<?= h($product['product_id']) ?>)">カートに追加</button>
                        <?php else: ?>
                            <button class="btn btn-primary" disabled>売り切れ</button>
                        <?php endif; ?>
                        <button class="btn btn-secondary" onclick="history.back()">戻る</button>
                    </div>
                </div>
            </div>

            <!-- レビューセクション -->
            <?php if (!empty($reviews)): ?>
                <div class="reviews">
                    <h2>カスタマーレビュー</h2>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-item">
                            <div class="review-rating">★<?= h($review['rating']) ?></div>
                            <?php if ($review['comment']): ?>
                                <div class="review-comment"><?= nl2br(h($review['comment'])) ?></div>
                            <?php endif; ?>
                            <div class="review-date"><?= date('Y年m月d日', strtotime($review['created_at'])) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="error">商品情報を読み込めません。</div>
        <?php endif; ?>
    </div>

    <script>
        function addToCart(productId) {
            // TODO: カートに追加する機能を実装
            alert('カートに追加しました（開発中）');
        }
    </script>

    <?php
    $footerPath = __DIR__ . '/footer.php';
    if (is_file($footerPath)) { require $footerPath; }
    ?>
</body>
</html>
