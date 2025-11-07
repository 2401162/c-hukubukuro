<?php
// customer-input.php : 入力内容確認ページ
// フロー: customer-newinput.php (POST) → 本ページ(確認) → customer_done.php

// --- サーバーサイド必須チェック & 形式チェック ---
$required = ["name_sei","name_mei","username","email","password","tel","postal_code1","postal_code2","prefecture","city","address"];
$errors   = [];
$P        = [];

// POST前提。直接アクセスなら入力へ戻す
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: customer-newinput.php');
  exit;
}

// 値の取り出し（トリム）
foreach ($required as $k) { $P[$k] = trim($_POST[$k] ?? ""); }

// 形式チェック
if ($P['postal_code1'] !== '' && !preg_match('/^\d{3}$/', $P['postal_code1'])) $errors[] = "郵便番号（前半）は3桁の数字で入力してください。";
if ($P['postal_code2'] !== '' && !preg_match('/^\d{4}$/', $P['postal_code2'])) $errors[] = "郵便番号（後半）は4桁の数字で入力してください。";
if ($P['tel'] !== '' && !preg_match('/^\d{10,11}$/', str_replace('-', '', $P['tel']))) $errors[] = "電話番号は10～11桁の数字で入力してください（ハイフン可）。";
if ($P['email'] !== '' && !filter_var($P['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "メールアドレスの形式が正しくありません。";

// 空チェック
function labelOf($name){
  return [
    "name_sei"=>"姓","name_mei"=>"名","username"=>"ユーザー名","email"=>"メールアドレス",
    "password"=>"パスワード","tel"=>"電話番号","postal_code1"=>"郵便番号（前半）","postal_code2"=>"郵便番号（後半）",
    "prefecture"=>"都道府県","city"=>"市区町村","address"=>"番地","building"=>"建物名"
  ][$name] ?? $name;
}
foreach ($required as $k) {
  if ($P[$k] === '') $errors[] = labelOf($k) . "が入力されていません。";
}

// エラー表示（ここでは htmlspecialchars を直書きして h() の重複定義を避ける）
if ($errors) {
  echo '<!DOCTYPE html><html lang="ja"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>入力エラー</title></head><body>';
  include 'header.php';
  echo '<div style="max-width:640px;margin:24px auto;padding:16px;border:1px solid #f2b8b5;background:#fff5f5;border-radius:6px;">';
  echo '<h2 style="color:#a40000;margin:0 0 8px;">入力に不備があります</h2><ul style="margin:8px 0 0 20px;color:#a40000;">';
  foreach ($errors as $e) echo '<li>'.htmlspecialchars($e, ENT_QUOTES, 'UTF-8').'</li>';
  echo '</ul>';
  echo '<form method="post" action="customer-newinput.php" style="margin-top:16px;">';
  foreach ($_POST as $k=>$v) {
    if (is_array($v)) continue;
    echo '<input type="hidden" name="'.htmlspecialchars($k, ENT_QUOTES, 'UTF-8').'" value="'.htmlspecialchars($v, ENT_QUOTES, 'UTF-8').'">';
  }
  echo '<button type="submit" style="padding:10px 16px;border:none;border-radius:6px;background:#333;color:#fff;cursor:pointer;">入力画面に戻る</button>';
  echo '</form></div></body></html>';
  exit;
}

// --- ここから確認表示用の整形 ---
function h($v){ return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }

$fields = [
  'name_sei'   => $P['name_sei'],
  'name_mei'   => $P['name_mei'],
  'username'   => $P['username'],
  'email'      => $P['email'],
  'password'   => $P['password'],
  'tel'        => $P['tel'],
  'zip'        => $P['postal_code1'].$P['postal_code2'],
  'prefecture' => $P['prefecture'],
  'city'       => $P['city'],
  'address'    => $P['address'],
  'building'   => $P['building'],
];
$masked_pw = $fields['password'] !== '' ? str_repeat('●', max(6, mb_strlen($fields['password']))) : '';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>会員登録 - 入力内容確認</title>
  <style>
    html,body{ height:100%; }
    body{ margin:0; background:#fff; color:#111; font-family:"Noto Sans JP",system-ui,Meiryo,sans-serif; }
    .page{ max-width:900px; margin:0 auto; padding:0 16px 48px; }
    .title{ font-size:24px; font-weight:700; margin:24px 0 8px; }
    .sub{ font-size:16px; margin:18px 0 14px; }
    .card{ width:100%; max-width:640px; margin:0 auto; background:#fff; border-radius:8px; box-shadow:0 0 0 1px #e5e5e5; padding:24px 24px 16px; }
    .rows{ display:grid; grid-template-columns:160px 1fr; row-gap:14px; column-gap:12px; align-items:center; }
    .label{ font-size:14px; color:#333; }
    .value{ font-size:14px; color:#111; word-break:break-word; }
    .name-grid{ display:grid; grid-template-columns:80px 1fr 80px 1fr; column-gap:12px; row-gap:6px; align-items:center; }
    .actions{ display:flex; justify-content:center; gap:18px; margin:26px 0 0; flex-wrap:wrap; }
    .btn{ min-width:140px; height:40px; border-radius:6px; border:none; cursor:pointer; font-size:14px; font-weight:700; }
    .btn-back{ background:#333; color:#fff; }
    .btn-submit{ background:#e50012; color:#fff; }
    @media (max-width:480px){ .rows{ grid-template-columns:120px 1fr; } .name-grid{ grid-template-columns:64px 1fr 64px 1fr; } .card{ padding:18px 16px 12px; } }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="page">
    <h1 class="title">会員登録</h1>
    <div class="card">
      <h2 class="sub">入力内容確認</h2>

      <div class="name-grid" style="margin-bottom:14px;">
        <div class="label">姓</div><div class="value"><?= h($fields['name_sei']) ?></div>
        <div class="label">名</div><div class="value"><?= h($fields['name_mei']) ?></div>
      </div>

      <div class="rows">
        <div class="label">ユーザー名</div><div class="value"><?= h($fields['username']) ?></div>
        <div class="label">メールアドレス</div><div class="value"><?= h($fields['email']) ?></div>
        <div class="label">パスワード</div><div class="value"><?= $masked_pw ?></div>
        <div class="label">電話番号</div><div class="value"><?= h($fields['tel']) ?></div>
        <div class="label">郵便番号</div><div class="value"><?= h($fields['zip']) ?></div>
        <div class="label">都道府県</div><div class="value"><?= h($fields['prefecture']) ?></div>
        <div class="label">市区町村</div><div class="value"><?= h($fields['city']) ?></div>
        <div class="label">番地</div><div class="value"><?= h($fields['address']) ?></div>
        <div class="label">建物名（アパート、マンションなど）</div><div class="value"><?= h($fields['building']) ?></div>
      </div>

      <div class="actions">
        <!-- 戻る：入力へ値を戻す -->
        <form action="customer-newinput.php" method="post">
          <?php foreach($fields as $k=>$v): ?>
            <input type="hidden" name="<?= h($k) ?>" value="<?= h($v) ?>">
          <?php endforeach; ?>
          <!-- 郵便番号は newinput 側で前後に分かれるので分割して渡す -->
          <input type="hidden" name="postal_code1" value="<?= h(substr($fields['zip'],0,3)) ?>">
          <input type="hidden" name="postal_code2" value="<?= h(substr($fields['zip'],3)) ?>">
          <button type="submit" class="btn btn-back">戻る</button>
        </form>

        <!-- 登録：完了へ（POST） -->
        <form action="customer_done.php" method="post">
          <?php foreach($fields as $k=>$v): ?>
            <input type="hidden" name="<?= h($k) ?>" value="<?= h($v) ?>">
          <?php endforeach; ?>
          <!-- 完了側は zip を使わず都道府県/市区町村/番地を参照するため、そのままOK -->
          <button type="submit" class="btn btn-submit">登録</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>



