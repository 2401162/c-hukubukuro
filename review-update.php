<?php
require 'admin-db-connect.php';
header('Content-Type: application/json');

if (!isset($_POST['review_id'], $_POST['rating'], $_POST['comment'])) {
  echo json_encode(['success' => false, 'message' => '不正な入力です']);
  exit;
}

$sql = 'UPDATE review SET rating = ?, comment = ?, updated_at = CURRENT_TIMESTAMP WHERE review_id = ?';
$stmt = $pdo->prepare($sql);
$success = $stmt->execute([$_POST['rating'], $_POST['comment'], $_POST['review_id']]);

if ($success) {
  echo json_encode(['success' => true, 'message' => 'レビューを更新しました']);
} else {
  echo json_encode(['success' => false, 'message' => '更新に失敗しました']);
}
?>