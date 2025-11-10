<?php
declare(strict_types=1);

/* ====== 基本設定 ====== */
const SERVER = 'mysql326.phy.lolipop.jp';
const DBNAME = 'LAA1607624-group';
const USER   = 'LAA1607624';
const PASS   = 'pass0726';

/* ====== テーブル名（必要に応じて使用） ====== */
const TABLE_MEMBERS = 'members';
const TABLE_ORDERS  = 'orders';
const TABLE_CARTS   = 'carts';

/* ====== DSN（接続文字列） ====== */
$dsn = 'mysql:host=' . SERVER . ';dbname=' . DBNAME . ';charset=utf8mb4';

/* ====== PDO接続 ====== */
try {
    $pdo = new PDO(
        $dsn,
        USER,
        PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // 例外で捕捉
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // 連想配列
            PDO::ATTR_EMULATE_PREPARES   => false,                    // 静的プリペアド
        ]
    );
} catch (PDOException $e) {
    exit('DB接続失敗: ' . $e->getMessage());
}
