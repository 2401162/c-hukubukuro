<?php require 'admin-header.php'; ?>
<?php require 'admin-menu.php'; ?>
<?php require 'admin-db-connect.php'; ?>

<h1>レビュー管理</h1>

<table border="1" cellpadding="8">
    <tr>
        <th>ID</th><th>顧客ID</th><th>評価</th><th>コメント</th><th>作成日時</th><th>削除</th>
    </tr>

<?php
$stmt = $pdo->query('SELECT * FROM review WHERE is_active = 1 ORDER BY created_at DESC');
foreach ($stmt as $row) {
    echo '<tr>';
    echo '<td>' . $row['review_id'] . '</td>';
    echo '<td>' . $row['customer_id'] . '</td>';
    echo '<td>' . htmlspecialchars($row['rating'], ENT_QUOTES) . '</td>';
    echo '<td>' . nl2br(htmlspecialchars($row['comment'], ENT_QUOTES)) . '</td>';
    echo '<td>' . $row['created_at'] . '</td>';

    // 削除フォーム
    echo '<td>';
    echo '<form action="review-delete.php" method="post">';
    echo '<input type="hidden" name="review_id" value="' . $row['review_id'] . '">';
    echo '<input type="submit" value="削除">';
    echo '</form>';
    echo '</td>';

    echo '</tr>';
}
?>
</table>

<form action="admin-top.php" method="get">
        <br><input type="submit" value="トップページへ" class="top-button">
</form>

<?php require 'admin-footer.php'; ?>
