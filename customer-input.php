<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>会員登録</title>
  <style>
    /* 画面中央に細身カードを配置 */
    body{
      margin:0;
      min-height:100vh;
      display:flex;
      flex-direction:column;
      background:#fff;
      font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Hiragino Kaku Gothic ProN", "Noto Sans JP", Meiryo, sans-serif;
    }
    .content{
      margin: 24px auto 40px;
      width: 320px;                 /* 画像の細さに合わせる */
    }

    .title{
      font-size: 18px;
      font-weight: 700;
      margin: 8px 0 18px;
      color:#222;
      text-align:left;
    }

    /* ラベルは上・入力は全幅 */
    .form-group{
      margin: 12px 0;
    }
    .form-group label{
      display:block;
      font-size: 12px;
      color:#444;
      margin-bottom:6px;
    }
    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="tel"],
    select{
      width:100%;
      height:34px;
      box-sizing:border-box;
      border:1px solid #D9D9D9;
      border-radius:2px;
      padding: 6px 8px;
      font-size:14px;
      outline:none;
    }
    input::placeholder{ color:#aaa; }

    /* 横並び行（姓/名・郵便番号など） */
    .row{
      display:flex;
      gap:10px;
      align-items:flex-end;
    }
    .row .col{ flex:1; }
    .row .col-narrow{ width:86px; flex:0 0 86px; }      /* 郵便番号の小さめ枠 */
    .row .dash{
      align-self:center;
      color:#666;
      font-size:14px;
      margin:0 2px 6px;
    }
    /* 都道府県は選択肢の見た目を画像寄りに小さめで */
    .pref-wrap{
      display:flex;
      gap:10px;
      align-items:center;
    }
    .pref-wrap select{
      width: 140px;                  /* 画像の幅感 */
      height:34px;
    }

    /* 下部リンク */
    .notes{
      margin-top: 6px;
      font-size:12px;
      color:#666;
    }

    /* 送信ボタン（小さめ赤・中央） */
    .actions{
      display:flex;
      justify-content:center;
      margin-top:18px;
    }
    .button{
      background:#E43131;            /* 赤 */
      color:#fff;
      border:none;
      border-radius:4px;
      padding: 8px 18px;
      font-size:14px;
      cursor:pointer;
    }
    .button:hover{ opacity:.9; }

    /* 画像っぽい細い下線の行（市区町村・番地に雰囲気を寄せたいとき） */
    .underline input{
      border: none;
      border-bottom: 1px solid #D9D9D9;
      border-radius: 0;
      height: 28px;
      padding-left: 0;
    }

    /* ちょいスマホ時も同じ幅感で */
    @media (max-width: 360px){
      .content{ width: 92vw; }
      .pref-wrap select{ width: 44vw; }
    }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="content">
    <div class="title">会員登録</div>

    <?php
      // 戻り値の保持
      $prev_name_sei     = isset($_POST['name_sei'])      ? htmlspecialchars($_POST['name_sei'], ENT_QUOTES, 'UTF-8') : '';
      $prev_name_mei     = isset($_POST['name_mei'])      ? htmlspecialchars($_POST['name_mei'], ENT_QUOTES, 'UTF-8') : '';
      $prev_email        = isset($_POST['email'])         ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') : '';
      $prev_password     = isset($_POST['password'])      ? htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8') : '';
      $prev_tel          = isset($_POST['tel'])           ? htmlspecialchars($_POST['tel'], ENT_QUOTES, 'UTF-8') : '';
      $prev_postal_code1 = isset($_POST['postal_code1'])  ? htmlspecialchars($_POST['postal_code1'], ENT_QUOTES, 'UTF-8') : '';
      $prev_postal_code2 = isset($_POST['postal_code2'])  ? htmlspecialchars($_POST['postal_code2'], ENT_QUOTES, 'UTF-8') : '';
      $prev_prefecture   = isset($_POST['prefecture'])    ? htmlspecialchars($_POST['prefecture'], ENT_QUOTES, 'UTF-8') : '';
      $prev_city         = isset($_POST['city'])          ? htmlspecialchars($_POST['city'], ENT_QUOTES, 'UTF-8') : '';
      $prev_address      = isset($_POST['address'])       ? htmlspecialchars($_POST['address'], ENT_QUOTES, 'UTF-8') : '';
      $prev_building     = isset($_POST['building'])      ? htmlspecialchars($_POST['building'], ENT_QUOTES, 'UTF-8') : '';
    ?>

    <form method="post" action="customer-newinput.php" novalidate>
      <!-- 姓・名（横並び） -->
      <div class="row form-group">
        <div class="col">
          <label>姓</label>
          <input type="text" name="name_sei" maxlength="255" value="<?php echo $prev_name_sei; ?>">
        </div>
        <div class="col">
          <label>名</label>
          <input type="text" name="name_mei" maxlength="255" value="<?php echo $prev_name_mei; ?>">
        </div>
      </div>

      <div class="form-group">
        <label>ユーザー名</label>
        <input type="text" name="username" maxlength="255" placeholder="" />
      </div>

      <div class="form-group">
        <label>メールアドレス</label>
        <input type="email" name="email" maxlength="255" value="<?php echo $prev_email; ?>">
      </div>

      <div class="form-group">
        <label>パスワード</label>
        <input type="password" name="password" maxlength="255" value="<?php echo $prev_password; ?>">
      </div>

      <div class="form-group">
        <label>電話番号</label>
        <input type="tel" name="tel" maxlength="15" value="<?php echo $prev_tel; ?>">
      </div>

      <!-- 郵便番号（横並び） -->
      <div class="form-group">
        <label>郵便番号</label>
        <div class="row">
          <div class="col-narrow">
            <input type="text" name="postal_code1" maxlength="3" value="<?php echo $prev_postal_code1; ?>">
          </div>
          <div class="dash">－</div>
          <div class="col-narrow">
            <input type="text" name="postal_code2" maxlength="4" value="<?php echo $prev_postal_code2; ?>">
          </div>
        </div>
      </div>

      <!-- 都道府県（小さめ） -->
      <div class="form-group">
        <label>都道府県</label>
        <div class="pref-wrap">
          <select name="prefecture" id="prefecture">
            <option value="">選択してください</option>
            <?php
              $prefs = ['北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県','茨城県','栃木県','群馬県','埼玉県','千葉県','東京都','神奈川県','新潟県','富山県','石川県','福井県','山梨県','長野県','岐阜県','静岡県','愛知県','三重県','滋賀県','京都府','大阪府','兵庫県','奈良県','和歌山県','鳥取県','島根県','岡山県','広島県','山口県','徳島県','香川県','愛媛県','高知県','福岡県','佐賀県','長崎県','熊本県','大分県','宮崎県','鹿児島県','沖縄県'];
              foreach($prefs as $p){
                $sel = ($prev_prefecture===$p)?' selected':'';
                echo "<option value=\"$p\"$sel>$p</option>";
              }
            ?>
          </select>
        </div>
      </div>

      <div class="form-group underline">
        <label>市区町村</label>
        <input type="text" name="city" maxlength="255" value="<?php echo $prev_city; ?>">
      </div>

      <div class="form-group underline">
        <label>番地</label>
        <input type="text" name="address" maxlength="255" placeholder="00-00" value="<?php echo $prev_address; ?>">
      </div>

      <div class="form-group">
        <label>建物名（アパート、マンションなど）</label>
        <input type="text" name="building" maxlength="255" value="<?php echo $prev_building; ?>">
      </div>

      <div class="actions">
        <input type="submit" class="button" value="次へ">
      </div>
    </form>
  </div>
</body>
</html>
