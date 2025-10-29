<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>管理者ページログイン</title>
  <style>
    body {
      font-family: "Hiragino Kaku Gothic ProN", "Noto Sans JP", sans-serif;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100vh;
      background-color: #fff;
    }
    h1 {
      font-size: 28px;
      margin-bottom: 40px;
    }
    .login-box {
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 40px 50px;
      width: 400px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }
    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
    }
    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
    }
    button {
      width: 100%;
      background-color: #2d2d2d;
      color: #fff;
      border: none;
      padding: 12px;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
    }
    button:hover {
      background-color: #444;
    }
  </style>
</head>
<body>
  <h1>管理者ページログイン</h1>
  <div class="login-box">
    <form>
      <label for="admin-id">管理者ID</label>
      <input type="text" id="admin-id" name="admin-id" required>
      
      <label for="password">パスワード</label>
      <input type="password" id="password" name="password" required>
      
      <button type="submit">ログイン</button>
    </form>
  </div>
</body>
</html>
