<?php
// 画像アップロード＋サムネイル生成（uploads/ 配下に保存）
// 戻り値: JSON { success, path, thumb_path, filename }
$uploadDir = __DIR__ . '/uploads/';
$thumbDir  = $uploadDir . 'thumbs/';
$publicBase = 'uploads/';
$thumbBase  = 'uploads/thumbs/';

if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
}
if (!is_dir($thumbDir)) {
    @mkdir($thumbDir, 0755, true);
}

// セキュリティ用 .htaccess（あれば無視）
if (!is_file($uploadDir . '.htaccess')) {
    @file_put_contents($uploadDir . '.htaccess', "Options -Indexes\n<FilesMatch \"\\.php$\">\n  Deny from all\n</FilesMatch>\n");
}
if (!is_file($thumbDir . '.htaccess')) {
    @file_put_contents($thumbDir . '.htaccess', "Options -Indexes\n");
}

header('Content-Type: application/json; charset=utf-8');

if (!isset($_FILES['image']) || !is_uploaded_file($_FILES['image']['tmp_name'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ファイルが送信されていません。']);
    exit;
}

$file = $_FILES['image'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'アップロードエラー']);
    exit;
}

// サイズ制限 5MB
if ($file['size'] > 5 * 1024 * 1024) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ファイルは5MB以下にしてください。']);
    exit;
}

// MIME 確認
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

$allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
if (!isset($allowed[$mime])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => '許可されていない画像形式です（JPEG/PNG/GIF/WebP）。']);
    exit;
}

$ext = $allowed[$mime];
$basename = uniqid('pimg_', true);
$filename = $basename . '.' . $ext;
$dest = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'ファイル保存に失敗しました。']);
    exit;
}
@chmod($dest, 0644);

// サムネイル生成（最大300px）
$thumbFilename = $basename . '_thumb.' . $ext;
$thumbPath = $thumbDir . $thumbFilename;
$thumbGenerated = false;

if (function_exists('imagecreatefromstring') && @file_exists($dest)) {
    $srcImg = @imagecreatefromstring(file_get_contents($dest));
    if ($srcImg !== false) {
        $w = imagesx($srcImg);
        $h = imagesy($srcImg);
        $maxSize = 300;
        if ($w > 0 && $h > 0) {
            $ratio = min($maxSize / $w, $maxSize / $h, 1);
            $tw = max(1, (int)($w * $ratio));
            $th = max(1, (int)($h * $ratio));
            $thumb = imagecreatetruecolor($tw, $th);
            if ($ext !== 'jpg') {
                imagealphablending($thumb, false);
                imagesavealpha($thumb, true);
                $transparent = imagecolorallocatealpha($thumb, 0, 0, 0, 127);
                imagefilledrectangle($thumb, 0, 0, $tw, $th, $transparent);
            }
            imagecopyresampled($thumb, $srcImg, 0, 0, 0, 0, $tw, $th, $w, $h);
            switch ($ext) {
                case 'jpg': imagejpeg($thumb, $thumbPath, 86); break;
                case 'png': imagepng($thumb, $thumbPath, 6); break;
                case 'webp': imagewebp($thumb, $thumbPath, 80); break;
                default: imagegif($thumb, $thumbPath);
            }
            imagedestroy($thumb);
            imagedestroy($srcImg);
            @chmod($thumbPath, 0644);
            $thumbGenerated = true;
        }
    }
}

$response = [
    'success' => true,
    'path' => $publicBase . $filename,
    'thumb_path' => $thumbGenerated ? ($thumbBase . $thumbFilename) : null,
    'filename' => $filename
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;
?>