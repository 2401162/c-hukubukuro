<?php
require 'admin-db-connect.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!$pdo || !$input || !isset($input['name'], $input['jenre_id'], $input['price'], $input['stock'], $input['description'], $input['is_active'])) {
  echo json_encode(['success' => false, 'message' => '不正な入力です']);
  exit;
}

$image_path = isset($input['image_path']) ? $input['image_path'] : null;

$sql = $pdo->prepare('INSERT INTO product (jenre_id, name, price, stock, description, is_active, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)');
$success = $sql->execute([
  $input['jenre_id'],
  $input['name'],
  $input['price'],
  $input['stock'],
  $input['description'],
  $input['is_active'],
  $image_path
]);

if ($success) {
  $input['product_id'] = $pdo->lastInsertId();
  echo json_encode(['success' => true, 'product' => $input]);
} else {
  echo json_encode(['success' => false, 'message' => 'データベース登録に失敗しました']);
}
?>