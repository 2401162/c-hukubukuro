<style>
/* 全体レイアウト */
body {
    font-family: "Noto Sans JP", sans-serif;
    background-color: #fff;
    margin: 0;
    padding: 0;
}

h2 {
    margin: 20px;
    color: #333;
}

/* 購入履歴コンテナ */
.buy {
    display: flex;
    justify-content: center;
    padding: 20px;
    margin-left: 20px;
    margin-right: 20px;
    margin-bottom: 10px;
    margin-top: 10px;
}

.rireki {
    display: flex;
    flex-direction: column;
    gap: 20px;
    width: 90%;
    max-width: 600px;
}

/* 各購入カード */
.purchase-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border: 1px solid #eee;
    border-radius: 10px;
    padding: 15px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    background-color: #fff;
}

/* 画像部分 */
.card-image {
    flex-shrink: 0;
    width: 80px;
    height: 80px;
    margin-right: 15px;
}

.order_image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
}

/* 商品情報部分 */
.card-main {
    flex-grow: 1;
}

.product-name {
    font-weight: bold;
    margin: 0 0 5px 0;
    font-size: 16px;
}

.order-date,
.quantity {
    margin: 2px 0;
    font-size: 14px;
    color: #666;
}

/* 右側の金額・レビュー */
.right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.right h3 {
    margin: 0;
    font-size: 18px;
}

.right a {
    transition: .3s;
    padding: 24px 48px;
    border-radius: 5px;
    background-color: #c9c9c9ff;
    text-decoration: none;
    color: #000000;
    font-size: 10px;
}

.right a:hover {
    background-color: #757c83ff;
    box-shadow: 0 4px 10px rgba(25, 118, 210, 0.18);
}

</style>