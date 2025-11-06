<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>会員登録</title>
  <style>
    body {
      margin: 0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      font-family: "Noto Sans JP", sans-serif;
      background: #fff;
    }
    .content {
      margin: 24px auto 40px;
      width: 320px;
    }
    .title {
      font-size: 18px;
      font-weight: 700;
      margin: 8px 0 18px;
      color: #222;
      text-align: left;
    }
    .form-group {
      margin: 12px 0;
    }
    .form-group label {
      display: block;
      font-size: 12px;
      color: #444;
      margin-bottom: 6px;
    }
    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="tel"],
    select {
      width: 100%;
      height: 34px;
      border: 1px solid #d9d9d9;
      border-radius: 2px;
      padding: 6px 8px;
      font-size: 14px;
      box-sizing: border-box;
    }
    .row {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .postal {
      width: 80px;
    }
    .button {
      background: #e43131;
      color: #fff;
      border: none;
      border-radius: 4px;
      padding: 8px 18px;
      font-size: 14px;
      cursor: pointer;
    }
    .actions {
      display: flex;
      justify-content: center;
      margin-top: 20px;
    }
    #zip-error {
      color: #c00;
      font-size: 12px;
      margin-top: 4px;
      display: none;
    }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="content">
    <div class="title"><h2>会員登録<h2></div>

    <form method="post" action="customer-newinput.php">
      <div class="form-group row">
        <div style="flex:1;">
          <label>姓</label>
          <input type="text" name="name_sei" maxlength="255" />
        </div>
        <div style="flex:1;">
          <label>名</label>
          <input type="text" name="name_mei" maxlength="255" />
        </div>
      </div>

      <div class="form-group">
        <label>メールアドレス</label>
        <input type="email" name="email" maxlength="255" />
      </div>

      <div class="form-group">
        <label>パスワード</label>
        <input type="password" name="password" maxlength="255" />
      </div>

      <div class="form-group">
        <label>電話番号</label>
        <input type="tel" name="tel" maxlength="15" />
      </div>

      <div class="form-group">
        <label>郵便番号</label>
        <div class="row">
          <input type="text" id="postal_code1" name="postal_code1" maxlength="3" class="postal" /> －
          <input type="text" id="postal_code2" name="postal_code2" maxlength="4" class="postal" />
        </div>
        <p id="zip-error"></p>
      </div>

      <div class="form-group">
        <label>都道府県</label>
        <select id="prefecture" name="prefecture">
          <option value="">選択してください</option>
          <?php
          $prefs = ['北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県','茨城県','栃木県','群馬県','埼玉県','千葉県','東京都','神奈川県','新潟県','富山県','石川県','福井県','山梨県','長野県','岐阜県','静岡県','愛知県','三重県','滋賀県','京都府','大阪府','兵庫県','奈良県','和歌山県','鳥取県','島根県','岡山県','広島県','山口県','徳島県','香川県','愛媛県','高知県','福岡県','佐賀県','長崎県','熊本県','大分県','宮崎県','鹿児島県','沖縄県'];
          foreach($prefs as $p){ echo "<option value='$p'>$p</option>"; }
          ?>
        </select>
      </div>

      <div class="form-group">
        <label>市区町村</label>
        <input type="text" id="city" name="city" maxlength="255" />
      </div>

      <div class="form-group">
        <label>番地</label>
        <input type="text" id="address" name="address" maxlength="255" />
      </div>

      <div class="form-group">
        <label>建物名（アパート・マンションなど）</label>
        <input type="text" name="building" maxlength="255" />
      </div>

      <div class="actions">
        <input type="submit" class="button" value="次へ" />
      </div>
    </form>
  </div>

  <!-- 🔽 郵便番号→住所自動入力スクリプト -->
  <script>
    (() => {
      const p1 = document.getElementById("postal_code1");
      const p2 = document.getElementById("postal_code2");
      const pref = document.getElementById("prefecture");
      const city = document.getElementById("city");
      const addr = document.getElementById("address");
      const err = document.getElementById("zip-error");

      [p1, p2].forEach((el) => {
        el.addEventListener("input", () => {
          el.value = el.value.replace(/\D/g, "");
        });
      });

      p1.addEventListener("input", lookup);
      p2.addEventListener("input", lookup);
      p1.addEventListener("blur", lookup);
      p2.addEventListener("blur", lookup);

      async function lookup() {
        const a = p1.value.trim();
        const b = p2.value.trim();
        hideError();
        if (a.length !== 3 || b.length !== 4) return;
        const zipcode = a + b;

        try {
          const res = await fetch(
            "https://zipcloud.ibsnet.co.jp/api/search?zipcode=" + zipcode
          );
          const data = await res.json();
          if (!data.results || !data.results.length)
            return showError("該当する住所が見つかりません。");
          const r = data.results[0];
          const a1 = r.address1 || "";
          const a2 = r.address2 || "";
          const a3 = r.address3 || "";

          // 都道府県選択
          let matched = false;
          for (const opt of pref.options) {
            if (opt.value === a1) {
              opt.selected = true;
              matched = true;
              break;
            }
          }
          if (!matched)
            showError("都道府県が選択肢と一致しません。手動で選んでください。");

          city.value = a2;
          addr.value = a3;
        } catch (e) {
          console.error(e);
          showError("住所検索でエラーが発生しました。");
        }
      }

      function showError(msg) {
        err.textContent = msg;
        err.style.display = "block";
      }
      function hideError() {
        err.textContent = "";
        err.style.display = "none";
      }
    })();
  </script>
</body>
</html>

