<?php
require 'admin-db-connect.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!$pdo || !$input || !isset($input['product_id'], $input['name'], $input['jenre_id'], $input['price'], $input['stock'], $input['description'], $input['is_active'])) {
  echo json_encode(['success' => false, 'message' => '不正な入力です']);
  exit;
}

$image_path = isset($input['image_path']) ? $input['image_path'] : null;

$sql = $pdo->prepare('UPDATE product SET jenre_id=?, name=?, price=?, stock=?, description=?, is_active=?, image_path=? WHERE product_id=?');
$success = $sql->execute([
  $input['jenre_id'],
  $input['name'],
  $input['price'],
  $input['stock'],
  $input['description'],
  $input['is_active'],
  $image_path,
  $input['product_id']
]);

if ($success) {
  echo json_encode(['success' => true, 'product' => $input]);
} else {
  echo json_encode(['success' => false, 'message' => '更新に失敗しました']);
}
?>