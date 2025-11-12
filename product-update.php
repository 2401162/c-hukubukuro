<?php
require 'admin-db-connect.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!$pdo || !$input || !isset($input['product_id'], $input['name'], $input['jenre_id'], $input['price'], $input['stock'], $input['description'], $input['is_active'])) {
  echo json_encode(['success' => false, 'message' => '不正な入力です']);
  exit;
}

$sql = $pdo->prepare('UPDATE product SET jenre_id=?, name=?, price=?, stock=?, description=?, is_active=? WHERE product_id=?');
$success = $sql->execute([
  $input['jenre_id'],
  $input['name'],
  $input['price'],
  $input['stock'],
  $input['description'],
  $input['is_active'],
  $input['product_id']
]);

echo json_encode(['success' => $success]);
?>
