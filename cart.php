<?php
session_start();

// 初期データ（仮）
$products = [
  1 => ["name" => "【福袋】アニメグッズ詰め合わせ", "price" => 3000, "img" => "https://via.placeholder.com/100", "stock" => true],
  2 => ["name" => "【福袋】お菓子詰め合わせ", "price" => 2000, "img" => "https://via.placeholder.com/100", "stock" => true],
];

// カート初期化
if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [1 => 1, 2 => 1];
}

// 商品削除
if (isset($_POST['remove'])) {
  $id = intval($_POST['remove']);
  unset($_SESSION['cart'][$id]);
}

// 数量更新
if (isset($_POST['update'])) {
  $id = intval($_POST['id']);
  $qty = max(1, intval($_POST['qty']));
  $_SESSION['cart'][$id] = $qty;
}

// 合計計算
$subtotal = 0;
foreach ($_SESSION['cart'] as $id => $qty) {
  $subtotal += $products[$id]['price'] * $qty;
}
$shipping = 500;
$total = $subtotal + $shipping;
?>

<!-- ✅ header 読み込み -->
<?php include __DIR__ . '/header.php'; ?>

<main style="padding:40px;">
  <h1>ショッピングカート</h1>

  <div class="cart-container" style="display:flex;justify-content:space-between;align-items:flex-start;">
    <div class="cart-items" style="width:70%;">
      <?php if (empty($_SESSION['cart'])): ?>
        <p>カートに商品がありません。</p>
      <?php else: ?>
        <?php foreach ($_SESSION['cart'] as $id => $qty): ?>
          <?php $item = $products[$id]; ?>
          <div class="cart-item" style="display:flex;border:1px solid #ddd;border-radius:6px;padding:15px;margin-bottom:20px;">
            <img src="<?= htmlspecialchars($item['img']) ?>" alt="商品画像" style="width:100px;height:100px;object-fit:cover;border-radius:6px;">
            <div style="margin-left:15px;flex-grow:1;">
              <h3 style="margin:0 0 5px 0;font-size:16px;"><?= htmlspecialchars($item['name']) ?></h3>
              <p style="margin:3px 0;color:#555;">在庫：<?= $item['stock'] ? 'あり' : 'なし' ?></p>
              <p style="margin:3px 0;color:#555;">お届け予定：〇月〇日〜〇月〇日</p>
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;justify-content:space-between;">
              <strong><?= number_format($item['price'] * $qty) ?>円</strong>
              <div>
                <select id="qty-<?= $id ?>" onchange="updateQty(<?= $id ?>)">
                  <?php for ($i = 1; $i <= 10; $i++): ?>
                    <option value="<?= $i ?>" <?= $i == $qty ? 'selected' : '' ?>><?= $i ?></option>
                  <?php endfor; ?>
                </select>
                <form method="post" style="display:inline;">
                  <button type="submit" name="remove" value="<?= $id ?>">削除する</button>
                </form>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div class="summary-box" style="width:25%;border:1px solid #ddd;border-radius:6px;padding:20px;text-align:right;">
      <p>小計：<?= number_format($subtotal) ?>円</p>
      <p>送料：<?= number_format($shipping) ?>円</p>
      <p style="font-weight:bold;">合計：<?= number_format($total) ?>円</p>
      <button style="background:#ff3b3b;color:#fff;padding:10px 20px;border:none;border-radius:5px;margin-top:20px;font-weight:bold;cursor:pointer;">
        購入に進む
      </button>
    </div>
  </div>
</main>

<!-- ✅ footer 読み込み -->
<?php include __DIR__ . '/footer.php'; ?>

<script>
function updateQty(id) {
  const qty = document.getElementById("qty-" + id).value;
  const formData = new FormData();
  formData.append("update", "1");
  formData.append("id", id);
  formData.append("qty", qty);

  fetch("cart.php", { method: "POST", body: formData })
    .then(() => location.reload());
}
</script>
