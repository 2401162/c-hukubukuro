<?php require 'admin-db-connect.php'; ?>

<?php
$sql = 'UPDATE review SET is_active = 0 WHERE review_id = ?';
$stmt = $pdo->prepare($sql);
$stmt->execute([$_POST['review_id']]);

echo 'レビューを削除しました（非表示にしました）。<a href="admin-review.php">戻る</a>';
?>
