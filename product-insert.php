<?php
require 'admin-db-connect.php';
header('Content-Type: application/json');

// JSONデータを受け取る
$input = json_decode(file_get_contents('php://input'), true);

// 必須項目が揃っているかチェック
if (!$pdo || !$input || !isset($input['name'], $input['jenre_id'], $input['price'], $input['stock'], $input['description'], $input['is_active'])) {
  echo json_encode(['success' => false, 'message' => '不正な入力です']);
  exit;
}

// SQL実行
$sql = $pdo->prepare('INSERT INTO product (jenre_id, name, price, stock, description, is_active) VALUES (?, ?, ?, ?, ?, ?)');
$success = $sql->execute([
  $input['jenre_id'],
  $input['name'],
  $input['price'],
  $input['stock'],
  $input['description'],
  $input['is_active']
]);

// 結果を返す
if ($success) {
  $input['product_id'] = $pdo->lastInsertId(); // 新しいIDを取得
  echo json_encode(['success' => true, 'product' => $input]);
} else {
  echo json_encode(['success' => false, 'message' => 'データベース登録に失敗しました']);
}
?>
