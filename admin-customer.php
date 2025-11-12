<?php session_start(); ?>
<?php require 'db-connect.php'; ?>
<?php require 'admin-header.php'; ?>
<?php require 'admin-menu.php'; ?>
<h1>顧客管理</h1>
<table>
    <thead>
        <tr>
            <th>名前</th>
            <th>メールアドレス</th>
            <th>電話番号</th>
            <th>パスワード</th>
            <th>住所</th>0
        </tr>
    </thead>
    <tbody>
     <?php
        $stmt = $pdo->query("SELECT name, email, phone, prefecture, city , address_line , password_hash FROM customer");
        foreach ($stmt as $row):
        ?>
        <tr>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?= htmlspecialchars($row['phone']) ?></td>
          <td><?= htmlspecialchars($row['password_hash']) ?></td>
          <td><?= htmlspecialchars($row['prefecture'] . $row['city'] . $row['address_line']) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
        </table>
        <form acrion="admin-top.php" method="get">
            <button type="submit">トップページへ</button>
        </form>

<?php require 'admin-footer.php'; ?>