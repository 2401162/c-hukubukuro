<?php
session_start();
require 'db-connect.php';
require 'admin-header.php';
require 'admin-menu.php';
?>

<h1>顧客管理</h1>

<style>
    table {
        width: 90%;
        margin: 20px auto;
        border-collapse: collapse;
        font-family: "Noto Sans JP", sans-serif;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    th, td {
        border: 1px solid #ccc;
        padding: 10px 15px;
        text-align: left;
    }
    th {
        background-color: #f8f8f8;
        font-weight: 600;
    }
    tr:nth-child(even) {
        background-color: #fafafa;
    }
    h1 {
        text-align: center;
        margin-top: 30px;
    }
    /* 🔽 トップページボタンのスタイル */
    .top-button {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: #2d2d2d;
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
        z-index: 100;
    }
    .top-button:hover {
        background-color: #444;
    }
</style>

<table>
    <thead>
        <tr>
            <th>名前</th>
            <th>メールアドレス</th>
            <th>電話番号</th>
            <th>住所</th>
        </tr>
    </thead>
    <tbody>
    <?php
        try {
            $pdo = new PDO($connect, USER, PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            $stmt = $pdo->query("SELECT name, email, phone, prefecture, city, address_line FROM customer");

            foreach ($stmt as $row):
    ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['prefecture'] . $row['city'] . $row['address_line']) ?></td>
        </tr>
    <?php
            endforeach;

        } catch (PDOException $e) {
            echo "<tr><td colspan='4' style='color:red;'>データベースエラー: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
        }
    ?>
    </tbody>
</table>

<!-- 🔽 常に右下に固定されるボタン -->
<form action="admin-top.php" method="get">
    <button type="submit" class="top-button">トップページへ</button>
</form>

<?php require 'admin-footer.php'; ?>
