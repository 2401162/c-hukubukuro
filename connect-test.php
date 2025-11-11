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

	<?php
	// テーブルごとの件数を取得（負荷が高い場合は無効化可能）
	$tableCounts = [];
	try {
		foreach ($tables as $t) {
			$cstmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM `" . str_replace('`', '', $t) . "`");
			$cstmt->execute();
			$r = $cstmt->fetch(PDO::FETCH_ASSOC);
			$tableCounts[$t] = isset($r['cnt']) ? (int)$r['cnt'] : 0;
		}
	} catch (PDOException $e) {
		// 件数取得でエラーが出ても一覧表示は続ける
		error_log('Count error: ' . $e->getMessage());
	}

	// 表示するテーブル（オプション）と取得行数の処理
	$showTable = isset($_GET['table']) ? (string)$_GET['table'] : '';
	$showLimit = isset($_GET['limit']) ? min(1000, max(1, (int)$_GET['limit'])) : 100;
	$showRows = [];
	$showError = '';
	if ($showTable !== '') {
		// 安全確認: テーブル名はスキーマの一覧にあることを確認
		if (!array_key_exists($showTable, $result) || !preg_match('/^[A-Za-z0-9_]+$/', $showTable)) {
			$showError = '無効なテーブル名が指定されています。';
		} else {
			try {
				$safeTable = str_replace('`','',$showTable);
				$q = "SELECT * FROM `" . $safeTable . "` LIMIT " . $showLimit;
				$rowsStmt = $pdo->query($q);
				$showRows = $rowsStmt->fetchAll(PDO::FETCH_ASSOC);
			} catch (PDOException $e) {
				$showError = 'テーブルの読み取り中にエラーが発生しました: ' . $e->getMessage();
			}
		}
	}
	?>
	<?php if (empty($tables)): ?>
		<p>テーブルが見つかりませんでした。</p>
	<?php endif; ?>

	<?php foreach ($result as $tableName => $columns): ?>
		<div class="table-name"><strong>テーブル:</strong> <?php echo htmlspecialchars($tableName, ENT_QUOTES, 'UTF-8'); ?>
			<span class="small"> — 件数: <?php echo isset($tableCounts[$tableName]) ? number_format($tableCounts[$tableName]) : 'N/A'; ?></span>
			<span class="small"> — <a href="?table=<?php echo urlencode($tableName); ?>&limit=100">表示(100行)</a></span>
		</div>
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

	<?php if ($showTable !== ''): ?>
		<hr>
		<h2>テーブル: <?php echo htmlspecialchars($showTable, ENT_QUOTES, 'UTF-8'); ?> の内容 (最大 <?php echo htmlspecialchars((string)$showLimit, ENT_QUOTES, 'UTF-8'); ?> 行)</h2>
		<?php if ($showError): ?>
			<div class="small" style="color:#a33"><?php echo htmlspecialchars($showError, ENT_QUOTES, 'UTF-8'); ?></div>
		<?php else: ?>
			<?php if (empty($showRows)): ?>
				<p class="small">表示する行がありません（または結果が空です）。</p>
			<?php else: ?>
				<table>
					<thead>
						<tr>
							<?php foreach (array_keys($showRows[0]) as $col): ?>
								<th><?php echo htmlspecialchars($col, ENT_QUOTES, 'UTF-8'); ?></th>
							<?php endforeach; ?>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($showRows as $r): ?>
							<tr>
								<?php foreach ($r as $v): ?>
									<td><?php echo htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); ?></td>
								<?php endforeach; ?>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>

</body>
</html>

