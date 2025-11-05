<?php session_start(); ?>
<?php require 'db-conect.php'; ?>
<?php require 'admin-header.php'; ?>
<?php require 'admin-menu.php'; ?>
<h1>顧客管理</h1>
<table>
    <thead>
        <tr>
            <th>名前</th>
            <th>ユーザーネーム</th>
            <th>メールアドレス</th>
            <th>電話番号</th>
            <th>パスワード</th>
        </tr>
    </thead>
    <tbody>
     <?php
        $stmt = $pdo->query("SELECT name, username, email, phone, password FROM customers");
        foreach ($stmt as $row):
        ?>
        <tr>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['username']) ?></td>
          <td><
          ?= htmlspecialchars($row['email']) ?></td>
          <td><?= htmlspecialchars($row['phone']) ?></td>
          <td><?= htmlspecialchars($row['password']) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
        </table>
        <form acrion="admin-top.php" method="get">
            <button type="submit">トップページへ</button>
        </form>

<?php require 'admin-footer.php'; ?>