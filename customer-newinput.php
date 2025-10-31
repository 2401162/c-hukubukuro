<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員登録</title>
</head>
<body>
    <div class="header">     
    <h1>福袋販売サイト</h1>
    <img src="images/logo.png" alt="サイトロゴ">
    </div>
    <div class="content">
    <?php
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // 受信するフィールド名は customer-input.php と合わせる
        $name_sei = isset($_POST['name_sei']) ? trim($_POST['name_sei']) : '';
        $name_mei = isset($_POST['name_mei']) ? trim($_POST['name_mei']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $tel = isset($_POST['tel']) ? trim($_POST['tel']) : '';
        // input name は customer-input.php 側で postal_code1/2 なので一致させる
        $postal_code1 = isset($_POST['postal_code1']) ? trim($_POST['postal_code1']) : '';
        $postal_code2 = isset($_POST['postal_code2']) ? trim($_POST['postal_code2']) : '';
        $prefecture = isset($_POST['prefecture']) ? trim($_POST['prefecture']) : '';
        $city = isset($_POST['city']) ? trim($_POST['city']) : '';
        $address = isset($_POST['address']) ? trim($_POST['address']) : '';
        $building = isset($_POST['building']) ? trim($_POST['building']) : '';

        $errors = [];
        // 必須チェック（簡易）
        if ($name_sei === '' || $name_mei === '') $errors[] = '氏名（姓・名）は必須です。';
        if ($email === '') $errors[] = 'メールアドレスは必須です。';
        if ($password === '') $errors[] = 'パスワードは必須です。';
        if ($postal_code1 === '' || $postal_code2 === '') $errors[] = '郵便番号は必須です。';
        if ($city === '' || $address === '') $errors[] = '市区町村・番地は必須です。';

        // メール形式チェック
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'メールアドレスの形式が正しくありません。';
        }
        // 郵便番号形式チェック
        if ($postal_code1 !== '' && !preg_match('/^\d{3}$/', $postal_code1)) {
            $errors[] = '郵便番号（前半）は3桁の数字で入力してください。';
        }
        if ($postal_code2 !== '' && !preg_match('/^\d{4}$/', $postal_code2)) {
            $errors[] = '郵便番号（後半）は4桁の数字で入力してください。';
        }

        if (!empty($errors)) {
            echo "<h2>入力にエラーがあります</h2>";
            echo "<ul style='color:#c00;'>";
            foreach ($errors as $err) {
                echo "<li>" . htmlspecialchars($err, ENT_QUOTES, 'UTF-8') . "</li>";
            }
            echo "</ul>";
            echo "<p><a href=\"javascript:history.back()\">戻る</a>して内容を修正してください。</p>";
        } else {
            // エスケープして表示
            $name_sei_html = htmlspecialchars($name_sei, ENT_QUOTES, 'UTF-8');
            $name_mei_html = htmlspecialchars($name_mei, ENT_QUOTES, 'UTF-8');
            $email_html = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
            $tel_html = htmlspecialchars($tel, ENT_QUOTES, 'UTF-8');
            $postal_code1_html = htmlspecialchars($postal_code1, ENT_QUOTES, 'UTF-8');
            $postal_code2_html = htmlspecialchars($postal_code2, ENT_QUOTES, 'UTF-8');
            $prefecture_html = htmlspecialchars($prefecture, ENT_QUOTES, 'UTF-8');
            $city_html = htmlspecialchars($city, ENT_QUOTES, 'UTF-8');
            $address_html = htmlspecialchars($address, ENT_QUOTES, 'UTF-8');
            $building_html = htmlspecialchars($building, ENT_QUOTES, 'UTF-8');

            echo "<h2>ご入力内容の確認</h2>";
            // 姓と名をハイフンでつなぎ横並びに表示
            echo "<div class=\"confirm-row\">";
            echo "<div class=\"confirm-item\"><strong>氏名</strong><br>" . $name_sei_html . ' - ' . $name_mei_html . "</div>";
            // 郵便番号も横並びで表示
            echo "<div class=\"confirm-item\"><strong>郵便番号</strong><br>" . $postal_code1_html . '-' . $postal_code2_html . "</div>";
            echo "</div>";

            echo "<p><strong>都道府県:</strong> " . $prefecture_html . "</p>";
            echo "<p><strong>市区町村:</strong> " . $city_html . "</p>";
            echo "<p><strong>番地:</strong> " . $address_html . "</p>";
            if ($building_html !== '') echo "<p><strong>建物名:</strong> " . $building_html . "</p>";
            echo "<p><strong>メールアドレス:</strong> " . $email_html . "</p>";
            echo "<p><strong>電話番号:</strong> " . $tel_html . "</p>";
            // パスワードは伏せて表示
            echo "<p><strong>パスワード:</strong> " . str_repeat('*', mb_strlen($password)) . "</p>";
            // ハッシュ化して登録用に送る（パスワードはここでハッシュ化して送信）
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // 戻る（編集）用フォーム：元の生パスワードを含めて customer-input.php に POST することで入力を保持
            echo '<div style="margin-top:12px;">';
            echo '<form method="post" action="customer-input.php" style="display:inline-block; margin-right:12px;">';
            // 生データを hidden で送る（戻って編集するため）
            echo '<input type="hidden" name="name_sei" value="' . htmlspecialchars($name_sei, ENT_QUOTES, 'UTF-8') . '">';
            echo '<input type="hidden" name="name_mei" value="' . htmlspecialchars($name_mei, ENT_QUOTES, 'UTF-8') . '">';
            echo '<input type="hidden" name="email" value="' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '">';
            echo '<input type="hidden" name="password" value="' . htmlspecialchars($password, ENT_QUOTES, 'UTF-8') . '">';
            echo '<input type="hidden" name="tel" value="' . htmlspecialchars($tel, ENT_QUOTES, 'UTF-8') . '">';
            echo '<input type="hidden" name="postal_code1" value="' . htmlspecialchars($postal_code1, ENT_QUOTES, 'UTF-8') . '">';
            echo '<input type="hidden" name="postal_code2" value="' . htmlspecialchars($postal_code2, ENT_QUOTES, 'UTF-8') . '">';
            echo '<input type="hidden" name="prefecture" value="' . htmlspecialchars($prefecture, ENT_QUOTES, 'UTF-8') . '">';
            echo '<input type="hidden" name="city" value="' . htmlspecialchars($city, ENT_QUOTES, 'UTF-8') . '">';
            echo '<input type="hidden" name="address" value="' . htmlspecialchars($address, ENT_QUOTES, 'UTF-8') . '">';
            echo '<input type="hidden" name="building" value="' . htmlspecialchars($building, ENT_QUOTES, 'UTF-8') . '">';
            echo '<input type="submit" value="戻るして編集" class="button">';
            echo '</form>';

            // 登録実行用フォーム：パスワードはハッシュ化して送信
            echo '<form method="post" action="member-register.php" style="display:inline-block;">';
            echo '<input type="hidden" name="name_sei" value="' . htmlspecialchars($name_sei, ENT_QUOTES, 'UTF-8') . '">';
            echo '<input type="hidden" name="name_mei" value="' . htmlspecialchars($name_mei, ENT_QUOTES, 'UTF-8') . '">';
            echo '<input type="hidden" name="email" value="' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '">';
            // こちらはハッシュ化したパスワードを送る
            echo '<input type="hidden" name="password" value="' . htmlspecialchars($password_hash, ENT_QUOTES, 'UTF-8') . '">';
            echo '<input type="hidden" name="tel" value="' . htmlspecialchars($tel, ENT_QUOTES, 'UTF-8') . '">';
            echo '<input type="hidden" name="postal_code1" value="' . htmlspecialchars($postal_code1, ENT_QUOTES, 'UTF-8') . '">';
            echo '<input type="hidden" name="postal_code2" value="' . htmlspecialchars($postal_code2, ENT_QUOTES, 'UTF-8') . '">';
            echo '<input type="hidden" name="prefecture" value="' . htmlspecialchars($prefecture, ENT_QUOTES, 'UTF-8') . '">';
            echo '<input type="hidden" name="city" value="' . htmlspecialchars($city, ENT_QUOTES, 'UTF-8') . '">';
            echo '<input type="hidden" name="address" value="' . htmlspecialchars($address, ENT_QUOTES, 'UTF-8') . '">';
            echo '<input type="hidden" name="building" value="' . htmlspecialchars($building, ENT_QUOTES, 'UTF-8') . '">';
            echo '<input type="submit" value="この内容で登録する" class="button">';
            echo '</form>';
            echo '</div>';
        }
    } else {
        echo "<p>フォームが正しく送信されていません。</p>";
    }
    











    ?>
        
    </div>
    <style>
    .header {
        display: flex;
        align-items: center;
        justify-content: center; 
        gap: 12px; 
        margin: 20px auto;
    }
    .header h1 {
        margin: 0;
        font-size: 1.6rem;
    }
    .header img {
        width: 60px;
        height: auto;
        display: block;
    }
    .content {
        text-align: center;
        margin: 0 auto 30px auto; 
        border-color: #000;
        border-style: solid;
        border-width: 1px;
        border-radius: 4px;
        width: 50%;
        padding: 20px;
        box-sizing: border-box;
    }
    .customer-input {
        text-align: center;
        width: 100%;
    }
    .confirm-row { display:flex; gap:20px; justify-content:center; align-items:flex-start; margin-bottom:12px; }
    .confirm-item { min-width:140px; text-align:left; }
    </style>
    
</body>
</html>