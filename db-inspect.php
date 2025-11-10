<?php
declare(strict_types=1);
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once 'db-connect.php';

echo '接続できています！<br>';

$sql = $pdo->query("SHOW TABLES");
foreach ($sql as $row) {
    echo $row[0] . "<br>";
}

$dsn = 'mysql:host='.SERVER.';dbname='.DBNAME.';charset=utf8mb4';
$info = ['connected' => false, 'error' => null, 'server_version' => null, 'current_user' => null];

try {
    $pdo = new PDO($dsn, USER, PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 5,
    ]);
    $info['connected'] = true;
    $info['server_version'] = $pdo->query('SELECT VERSION() v')->fetch()['v'] ?? '';
    $info['current_user'] = $pdo->query('SELECT CURRENT_USER() u')->fetch()['u'] ?? '';

    $tables = $pdo->prepare("
        SELECT TABLE_NAME, TABLE_ROWS, ENGINE, CREATE_TIME, TABLE_COLLATION
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = :db
        ORDER BY TABLE_NAME
    ");
    $tables->execute([':db' => DBNAME]);
    $tableRows = $tables->fetchAll();

    $columns = $pdo->prepare("
        SELECT TABLE_NAME, ORDINAL_POSITION, COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_KEY, COLUMN_DEFAULT, EXTRA
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = :db
        ORDER BY TABLE_NAME, ORDINAL_POSITION
    ");
    $columns->execute([':db' => DBNAME]);
    $columnRows = $columns->fetchAll();

    $samples = [];
    foreach ($tableRows as $t) {
        $tn = $t['TABLE_NAME'];
        try {
            $cnt = $pdo->query("SELECT COUNT(*) c FROM `{$tn}`")->fetch()['c'] ?? 0;
            $one = $pdo->query("SELECT * FROM `{$tn}` LIMIT 1")->fetch() ?: [];
            $samples[$tn] = ['count' => (int)$cnt, 'one' => $one];
        } catch (Throwable $e) {
            $samples[$tn] = ['count' => null, 'one' => [], 'error' => $e->getMessage()];
        }
    }

} catch (Throwable $e) {
    $info['error'] = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>DB Diagnostics</title>
<style>
body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;line-height:1.5;margin:24px}
h1{font-size:20px;margin:0 0 12px}
section{margin:24px 0}
code,pre{background:#f6f7f9;border:1px solid #e2e5ea;border-radius:6px;padding:8px}
table{border-collapse:collapse;width:100%;margin-top:8px}
th,td{border:1px solid #e2e5ea;padding:6px 8px;font-size:14px}
th{background:#fafbfc;text-align:left}
.bad{color:#c62828;font-weight:600}
.good{color:#2e7d32;font-weight:600}
.tag{display:inline-block;padding:2px 6px;border-radius:4px;background:#eef2ff;border:1px solid #d7dcff;font-size:12px}
.small{font-size:12px;color:#777}
</style>
</head>
<body>
<h1>DB Diagnostics</h1>

<section>
  <div>Host: <code><?=htmlspecialchars(SERVER)?></code></div>
  <div>DB: <code><?=htmlspecialchars(DBNAME)?></code></div>
  <div>User: <code><?=htmlspecialchars(USER)?></code></div>
</section>

<section>
  <h2>接続状態</h2>
  <?php if($info['connected']): ?>
    <div class="good">接続成功</div>
    <div class="small">MySQL: <?=htmlspecialchars($info['server_version'])?> / CURRENT_USER(): <?=htmlspecialchars($info['current_user'])?></div>
  <?php else: ?>
    <div class="bad">接続失敗</div>
    <pre><?=htmlspecialchars((string)$info['error'])?></pre>
  <?php endif; ?>
</section>

<?php if($info['connected']): ?>
<section>
  <h2>テーブル一覧</h2>
  <table>
    <thead><tr><th>TABLE_NAME</th><th>ROWS</th><th>ENGINE</th><th>CREATE_TIME</th><th>COLLATION</th></tr></thead>
    <tbody>
      <?php foreach($tableRows as $r): ?>
        <tr>
          <td><span class="tag"><?=htmlspecialchars($r['TABLE_NAME'])?></span></td>
          <td><?=htmlspecialchars((string)$r['TABLE_ROWS'])?></td>
          <td><?=htmlspecialchars((string)$r['ENGINE'])?></td>
          <td><?=htmlspecialchars((string)$r['CREATE_TIME'])?></td>
          <td><?=htmlspecialchars((string)$r['TABLE_COLLATION'])?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php if(empty($tableRows)): ?>
    <div class="small">テーブルが見つかりませんでした（DB名の確認を）。</div>
  <?php endif; ?>
</section>

<section>
  <h2>カラム定義</h2>
  <table>
    <thead><tr><th>TABLE</th><th>#</th><th>COLUMN</th><th>TYPE</th><th>NULL</th><th>KEY</th><th>DEFAULT</th><th>EXTRA</th></tr></thead>
    <tbody>
      <?php foreach($columnRows as $c): ?>
        <tr>
          <td><?=htmlspecialchars($c['TABLE_NAME'])?></td>
          <td><?=htmlspecialchars((string)$c['ORDINAL_POSITION'])?></td>
          <td><?=htmlspecialchars($c['COLUMN_NAME'])?></td>
          <td><?=htmlspecialchars($c['COLUMN_TYPE'])?></td>
          <td><?=htmlspecialchars($c['IS_NULLABLE'])?></td>
          <td><?=htmlspecialchars($c['COLUMN_KEY'])?></td>
          <td><?=htmlspecialchars((string)$c['COLUMN_DEFAULT'])?></td>
          <td><?=htmlspecialchars($c['EXTRA'])?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<section>
  <h2>各テーブルの件数・サンプル1件</h2>
  <?php foreach($samples as $tn => $s): ?>
    <h3><?=htmlspecialchars($tn)?></h3>
    <?php if(isset($s['error'])): ?>
      <div class="bad">取得エラー</div>
      <pre><?=htmlspecialchars($s['error'])?></pre>
    <?php else: ?>
      <div>COUNT(*) = <strong><?=htmlspecialchars((string)$s['count'])?></strong></div>
      <pre><?=htmlspecialchars(json_encode($s['one'], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT))?></pre>
    <?php endif; ?>
  <?php endforeach; ?>
</section>

<section>
  <h2>権限（参考）</h2>
  <pre>
<?php
try {
    $gr = $pdo->query("SHOW GRANTS FOR CURRENT_USER()")->fetchAll(PDO::FETCH_NUM);
    foreach($gr as $row){ echo htmlspecialchars($row[0]).PHP_EOL; }
} catch (Throwable $e) { echo htmlspecialchars($e->getMessage()); }
?>
  </pre>
</section>
<?php endif; ?>

</body>
</html>

