<?php
require_once __DIR__ . '/db-connect.php';

// 出力を簡潔に切替可能 (?format=json)
$format = isset($_GET['format']) && $_GET['format'] === 'json' ? 'json' : 'html';

try {
		$pdo = new PDO(
				$connect,
				USER,
				PASS,
				[
						PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
						PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				]
		);
} catch (PDOException $e) {
		if ($format === 'json') {
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode(['error' => 'DB接続エラー', 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
		} else {
				echo '<!doctype html><meta charset="utf-8"><h1>DB接続エラー</h1><pre>' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</pre>';
		}
		exit;
}

// テーブル一覧取得
$tablesStmt = $pdo->prepare("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = :schema ORDER BY TABLE_NAME");
$tablesStmt->execute([':schema' => DBNAME]);
$tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);

$result = [];
foreach ($tables as $table) {
		$colsStmt = $pdo->prepare(
				"SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_KEY, COLUMN_DEFAULT, EXTRA, ORDINAL_POSITION
				 FROM INFORMATION_SCHEMA.COLUMNS
				 WHERE TABLE_SCHEMA = :schema AND TABLE_NAME = :table
				 ORDER BY ORDINAL_POSITION"
		);
		$colsStmt->execute([':schema' => DBNAME, ':table' => $table]);
		$cols = $colsStmt->fetchAll();

		$result[$table] = $cols;
}

if ($format === 'json') {
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		exit;
}

// HTML 出力
?>
<!doctype html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>DB 接続テスト — テーブル/カラム一覧</title>
	<style>
		body{font-family:system-ui, -apple-system, "Segoe UI", Roboto, "Hiragino Kaku Gothic ProN", "Meiryo", sans-serif; padding:20px}
		h1{margin-top:0}
		table{border-collapse:collapse; width:100%; margin-bottom:24px}
		th,td{border:1px solid #ddd; padding:8px; text-align:left}
		th{background:#f5f5f5}
		.table-name{margin:24px 0 8px}
		.small{font-size:0.9em; color:#555}
	</style>
</head>
<body>
	<h1>DB 接続テスト — テーブル / カラム一覧</h1>
	<p class="small">スキーマ: <?php echo htmlspecialchars(DBNAME, ENT_QUOTES, 'UTF-8'); ?> — テーブル数: <?php echo count($tables); ?> | <a href="?format=json">JSON 出力</a></p>

	<?php if (empty($tables)): ?>
		<p>テーブルが見つかりませんでした。</p>
	<?php endif; ?>

	<?php foreach ($result as $tableName => $columns): ?>
		<div class="table-name"><strong>テーブル:</strong> <?php echo htmlspecialchars($tableName, ENT_QUOTES, 'UTF-8'); ?></div>
		<table>
			<thead>
				<tr>
					<th>#</th>
					<th>カラム名</th>
					<th>型</th>
					<th>NULL</th>
					<th>キー</th>
					<th>デフォルト</th>
					<th>Extra</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($columns as $col): ?>
				<tr>
					<td><?php echo htmlspecialchars($col['ORDINAL_POSITION'], ENT_QUOTES, 'UTF-8'); ?></td>
					<td><?php echo htmlspecialchars($col['COLUMN_NAME'], ENT_QUOTES, 'UTF-8'); ?></td>
					<td><?php echo htmlspecialchars($col['COLUMN_TYPE'], ENT_QUOTES, 'UTF-8'); ?></td>
					<td><?php echo htmlspecialchars($col['IS_NULLABLE'], ENT_QUOTES, 'UTF-8'); ?></td>
					<td><?php echo htmlspecialchars($col['COLUMN_KEY'], ENT_QUOTES, 'UTF-8'); ?></td>
					<td><?php echo htmlspecialchars($col['COLUMN_DEFAULT'], ENT_QUOTES, 'UTF-8'); ?></td>
					<td><?php echo htmlspecialchars($col['EXTRA'], ENT_QUOTES, 'UTF-8'); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php endforeach; ?>

</body>
</html>

