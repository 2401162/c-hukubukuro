<?php
// admin-save_recommend.php
require_once 'admin-db-connect.php';

// POST チェック
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['order'])) {
    $order = json_decode($_POST['order'], true);
    if (!is_array($order)) {
        header('Location: admin-recommend.php');
        exit;
    }

    // トランザクション推奨
    $pdo->beginTransaction();
    try {
        // 既存を削除して新しく挿入（簡単な方法）
        $pdo->exec("DELETE FROM recommended");

        $stmt = $pdo->prepare("INSERT INTO recommended (product_id, sort_order) VALUES (?, ?)");
        foreach ($order as $i => $id) {
            // 整数化（安全対策）
            $pid = (int)$id;
            $stmt->execute([$pid, $i + 1]);
        }
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        // エラー表示は開発時のみ。実環境ではログに出す。
        die('保存エラー: ' . $e->getMessage());
    }
}

// 完了して戻る
header('Location: admin-recommend.php');
exit;