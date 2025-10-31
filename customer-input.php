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
                <div class="customer-input">

                    <form method="post" action="customer-newinput.php">
                        <label>姓<br>
                            <input type="text" name="name_sei" maxlength="255" class="name">
                        </label>

                        <label>名<br>
                            <input type="text" name="name_mei" maxlength="255" class="name">
                        </label>

                        <label>メールアドレス<br>
                            <input type="email" name="email" maxlength="255" class="email">
                        </label>

                        <label>パスワード<br>
                            <input type="password" name="password" maxlength="255" class="password">
                        </label>

                        <label>電話番号<br>
                            <input type="tel" name="tel" maxlength="15" class="tel">
                        </label>

                        <label>郵便番号<br>
                            <input type="text" id="postal_code1" name="postal_code1" size="4" maxlength="3" class="postal"> -
                            <input type="text" id="postal_code2" name="postal_code2" size="4" maxlength="4" class="postal">
                        </label>
                        <p id="zip-error" style="color:#c00; width:80%; margin:4px auto 0 auto; text-align:left; display:none;"></p>

                        <label>都道府県<br>
                            <select id="prefecture" name="prefecture" class="prefecture">
                                <option value="">選択してください</option>
                                <option value="北海道">北海道</option>
                                <option value="青森県">青森県</option>
                                <option value="岩手県">岩手県</option>
                                <option value="宮城県">宮城県</option>
                                <option value="秋田県">秋田県</option>
                                <option value="山形県">山形県</option>
                                <option value="福島県">福島県</option>
                                <option value="茨城県">茨城県</option>
                                <option value="栃木県">栃木県</option>
                                <option value="群馬県">群馬県</option>
                                <option value="埼玉県">埼玉県</option>
                                <option value="千葉県">千葉県</option>
                                <option value="東京都">東京都</option>
                                <option value="神奈川県">神奈川県</option>
                                <option value="新潟県">新潟県</option>
                                <option value="富山県">富山県</option>
                                <option value="石川県">石川県</option>
                                <option value="福井県">福井県</option>
                                <option value="山梨県">山梨県</option>
                                <option value="長野県">長野県</option>
                                <option value="岐阜県">岐阜県</option>
                                <option value="静岡県">静岡県</option>
                                <option value="愛知県">愛知県</option>
                                <option value="三重県">三重県</option>
                                <option value="滋賀県">滋賀県</option>
                                <option value="京都府">京都府</option>
                                <option value="大阪府">大阪府</option>
                                <option value="兵庫県">兵庫県</option>
                                <option value="奈良県">奈良県</option>
                                <option value="和歌山県">和歌山県</option>
                                <option value="鳥取県">鳥取県</option>
                                <option value="島根県">島根県</option>
                                <option value="岡山県">岡山県</option>
                                <option value="広島県">広島県</option>
                                <option value="山口県">山口県</option>
                                <option value="徳島県">徳島県</option>
                                <option value="香川県">香川県</option>
                                <option value="愛媛県">愛媛県</option>
                                <option value="高知県">高知県</option>
                                <option value="福岡県">福岡県</option>
                                <option value="佐賀県">佐賀県</option>
                                <option value="長崎県">長崎県</option>
                                <option value="熊本県">熊本県</option>
                                <option value="大分県">大分県</option>
                                <option value="宮崎県">宮崎県</option>
                                <option value="鹿児島県">鹿児島県</option>
                                <option value="沖縄県">沖縄県</option>
                            </select>
                        </label>

                        <label>市町村区<br>
                            <input type="text" name="city" maxlength="255" class="city">
                        </label>

                        <label>番地<br>
                            <input type="text" name="address" maxlength="255" class="address">
                        </label>

                        <label>建物名（アパート・マンションなど）<br>
                            <input type="text" name="building" maxlength="255" class="building">
                        </label>

                                                <p><input type="submit" value="次へ" class="button"></p>
                    </form>

                                        <script>
                                        // フォームのクライアント側検証
                                        (function(){
                                            const form = document.querySelector('form');
                                            const zipError = document.getElementById('zip-error');
                                            form.addEventListener('submit', function(e){
                                                const sei = form.querySelector('input[name="name_sei"]').value.trim();
                                                const mei = form.querySelector('input[name="name_mei"]').value.trim();
                                                const email = form.querySelector('input[name="email"]').value.trim();
                                                const password = form.querySelector('input[name="password"]').value;
                                                const p1 = form.querySelector('input[name="postal_code1"]').value.trim();
                                                const p2 = form.querySelector('input[name="postal_code2"]').value.trim();
                                                const city = form.querySelector('input[name="city"]').value.trim();
                                                const addr = form.querySelector('input[name="address"]').value.trim();
                                                // 必須チェック
                                                if(!sei || !mei || !email || !password || !p1 || !p2 || !city || !addr){
                                                    alert('必須項目を全て入力してください（姓・名・メール・パスワード・郵便番号・市区町村・番地）');
                                                    e.preventDefault();
                                                    return false;
                                                }
                                                // email 形式チェック（簡易）
                                                if(!/^\S+@\S+\.\S+$/.test(email)){
                                                    alert('正しいメールアドレスを入力してください');
                                                    e.preventDefault();
                                                    return false;
                                                }
                                                // パスワード長チェック
                                                if(password.length < 6){
                                                    alert('パスワードは6文字以上で入力してください');
                                                    e.preventDefault();
                                                    return false;
                                                }
                                                // 郵便番号数値チェック
                                                if(!/^\d{3}$/.test(p1) || !/^\d{4}$/.test(p2)){
                                                    alert('郵便番号は3桁-4桁で入力してください（数字のみ）');
                                                    e.preventDefault();
                                                    return false;
                                                }
                                                // 問題なければ送信
                                                return true;
                                            });
                                        })();
                                        </script>

                </div>
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
        .customer-input label {
                display: block;
                width: 80%;
                margin: 8px auto;
                text-align: left;
        }
        .customer-input input[type="text"],
        .customer-input input[type="email"],
        .customer-input input[type="password"],
        .customer-input input[type="tel"],
        .customer-input select {
                width: 100%;
                padding: 8px;
                box-sizing: border-box;
                margin-top: 6px;
        }
        .customer-input .postal { width: 48%; display: inline-block; }
        .button { background-color: #000; color: #fff; padding: 10px 20px; border-radius:4px; border:none; }
    </style>
        <script>
        // 郵便番号から住所を取得して自動補完（zipcloud）
        (function(){
            const p1 = document.getElementById('postal_code1');
            const p2 = document.getElementById('postal_code2');
            const prefecture = document.getElementById('prefecture');
            const city = document.querySelector('input[name="city"]');
            const address = document.querySelector('input[name="address"]');

            function lookupZip(){
                const v1 = p1.value.trim();
                const v2 = p2.value.trim();
                const zipErrorEl = document.getElementById('zip-error');
                zipErrorEl.style.display = 'none';
                zipErrorEl.textContent = '';
                if(!v1 || !v2) return;
                const zipcode = v1 + v2;
                // zipcloud API
                fetch('https://zipcloud.ibsnet.co.jp/api/search?zipcode=' + zipcode)
                    .then(res=>res.json())
                    .then(data=>{
                        if(data && data.results && data.results.length){
                            const r = data.results[0];
                            // r.address1=都道府県, address2=市区町村, address3=町域
                            const a1 = r.address1 || '';
                            const a2 = r.address2 || '';
                            const a3 = r.address3 || '';
                            // 都道府県を選択
                            let matched = false;
                            for(const opt of prefecture.options){
                                if(opt.value === a1){ opt.selected = true; matched = true; break; }
                            }
                            city.value = a2;
                            address.value = a3;
                            if(!matched){
                                // prefecture value not found in select (rare)
                                zipErrorEl.textContent = '注意：APIの都道府県が選択肢と一致しません。手動で確認してください。';
                                zipErrorEl.style.display = 'block';
                            }
                        } else {
                            // 見つからない場合はエラーメッセージを表示
                            zipErrorEl.textContent = '郵便番号に該当する住所が見つかりませんでした。手動で入力してください。';
                            zipErrorEl.style.display = 'block';
                        }
                    })
                    .catch(err=>{
                        console.log('zip lookup error', err);
                        zipErrorEl.textContent = '住所検索でエラーが発生しました。後ほど再度お試しください。';
                        zipErrorEl.style.display = 'block';
                    });
            }

            p1.addEventListener('blur', lookupZip);
            p2.addEventListener('blur', lookupZip);
            // Enter/自動補完対策: どちらかで入力が終わったら実行
            p1.addEventListener('input', ()=>{ if(p1.value.length===3 && p2.value.length>=4) lookupZip(); });
            p2.addEventListener('input', ()=>{ if(p2.value.length===4 && p1.value.length===3) lookupZip(); });
        })();
        </script>
    
</body>
</html>