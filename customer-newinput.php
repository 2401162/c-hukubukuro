<?php
// 入力ページ → 確認(customer-input.php)へPOST
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>会員登録</title>
  <style>
    /* --- 共通: ヘッダーの縦位置を明示（他ページと統一） --- */
    .site-header{
      display:flex;
      align-items:center;
      justify-content:space-between;
      line-height:1;
      margin:0;
    }
    .site-header img,.site-header svg,.site-header i{
      vertical-align:middle;
      display:inline-block;
    }

    /* --- このページ専用のスコープ --- */
    body { margin:0; font-family:"Noto Sans JP",sans-serif; background:#fff; }
    .register-page .content { margin:24px auto 40px; width:320px; }
    .register-page .title { font-size:18px; font-weight:700; margin:8px 0 18px; color:#222; }
    .register-page .form-group { margin:12px 0; }
    .register-page .form-group label { display:block; font-size:12px; color:#444; margin-bottom:6px; }
    .register-page input[type="text"],
    .register-page input[type="email"],
    .register-page input[type="password"],
    .register-page input[type="tel"],
    .register-page select{
      width:100%; height:34px; border:1px solid #d9d9d9; border-radius:2px; padding:6px 8px; font-size:14px; box-sizing:border-box;
    }

    .register-page .form-row{ display:flex; align-items:center; gap:8px; }
    .register-page .postal{ width:80px; }
    .register-page .actions{ display:flex; justify-content:center; margin-top:20px; }
    .register-page .button{ background:#e43131; color:#fff; border:none; border-radius:4px; padding:8px 18px; font-size:14px; cursor:pointer; }

    .register-page .errors { margin:0 0 12px; padding:10px 12px; border:1px solid #f2b8b5; background:#fff5f5; color:#a40000; border-radius:4px; display:none; }
    .register-page .errors ul { margin:6px 0 0; padding-left:18px; }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="register-page">
    <div class="content">
      <div class="title"><h2>会員登録</h2></div>

      <div class="errors" id="errors">
        <strong>入力に不備があります。</strong>
        <ul id="error-list"></ul>
      </div>

      <form id="register-form" method="post" action="customer-input.php" novalidate>
        <div class="form-group form-row">
          <div style="flex:1;">
            <label>姓</label>
            <input type="text" name="name_sei" maxlength="255" required />
          </div>
          <div style="flex:1;">
            <label>名</label>
            <input type="text" name="name_mei" maxlength="255" required />
          </div>
        </div>

        <div class="form-group">
          <label>ユーザー名</label>
          <input type="text" name="username" maxlength="255" required />
        </div>

        <div class="form-group">
          <label>メールアドレス</label>
          <input type="email" name="email" maxlength="255" required />
        </div>

        <div class="form-group">
          <label>パスワード</label>
          <input type="password" name="password" maxlength="255" required />
        </div>

        <div class="form-group">
          <label>電話番号</label>
          <input type="tel" name="tel" maxlength="13" placeholder="09012345678 または 090-1234-5678" required />
        </div>

        <div class="form-group">
          <label>郵便番号</label>
          <div class="form-row">
            <input type="text" id="postal_code1" name="postal_code1" maxlength="3" class="postal" required /> －
            <input type="text" id="postal_code2" name="postal_code2" maxlength="4" class="postal" required />
          </div>
        </div>

        <div class="form-group">
          <label>都道府県</label>
          <select id="prefecture" name="prefecture" required>
            <option value="">選択してください</option>
            <?php
            $prefs=['北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県','茨城県','栃木県','群馬県','埼玉県','千葉県','東京都','神奈川県','新潟県','富山県','石川県','福井県','山梨県','長野県','岐阜県','静岡県','愛知県','三重県','滋賀県','京都府','大阪府','兵庫県','奈良県','和歌山県','鳥取県','島根県','岡山県','広島県','山口県','徳島県','香川県','愛媛県','高知県','福岡県','佐賀県','長崎県','熊本県','大分県','宮崎県','鹿児島県','沖縄県'];
            foreach($prefs as $p){ echo "<option value='$p'>$p</option>"; }
            ?>
          </select>
        </div>

        <div class="form-group">
          <label>市区町村・町域</label>
          <!-- ※ ここに address2 + address3 を自動連結で入れる -->
          <input type="text" id="city" name="city" maxlength="255"/>
        </div>

        <div class="form-group">
          <label>番地</label>
          <!-- ※ 自動入力しない（ユーザーが番地のみ入力） -->
          <input type="text" id="address" name="address" maxlength="255" placeholder="例）1-2-3" required />
        </div>

        <div class="form-group">
          <label>建物名（アパート・マンションなど）</label>
          <input type="text" name="building" maxlength="255" required />
        </div>

        <div class="actions">
          <input type="submit" class="button" value="次へ" />
        </div>
      </form>
    </div>
  </div>

  <script>
    // スコープ: このページ内から要素を取得
    const page = document.querySelector(".register-page");
    const p1   = page.querySelector("#postal_code1");
    const p2   = page.querySelector("#postal_code2");
    const pref = page.querySelector("#prefecture");
    const city = page.querySelector("#city");
    const addr = page.querySelector("#address");

    // 入力制限（郵便番号は数字のみ）
    [p1,p2].forEach(el => el.addEventListener("input", () => {
      el.value = el.value.replace(/\D/g, "");
      lookup(); // 入力のたび検索
    }));

    async function lookup(){
      const a = (p1.value || "").trim();
      const b = (p2.value || "").trim();
      if (a.length !== 3 || b.length !== 4) return;

      try{
        const res = await fetch("https://zipcloud.ibsnet.co.jp/api/search?zipcode=" + a + b);
        const data = await res.json();
        if (!data.results || !data.results.length) return;
        const r = data.results[0];

        const a1 = r.address1 || ""; // 都道府県
        const a2 = r.address2 || ""; // 市区町村
        const a3 = r.address3 || ""; // 町域

        // 都道府県を選択
        for (const opt of pref.options) {
          if (opt.value === a1) { opt.selected = true; break; }
        }

        // ★ 市区町村・町域をまとめて city に入れる
        const fullCity = (a2 + a3).replace(/\s+/g, "");
        city.value = fullCity;

        // ★ 番地は自動入力しない（空にしてフォーカスを当てる）
        addr.value = "";
        addr.focus();

      }catch(e){
        console.error("住所検索エラー:", e);
      }
    }

    // 送信前バリデーション（既存のまま）
    document.getElementById("register-form").addEventListener("submit", function(e){
      const errs = [];
      const f = this;
      const val = (name)=> (f.elements[name]?.value ?? "").trim();

      const requiredFields = [
        "name_sei","name_mei","username","email","password",
        "tel","postal_code1","postal_code2","prefecture","city","address"
      ];
      requiredFields.forEach(n => { if (!val(n)) errs.push(`${labelOf(n)}が入力されていません。`); });

      if (val("postal_code1") && !/^\d{3}$/.test(val("postal_code1"))) errs.push("郵便番号（前半）は3桁の数字で入力してください。");
      if (val("postal_code2") && !/^\d{4}$/.test(val("postal_code2"))) errs.push("郵便番号（後半）は4桁の数字で入力してください。");

      const telDigits = val("tel").replace(/-/g,"");
      if (telDigits && !/^\d{10,11}$/.test(telDigits)) errs.push("電話番号は10～11桁の数字で入力してください（ハイフン可）。");

      if (val("email") && !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(val("email"))) errs.push("メールアドレスの形式が正しくありません。");

      if (errs.length){
        e.preventDefault();
        const box = document.getElementById("errors");
        const ul = document.getElementById("error-list");
        ul.innerHTML = "";
        errs.forEach(m => {
          const li = document.createElement("li");
          li.textContent = m;
          ul.appendChild(li);
        });
        box.style.display = "block";
        window.scrollTo({ top: 0, behavior: "smooth" });
      }

      function labelOf(name){
        switch(name){
          case "name_sei": return "姓";
          case "name_mei": return "名";
          case "username": return "ユーザー名";
          case "email": return "メールアドレス";
          case "password": return "パスワード";
          case "tel": return "電話番号";
          case "postal_code1": return "郵便番号（前半）";
          case "postal_code2": return "郵便番号（後半）";
          case "prefecture": return "都道府県";
          case "city": return "市区町村・町域";
          case "address": return "番地";
          case "building": return "建物名";
          default: return name;
        }
      }
    });
  </script>
</body>
</html>





