<?php
<?php
require 'admin-db-connect.php';
header('Content-Type: application/json');

if (!isset($_POST['review_id'])) {
  echo json_encode(['success' => false, 'message' => 'レビューIDが指定されていません']);
  exit;
}

$review_id = $_POST['review_id'];

$sql = $pdo->prepare('UPDATE review SET is_active = 0 WHERE review_id = ?');
$success = $sql->execute([$review_id]);

if ($success) {
  echo json_encode(['success' => true, 'message' => '削除しました']);
} else {
  echo json_encode(['success' => false, 'message' => '削除に失敗しました']);
}
?>