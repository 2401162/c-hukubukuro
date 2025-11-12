<?php require 'admin-db-connect.php'; ?>

<?php
$sql = 'UPDATE review SET rating = ?, comment = ?, updated_at = CURRENT_TIMESTAMP WHERE review_id = ?';
$stmt = $pdo->prepare($sql);
$stmt->execute([$_POST['rating'], $_POST['comment'], $_POST['review_id']]);

echo 'レビューを更新しました。<a href="admin-review.php">戻る</a>';
?>
