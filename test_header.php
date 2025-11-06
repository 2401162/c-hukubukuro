<?php
// 簡易確認ページ：このファイルをプロジェクトルートで `php -S localhost:8000` してブラウザで開いてください。
?>
<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>ヘッダーテスト</title>
</head>
<body>
  <?php include __DIR__ . '/header.php'; ?>
  <main style="padding:20px;">
    <h1>ヘッダー確認</h1>
    <p>アイコンのみ表示（マイページ・カート）になっているか確認してください。</p>
  </main>
</body>
</html>