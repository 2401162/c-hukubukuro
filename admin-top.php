<?php require 'admin-auth.php'; ?>
<?php require 'admin-header.php'; ?>
<?php require 'admin-menu.php'; ?>

<?php
echo '<h1>管理者機能一覧</h1>';
echo '<div class="a"><ul>
        <li>商品管理→商品の登録・更新・削除を行う</li>
        <li>おすすめ更新→トップページに表示するおすすめ商品の設定</li>
        <li>レビュー管理→投稿されたレビューの確認・削除</li>
        <li>顧客管理→登録済みの顧客情報を閲覧</li>
      </ul></div>';

echo '<form action="admin-logout.php" method="post">';
echo '<input type="submit" value="ログアウト" class="top-button">';
echo '</form>';
?>

<?php require 'admin-footer.php'; ?>
