<?php
session_start();
require_once __DIR__ . "/db-connect.php";   // ← DB 接続ファイル読み込み

// ==============================
// DB 接続
// ==============================
try {
    $pdo = new PDO($connect, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit("DB接続に失敗しました：" . $e->getMessage());
}

// ==============================
// 商品データ取得
// ==============================
// products テーブル前提：id, name, price, img, stock
$sql = "SELECT id, name, price, img, stock FROM products";
$stmt = $pdo->query($sql);

$products = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $products[$row["id"]] = $row;
}

// ==============================
// カート初期化（デモ用）
// ==============================
if (!isset($_SESSION['cart'])) {
    // products の最初の2つを自動で入れる例
    $pids = array_keys($products);
    $_SESSION['cart'] = [
        $pids[0] => 1,
        $pids[1] => 1
    ];
}

// ==============================
// 商品削除
// ==============================
if (isset($_POST['remove'])) {
    $id = intval($_POST['remove']);
    unset($_SESSION['cart'][$id]);
}

// ==============================
// 数量更新
// ==============================
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $qty = max(1, intval($_POST['qty']));
    $_SESSION['cart'][$id] = $qty;
}

// ==============================
// 合計計算
// ==============================
$subtotal = 0;
foreach ($_SESSION['cart'] as $id => $qty) {
    if (isset($products[$id])) {
        $subtotal += $products[$id]['price'] * $qty;
    }
}

$shipping = 500;
$total = $subtotal + $shipping;

?>

<?php include __DIR__ . '/admin-header.php'; ?>

<style>
/* ここからデザイン（あなたのスクショと同じスタイル） */
.cart-wrapper {
  width: 100%;
  max-width: 1200px;
  margin: 40px auto;
}

.cart-title {
  font-size: 32px;
  font-weight: bold;
  margin-bottom: 30px;
}

.cart-flex {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.cart-item-box {
  width: 70%;
}

.cart-item {
  display: flex;
  border: 1px solid #ddd;
  border-radius: 10px;
  padding: 20px;
  margin-bottom: 20px;
  background: #fff;
}

.cart-item img {
  width: 120px;
  height: 120px;
  object-fit: cover;
  border-radius: 6px;
}

.cart-item-info {
  margin-left: 15px;
  flex-grow: 1;
}

.cart-item-name {
  font-size: 18px;
  font-weight: bold;
}

.cart-right {
  width: 25%;
  border: 1px solid #ddd;
  border-radius: 10px;
  padding: 20px;
  background: #fff;
}

.buy-btn {
  background: #ff4a4a;
  color: #fff;
  width: 100%;
  padding: 12px 0;
  border: none;
  border-radius: 6px;
  margin-top: 20px;
  font-size: 16px;
  cursor: pointer;
}

.remove-btn {
  background: #ddd;
  border: none;
  padding: 5px 12px;
  border-radius: 4px;
  cursor: pointer;
}
</style>

<div class="cart-wrapper">
  <div class="cart-title">ショッピングカート</div>

  <div class="cart-flex">

    <!-- 左：商品一覧 -->
    <div class="cart-item-box">

      <?php if (empty($_SESSION['cart'])): ?>
        <p>カートに商品がありません。</p>

      <?php else: ?>
        <?php foreach ($_SESSION['cart'] as $id => $qty): ?>
          <?php if (!isset($products[$id])) continue; ?>
          <?php $item = $products[$id]; ?>

          <div class="cart-item">
            <img src="<?= htmlspecialchars($item['img']) ?>" alt="商品画像">

            <div class="cart-item-info">
              <div class="cart-item-name"><?= htmlspecialchars($item['name']) ?></div>
              <p>在庫：<?= $item['stock'] ? 'あり' : 'なし' ?></p>
              <p>お届け予定：〇月〇日〜〇月〇日</p>
            </div>

            <div style="text-align:right;">
              <div style="font-size: 18px; font-weight:bold;">
                <?= number_format($item['price'] * $qty) ?>円
              </div>

              <div style="margin-top:10px;">
                <select id="qty-<?= $id ?>" onchange="updateQty(<?= $id ?>)">
                  <?php for ($i = 1; $i <= 10; $i++): ?>
                    <option value="<?= $i ?>" <?= $i == $qty ? 'selected' : '' ?>><?= $i ?></option>
                  <?php endfor; ?>
                </select>

                <form method="post" style="display:inline;">
                  <button type="submit" name="remove" value="<?= $id ?>" class="remove-btn">
                    削除する
                  </button>
                </form>
              </div>
            </div>

          </div>

        <?php endforeach; ?>
      <?php endif; ?>

    </div>

    <!-- 右：合計 -->
    <div class="cart-right">
      <p>小計：<?= number_format($subtotal) ?>円</p>
      <p>送料：<?= number_format($shipping) ?>円</p>
      <p style="font-weight:bold;">合計：<?= number_format($total) ?>円</p>

      <button class="buy-btn">購入に進む</button>
    </div>

  </div>
</div>

<script>
function updateQty(id) {
  const qty = document.getElementById("qty-" + id).value;
  const fd = new FormData();
  fd.append("update", 1);
  fd.append("id", id);
  fd.append("qty", qty);

  fetch("cart.php", { method: "POST", body: fd })
    .then(() => location.reload());
}
</script>

<?php include __DIR__ . '/admin-footer.php'; ?>
