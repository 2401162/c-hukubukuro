<?php
const SERVER = 'mysql326.phy.lolipop.lan';
const DBNAME = 'LAA1607624-group';
const USER = 'LAA1607624';
const PASS = 'pass0726';

$connect = 'mysql:host=' . SERVER . ';dbname=' . DBNAME . ';charset=utf8';

try {
    $pdo = new PDO($connect, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo '接続エラー: ' . $e->getMessage();
    exit;
}
?>
