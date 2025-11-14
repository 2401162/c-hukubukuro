<?php
$uploadDir = __DIR__ . '/uploads/';
$publicBase = 'uploads/';

if (!is_dir($uploadDir)) {
  mkdir($uploadDir, 0755, true);
}

header('Content-Type: application/json; charset=utf-8');

if (!isset($_FILES['image'])) {
  echo json_encode(['success' => false, 'error' => 'ファイルが送信されていません。']);
  exit;
}

$file = $_FILES['image'];
if ($file['error'] !== UPLOAD_ERR_OK) {
  echo json_encode(['success' => false, 'error' => 'アップロードエラー']);
  exit;
}

if ($file['size'] > 5 * 1024 * 1024) {
  echo json_encode(['success' => false, 'error' => 'ファイルが大きすぎます。']);
  exit;
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);
$allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
if (!isset($allowed[$mime])) {
  echo json_encode(['success' => false, 'error' => '許可されていないファイル形式です。']);
  exit;
}

$ext = $allowed[$mime];
$filename = uniqid('pimg_', true) . '.' . $ext;
$dest = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
  echo json_encode(['success' => false, 'error' => 'ファイル保存に失敗しました。']);
  exit;
}

echo json_encode(['success' => true, 'path' => $publicBase . $filename]);
exit;
?>