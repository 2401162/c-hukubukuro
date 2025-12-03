<style>
/* ===== 商品全体レイアウト ===== */
.syouhin {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 40px;
    margin: 0 auto;
}

.list-link {
    margin: 5px;
    font-size: 15px;
    text-align: center;
}

/* ===== 商品部分 ===== */
.product {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    gap: 40px;
    width: 100%;
}

/* 商品画像 */
.product img {
    width: 500px;
    border-radius: 10px;
}

/* 商品情報 */
.product form {
    flex: 1;
    min-width: 280px;
}

.product h3 {
    font-size: 24px;
    margin-bottom: 10px;
}

.product p {
    margin: 8px 0;
}

/* 価格の見た目 */
.product p.price {
    font-size: 28px;
    margin: 8px 0;
}

.product p.price small {
    font-size: 14px;
    margin-left: 3px;
}

.product .average-rating small {
    font-size: 12px;
}

/* ===== セレクトとボタン ===== */
select {
    padding: 5px;
    font-size: 14px;
    margin-top: 5px;
}

button {
    background-color: #ff8c8c;
    border: none;
    color: #fff;
    font-size: 16px;
    padding: 10px 20px;
    border-radius: 8px;
    margin-top: 15px;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background-color: #ff6b6b;
}

/* ===== 商品説明/レビュー ===== */
.description {
    text-align: left;
    margin-top: 20px;
    width: 90%;
}

.description p:first-child {
    font-weight: bold;
    font-size: 18px;
    color: #444;
}

.description hr {
    border: none;
    border-top: 2px solid #878787;
    margin: 10px 0 20px 0;
}

.description p:last-child {
    line-height: 1.6;
}

/* ===== レビューボックス ===== */
.review-box {
    width: 100%;
    padding: 12px 0;
    border-bottom: 1px solid #ddd;
    box-sizing: border-box;
}

.review-box.no-reviews {
    padding: 16px;
    background: #fff9f6;
    border-radius: 8px;
    border: 1px dashed #f0c6c6;
    color: #444;
    width: 100%;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.stars {
    margin-left: 8px;
    font-size: 18px;
}

.review-comment {
    line-height: 1.6;
}

/* ===== レスポンシブ対応 ===== */
@media (max-width: 768px) {
    .product {
        flex-direction: column;
        align-items: center;
    }

    .product img {
        width: 80%;
    }

    .product form {
        width: 90%;
    }

    .description {
        width: 95%;
    }
}
</style>