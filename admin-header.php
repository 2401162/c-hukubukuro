<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>PHP Sample Programs</title>
</head>
<style media="screen">
  /* Vue 初期化前のフラッシュ防止 */
  [v-cloak] { display: none !important; }

/* 検索バー（見た目を整える） */
    .search-bar{
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
      margin: 10px 0;
      padding: 8px;
      border-radius: 8px;
      background: #fafafa;
      border: 1px solid #ececec;
      box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }

    /* テキスト入力（伸縮して広がる） */
    .search-bar input[type="text"]{
      flex: 1 1 320px;
      min-width: 160px;
      padding: 8px 12px;
      border: 1px solid #dcdcdc;
      border-radius: 6px;
      font-size: 14px;
      color: #333;
      background: #fff;
      box-sizing: border-box;
    }

    /* select / button の基本スタイル統一 */
    .search-bar select,
    .search-bar button{
      padding: 8px 12px;
      border-radius: 6px;
      border: 1px solid #dcdcdc;
      background: #fff;
      font-size: 14px;
      cursor: pointer;
    }

    /* クリアボタン（目立たせすぎない） */
    .search-bar button{
      background: linear-gradient(180deg,#f7f7f7,#ffffff);
      color: #333;
    }

    /* ボタンのホバー効果 */
    .search-bar button:hover,
    .search-bar select:hover,
    .search-bar input[type="text"]:focus{
      border-color: #bfcbd6;
      outline: none;
      box-shadow: 0 0 0 3px rgba(100,140,200,0.06);
    }

    /* 小さい画面での調整 */
    @media (max-width: 600px){
      .search-bar {
        padding: 6px;
        gap: 6px;
      }
      .search-bar input[type="text"]{ flex-basis: 100%; }
      .search-bar select, .search-bar button{ flex-basis: auto; }
    }

    nav ul li{
      display: inline-block;
      margin: 0px 20px;
      padding: 10px;
      color: #ffffff;
    }
    nav li:hover{
      transform: translateY(-2px);
    }
    nav ul li a {
      color: #ffffff;
      text-decoration: none; /* 下線を消したい場合はこれも追加！ */
    }
    .header-bar {
      display: flex;
      align-items: center;
      justify-content: flex-start; /* 必要に応じて調整 */
      margin-bottom: 20px;
    }

    .header-bar h1 {
      margin: 0;
      font-size: 32px;
    }
    .top-button{
        padding: 15px 40px;
        font-size: 16px;
        font-weight: 600;
        color: white;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        .top-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 7px 20px rgba(102, 126, 234, 0.5);
    }
    .add-button {
        background-color: #2196F3;
        color: white;
        padding: 10px 20px;
        margin-left: 10px; 
        border: none;
        border-radius: 50px;
        cursor: pointer;
        display: flex;
    }
      .add-button:hover {
        background-color: #1976D2;
      }
    nav{
        background: #5C6876;
        font-size: 25px;
        color: #ffffff;
    }
    .a{
      font-weight: bold;
      font-size: 20px;
    }
    table {
    border-collapse: collapse;
    width: 100%;
    margin-top: 10px;
  }
  th, td {
    padding: 8px;
    border: 1px solid #ccc;
    text-align: left;
  }
  th {
    background-color: #f0f0f0;
  }
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background: #fff;
  padding: 30px;
  border-radius: 10px;
  width: 600px;
  max-width: 90%;
  box-shadow: 0 0 10px rgba(0,0,0,0.3);
}

.modal-content h2 {
  margin-top: 0;
  font-size: 1.5em;
  margin-bottom: 20px;
  text-align: center;
}

.modal-content form > div {
  margin-bottom: 15px;
  display: flex;
  flex-direction: column;
}

.modal-content label {
  font-weight: bold;
  margin-bottom: 5px;
}

.modal-content input[type="text"],
.modal-content input[type="number"],
.modal-content textarea,
.modal-content select {
  padding: 10px;
  font-size: 1em;
  border: 1px solid #ccc;
  border-radius: 5px;
  width: 100%;
  box-sizing: border-box;
}

.modal-content textarea {
  resize: vertical;
  min-height: 80px;
}

.modal-buttons {
  margin-top: 20px;
  text-align: right;
}

.modal-buttons button {
  padding: 10px 20px;
  margin-left: 10px;
  font-size: 1em;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.modal-buttons button[type="submit"] {
  background-color: #337ab7;
  color: white;
}

.modal-buttons button[type="button"] {
  background-color: #ccc;
}
 
</style>
<body>