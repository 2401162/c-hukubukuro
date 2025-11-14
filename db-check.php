<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
ini_set('display_errors','1'); error_reporting(E_ALL);

require_once 'db-connect.php';

try {
    $pdo = new PDO($connect, USER, PASS, [
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
    ]);
} catch(Throwable $e) {
    echo 'DB接続失敗: '.htmlspecialchars($e->getMessage());
    exit;
}

$tables = [];
$columns = [];
$samples = [];

try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach($tables as $t){
        $c = $pdo->query("SHOW COLUMNS FROM `{$t}`")->fetchAll();
        $columns[$t] = $c;

        $cnt = $pdo->query("SELECT COUNT(*) FROM `{$t}`")->fetchColumn();
        $row = $pdo->query("SELECT * FROM `{$t}` LIMIT 1")->fetch();
        $samples[$t] = ['count'=>$cnt,'row'=>$row];
    }
} catch(Throwable $e){
    echo '取得失敗: '.htmlspecialchars($e->getMessage());
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>DB確認</title>
<style>
body{font-family:sans-serif;line-height:1.5;padding:20px}
h2{margin-top:32px}
table{border-collapse:collapse;width:100%;margin-top:10px}
th,td{border:1px solid #ccc;padding:6px}
pre{background:#f7f7f7;padding:10px;border:1px solid #ccc}
.tag{display:inline-block;background:#eef;padding:2px 6px;border:1px solid #99f;border-radius:4px}
</style>
</head>
<body>

<h1>DB確認ページ</h1>

<h2>接続情報</h2>
<div>Host: <code><?=htmlspecialchars(SERVER)?></code></div>
<div>DB: <code><?=htmlspecialchars(DBNAME)?></code></div>

<h2>テーブル一覧</h2>
<?php if(!$tables): ?>
<p>テーブルがありません。</p>
<?php else: ?>
<ul>
<?php foreach($tables as $t): ?>
<li class="tag"><?=htmlspecialchars($t)?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php foreach($tables as $t): ?>
<h2><?=htmlspecialchars($t)?></h2>

<h3>カラム情報</h3>
<table>
<tr>
<th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th>
</tr>
<?php foreach($columns[$t] as $c): ?>
<tr>
<td><?=htmlspecialchars($c['Field'])?></td>
<td><?=htmlspecialchars($c['Type'])?></td>
<td><?=htmlspecialchars($c['Null'])?></td>
<td><?=htmlspecialchars($c['Key'])?></td>
<td><?=htmlspecialchars((string)$c['Default'])?></td>
<td><?=htmlspecialchars($c['Extra'])?></td>
</tr>
<?php endforeach; ?>
</table>

<h3>件数 / サンプル1件</h3>
<p>COUNT: <strong><?=$samples[$t]['count']?></strong></p>
<pre><?=htmlspecialchars(json_encode($samples[$t]['row'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT))?></pre>

<?php endforeach; ?>

</body>
</html>
