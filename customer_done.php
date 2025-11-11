<?php
// 完了画面: 確認(customer-input.php)からPOSTで到達
// DB接続：customer テーブルに登録データを保存
require_once __DIR__ . '/db-connect.php';

function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$registration_success = false;
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name_sei     = trim($_POST['name_sei'] ?? '');
    $name_mei     = trim($_POST['name_mei'] ?? '');
    $username     = trim($_POST['username'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $password     = $_POST['password'] ?? '';
    $tel          = trim($_POST['tel'] ?? '');
    $zip          = (trim($_POST['zip'] ?? '') === '') ? null : trim($_POST['zip'] ?? '');
    $postal_code1 = trim($_POST['postal_code1'] ?? '');
    $postal_code2 = trim($_POST['postal_code2'] ?? '');
    $prefecture   = trim($_POST['prefecture'] ?? '');
    $city         = trim($_POST['city'] ?? '');
    $addr         = trim($_POST['address'] ?? '');
    
    // 郵便番号を結合
    $postal_code = ($postal_code1 && $postal_code2) ? $postal_code1 . $postal_code2 : null;
    
    try {
        $pdo = new PDO(
            $connect,
            USER,
            PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
        
        // customer テーブル: 
        // customer_id (PK auto), email (UNI), password_hash, name, phone, postal_code, prefecture, city, address_line, is_active, created_at, updated_at
        
        // パスワードをハッシュ化
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        
        // 組み合わせた名前を保存
        $full_name = $name_sei . ' ' . $name_mei;
        
        $stmt = $pdo->prepare(
            "INSERT INTO customer (email, password_hash, name, phone, postal_code, prefecture, city, address_line, is_active, created_at, updated_at)
             VALUES (:email, :password_hash, :name, :phone, :postal_code, :prefecture, :city, :address_line, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)"
        );
        
        $stmt->execute([
            ':email'           => $email,
            ':password_hash'   => $password_hash,
            ':name'            => $full_name,
            ':phone'           => $tel ?: null,
            ':postal_code'     => $postal_code,
            ':prefecture'      => $prefecture ?: null,
            ':city'            => $city ?: null,
            ':address_line'    => $addr ?: null,
        ]);
        
        $registration_success = true;
    } catch (PDOException $e) {
        // メールアドレスが既に登録されている場合など
        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
            $error_message = 'このメールアドレスは既に登録されています。';
        } else {
            $error_message = 'データベースエラーが発生しました。管理者にお問い合わせください。';
        }
    } catch (Throwable $e) {
        $error_message = 'エラーが発生しました。' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>登録完了</title>
  <style>
    .wrapper{ display:flex; justify-content:center; align-items:center; min-height:80vh; }
    .content{ text-align:center; width:60%; border:1px solid #000; padding:20px; border-radius:6px; background:#fff; }
    .error{ border-color:#c33; background:#fcc; color:#c33; }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="wrapper">
    <div class="content">
      <?php
      if ($_SERVER["REQUEST_METHOD"] === "POST") {
          if ($registration_success) {
              echo "<h1>会員登録完了</h1>";
              echo "<p>以下の内容で登録を受け付けました。</p>";
              echo "<p><strong>氏名:</strong> " . h($name_sei) . " - " . h($name_mei) . "</p>";
              echo "<p><strong>メールアドレス:</strong> " . h($email) . "</p>";
              echo "<p><strong>住所:</strong> " . h($prefecture . ' ' . $city . ' ' . $addr) . "</p>";
              echo "<p>パスワードは安全のため表示していません。</p>";
              echo "<p><a href='rogin-input.php'>ログイン画面へ</a></p>";
          } else {
              echo "<h2 class='error'>登録エラー</h2>";
              echo "<p>" . h($error_message) . "</p>";
              echo "<p><a href='customer-newinput.php'>会員登録ページに戻る</a></p>";
          }
      } else {
          echo "<h2>不正なアクセス</h2>";
          echo "<p>登録完了ページに直接アクセスしています。フォームから登録してください。</p>";
          echo "<p><a href='customer-newinput.php'>会員登録ページへ</a></p>";
      }
      ?>
    </div>
  </div>
</body>
</html>



