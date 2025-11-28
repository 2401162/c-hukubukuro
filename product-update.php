<?php
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

$product_id = (int)($input['product_id'] ?? 0);
$name = $input['name'] ?? null;
$jenre_id = (int)($input['jenre_id'] ?? 0);
$price = (int)($input['price'] ?? 0);
$stock = (int)($input['stock'] ?? 0);
$description = $input['description'] ?? '';
$is_active = (int)($input['is_active'] ?? 1);
$image_path = $input['image_path'] ?? null;

if ($product_id <= 0 || !$name || $price < 0 || $stock < 0) {
  http_response_code(400);
  echo json_encode(['success' => false, 'error' => '必須項目が不足しています']);
  exit;
}

try {
  // 更新前に現在のデータを取得（売上情報を保持するため）
  $getStmt = $pdo->prepare("
    SELECT p.*, COALESCE(SUM(oi.quantity), 0) AS total_sold, COALESCE(SUM(oi.quantity * oi.unit_price), 0) AS total_revenue
    FROM product p
    LEFT JOIN order_item oi ON oi.product_id = p.product_id
    WHERE p.product_id = :product_id
    GROUP BY p.product_id
  ");
  $getStmt->execute([':product_id' => $product_id]);
  $current = $getStmt->fetch(PDO::FETCH_ASSOC);

  if (!$current) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => '商品が見つかりません']);
    exit;
  }

  // image_path が null でなければ更新、null なら元の値を保持
  if ($image_path === null) {
    $image_path = $current['image_path'];
  }

  $sql = "
    UPDATE product 
    SET name = :name, 
        jenre_id = :jenre_id, 
        price = :price, 
        stock = :stock, 
        description = :description, 
        image_path = :image_path, 
        is_active = :is_active, 
        updated_at = NOW()
    WHERE product_id = :product_id
  ";
  $stmt = $pdo->prepare($sql);
  $result = $stmt->execute([
    ':product_id' => $product_id,
    ':name' => $name,
    ':jenre_id' => $jenre_id,
    ':price' => $price,
    ':stock' => $stock,
    ':description' => $description,
    ':image_path' => $image_path,
    ':is_active' => $is_active
  ]);

  if ($result) {
    echo json_encode([
      'success' => true,
      'product' => [
        'product_id' => $product_id,
        'name' => $name,
        'jenre_id' => $jenre_id,
        'price' => $price,
        'stock' => $stock,
        'description' => $description,
        'image_path' => $image_path,
        'is_active' => $is_active,
        'created_at' => $current['created_at'],
        'total_sold' => (int)($current['total_sold'] ?? 0),
        'total_revenue' => (int)($current['total_revenue'] ?? 0)
      ]
    ], JSON_UNESCAPED_UNICODE);
  } else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => '更新に失敗しました']);
  }
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
exit;
?>