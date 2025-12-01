<?php
require 'admin-db-connect.php';
header('Content-Type: application/json; charset=utf-8');

if (!$pdo) {
  http_response_code(500);
  echo json_encode(['success' => false, 'error' => 'データベース接続失敗']);
  exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
  http_response_code(400);
  echo json_encode(['success' => false, 'error' => '無効なリクエスト']);
  exit;
}

$name = $input['name'] ?? null;
$jenre_id = (int)($input['jenre_id'] ?? 0);
$price = (int)($input['price'] ?? 0);
$stock = (int)($input['stock'] ?? 0);
$description = $input['description'] ?? '';
$is_active = (int)($input['is_active'] ?? 1);
$image_path = $input['image_path'] ?? null;

if (!$name || $price < 0 || $stock < 0) {
  http_response_code(400);
  echo json_encode(['success' => false, 'error' => '必須項目が不足しています']);
  exit;
}

try {
  $sql = "
    INSERT INTO product (name, jenre_id, price, stock, description, image_path, is_active, created_at, updated_at)
    VALUES (:name, :jenre_id, :price, :stock, :description, :image_path, :is_active, NOW(), NOW())
  ";
  $stmt = $pdo->prepare($sql);
  $result = $stmt->execute([
    ':name' => $name,
    ':jenre_id' => $jenre_id,
    ':price' => $price,
    ':stock' => $stock,
    ':description' => $description,
    ':image_path' => $image_path,
    ':is_active' => $is_active
  ]);

  if ($result) {
    $product_id = $pdo->lastInsertId();
    echo json_encode([
      'success' => true,
      'product' => [
        'product_id' => (int)$product_id,
        'name' => $name,
        'jenre_id' => $jenre_id,
        'price' => $price,
        'stock' => $stock,
        'description' => $description,
        'image_path' => $image_path,
        'is_active' => $is_active,
        'created_at' => date('Y-m-d H:i:s'),
        'total_sold' => 0,
        'total_revenue' => 0
      ]
    ], JSON_UNESCAPED_UNICODE);
  } else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => '挿入に失敗しました']);
  }
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
exit;
?>