<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require 'header.php';
require_once 'db-connect.php';

$sort = $_GET['sort'] ?? 'all';
$allowed = ['all','recommend','ranking'];
if (!in_array($sort, $allowed, true)) { $sort = 'all'; }

/* ==== データベースから商品データ取得 ==== */
$products = [];
$dbError = '';
try {
    $pdo = new PDO(
        $connect,
        USER,
        PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    
    // product テーブル: product_id (PK), jenre_id, name, price, stock, description, is_active
    // review テーブル: review_id (PK), order_item_id, rating, comment, is_active, created_at, updated_at
  // 修正: review は order_item を経由して product に紐付くため、
  // order_item を先に結合し、そこから review を結合する。
  $query = "
    SELECT 
      p.product_id AS id,
      p.name,
      p.image_path AS image,
      p.price,
      p.description,
      p.stock,
      COALESCE(ROUND(AVG(r.rating), 1), 0) AS avg_rating,
      COUNT(r.review_id) AS review_count,
      COALESCE(SUM(oi.quantity), 0) AS total_sold,
      CASE WHEN p.product_id IN (SELECT product_id FROM product WHERE is_active = 1 LIMIT 5) THEN 1 ELSE 0 END AS reco
    FROM product p
    LEFT JOIN order_item oi ON oi.product_id = p.product_id
    LEFT JOIN review r ON r.order_item_id = oi.order_item_id AND r.is_active = 1
    WHERE p.is_active = 1
    GROUP BY p.product_id, p.name, p.price, p.description, p.stock, p.image_path
  ";
    
    if ($sort === 'recommend') {
        $query .= " ORDER BY reco DESC, avg_rating DESC, total_sold DESC, p.product_id DESC";
    } elseif ($sort === 'ranking') {
        $query .= " ORDER BY total_sold DESC, p.product_id DESC";
    } else {
        $query .= " ORDER BY p.product_id DESC";
    }
    
    try {
      $stmt = $pdo->prepare($query);
      $stmt->execute();
      $products = $stmt->fetchAll();
    } catch (PDOException $e) {
      // カラムが存在しない等のエラーであれば image を外したクエリで再試行
      if (stripos($e->getMessage(), 'unknown column') !== false || stripos($e->getMessage(), '1054') !== false) {
        error_log('Product query: image column missing, retrying without image');
          // image カラムを除いたクエリを作り直す
        $queryNoImage = str_replace('      p.image_path AS image,\n', '', $query);
        $queryNoImage = str_replace(', p.image_path', '', $queryNoImage);
        $queryNoImage = str_replace(', p.image_path', '', $queryNoImage);
        $queryNoImage = str_replace('p.image_path', '', $queryNoImage);
        // GROUP BY から p.image_path を削除
        $queryNoImage = str_ireplace(', p.image_path', '', $queryNoImage);
        try {
          $stmt = $pdo->prepare($queryNoImage);
          $stmt->execute();
          $products = $stmt->fetchAll();
        } catch (PDOException $e2) {
          throw $e2; // 元の catch に任せる
        }
      } else {
        throw $e;
      }
    }
    
    // 各商品に画像パス付与（DBの image_path（alias image）を優先、無ければ空）
    foreach ($products as &$p) {
      // DB に image_path（uploads/... など）があればそのまま使う。無ければ空にしてクライアント側でプレースホルダー表示
      if (empty($p['image'])) {
        $p['image'] = '';
      }
    }
    unset($p);
    
} catch (PDOException $e) {
  // DBエラーはログに出しつつ、ページ上に分かりやすく表示する
  error_log("Product DB Error: " . $e->getMessage());
  $dbError = 'データベースエラーが発生しました。管理者に問い合わせてください。';
  $products = [];
}

// フォールバック: 上の集計クエリで何らかの理由で結果が空になっている場合は
// 単純な product テーブルのみの取得を試みる（JOIN を外して商品が存在するか確認）
$usedFallback = false;
// NOTE: 集計クエリで例外が出た場合でも、商品自体は表示したいため
// dbError の有無に関わらず products が空ならフォールバックを試みる
if (empty($products)) {
  try {
    $fbStmt = $pdo->prepare(
      "SELECT product_id AS id, name, price, description, stock, 0 AS avg_rating, 0 AS review_count, 0 AS total_sold, 0 AS reco
       FROM product
       WHERE is_active = 1
       ORDER BY product_id DESC"
    );
    $fbStmt->execute();
    $fb = $fbStmt->fetchAll();
    if (!empty($fb)) {
      $products = $fb;
      foreach ($products as &$p) {
        // image_path が無ければ空に設定してクライアント側でプレースホルダー表示
        if (empty($p['image'])) {
            $p['image'] = '';
        }
      }
      unset($p);
      $usedFallback = true;
    }
  } catch (PDOException $e) {
    error_log('Product fallback error: ' . $e->getMessage());
  }
}

// NOTE: ダミーデータは表示しない。実際に登録されている商品のみ表示する。
// products が空の場合はページ上で「商品がありません」と表示する。

?>
<style>
.breadcrumb{max-width:1200px;margin:8px auto 0;padding:0 12px;color:#777;font-size:12px}
.list-header{display:flex;align-items:center;justify-content:space-between;gap:16px;margin:16px auto 24px;max-width:1200px;padding:0 12px}
.list-title{font-size:20px;font-weight:700;color:#333}
.badge{display:inline-block;font-size:11px;padding:2px 8px;border-radius:999px;margin-left:6px;background:#ffecec;color:#ec4c4c;border:1px solid #ffd7d7;vertical-align:middle}
.sort-tabs{display:flex;gap:14px}
.sort-tabs a{display:inline-block;padding:8px 14px;border:1px solid #ddd;border-radius:999px;text-decoration:none;font-size:13px;background:#fff}
.sort-tabs a.active{border-color:#ec4c4c;color:#ec4c4c;font-weight:700}
/* select をタブ風に見せる */
.sort-tabs select{appearance:none;-webkit-appearance:none;-moz-appearance:none;padding:8px 14px;border:1px solid #ddd;border-radius:999px;background:#fff;font-size:13px;cursor:pointer}
.product-wrap{max-width:1200px;margin:0 auto 48px;padding:0 12px}
.product-grid{display:grid;grid-template-columns:1fr;gap:16px;align-items:stretch}
@media (min-width:600px){.product-grid{grid-template-columns:repeat(2,1fr)}}
@media (min-width:900px){.product-grid{grid-template-columns:repeat(3,1fr)}}
@media (min-width:1100px){.product-grid{grid-template-columns:repeat(4,1fr)}}
.card{background:#fff;border:1px solid #eee;border-radius:12px;overflow:hidden;display:block;text-decoration:none;height:100%}
.thumb{aspect-ratio:1/1;width:100%;object-fit:cover;display:block;background:#f6f6f6}
.body{padding:10px 12px;display:flex;flex-direction:column;gap:6px}
.name{font-size:14px;font-weight:600;color:#333;line-height:1.4;margin:0}
.caption{font-size:12px;color:#777}
.price{font-size:14px;color:#555}
.rating{font-size:11px;color:#f90}
.pager-wrap{max-width:1200px;margin:8px auto 24px;padding:0 12px;display:flex;align-items:center;justify-content:space-between;gap:12px}
.pager{display:flex;flex-wrap:wrap;gap:8px}
.pager button{border:1px solid #ddd;background:#fff;padding:6px 10px;border-radius:8px;cursor:pointer}
.pager button[disabled]{opacity:.4;cursor:not-allowed}
.pager .is-active{border-color:#ec4c4c;color:#ec4c4c;font-weight:700}
.help{font-size:12px;color:#666}
.notice{max-width:1200px;margin:8px auto 0;padding:0 12px;color:#a33;font-size:13px}
</style>

<div class="breadcrumb">ホーム &gt; 商品一覧</div>

<div class="list-header">
  <div class="list-title">
    商品一覧
    <?php if($sort==='recommend'): ?><span class="badge">おすすめ順</span><?php endif; ?>
    <?php if($sort==='ranking'): ?><span class="badge">ランキング順</span><?php endif; ?>
  </div>
  <div class="sort-tabs">
    <!-- タブ表示からプルダウンに変更（形はそのまま見えるようスタイルを合わせる） -->
    <label for="sortSelect" style="display:none">並び替え</label>
    <select id="sortSelect" name="sort" aria-label="並び替え">
      <option value="all" <?= $sort==='all' ? 'selected' : '' ?>>新着</option>
      <option value="recommend" <?= $sort==='recommend' ? 'selected' : '' ?>>おすすめ</option>
      <option value="ranking" <?= $sort==='ranking' ? 'selected' : '' ?>>ランキング</option>
    </select>
  </div>
</div>

<?php if (!empty($dbError)): ?>
  <div class="notice"><?= htmlspecialchars($dbError, ENT_QUOTES, 'UTF-8') ?></div>
<?php elseif (!empty($usedFallback)): ?>
  <div class="notice">注意: 集計クエリで結果が取得できなかったため、簡易表示モードで商品を読み込んでいます。レビューや売上の集計値は表示されません。</div>
<?php endif; ?>

<div class="pager-wrap">
  <div class="help"><span id="countText"></span></div>
  <div class="pager" id="pager"></div>
</div>

<div class="product-wrap">
  <div class="product-grid" id="grid" aria-live="polite"></div>
</div>

<div class="pager-wrap">
  <div class="help">ページ移動</div>
  <div class="pager" id="pagerBottom"></div>
</div>

<script>
const PAGE_SIZE = 20;
const PRODUCTS = <?=
  json_encode($products, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
?>;

function renderPage(page=1){
  const total = PRODUCTS.length;
  // 商品が0件ならメッセージ表示してページャを消す
  if (total === 0) {
    const grid = document.getElementById('grid');
    grid.innerHTML = '<p class="notice">現在、商品は登録されていません。</p>';
    document.getElementById('countText').textContent = '0件';
    document.getElementById('pager').innerHTML = '';
    document.getElementById('pagerBottom').innerHTML = '';
    return;
  }
  const pages = Math.max(1, Math.ceil(total / PAGE_SIZE));
  if(page<1) page=1;
  if(page>pages) page=pages;

  const start = (page-1)*PAGE_SIZE;
  const slice = PRODUCTS.slice(start, start+PAGE_SIZE);

  const grid = document.getElementById('grid');
  grid.innerHTML = slice.map(p => {
    // 画像が無ければプレースホルダーを表示（onerror でも表示）
    const imgSrc = p.image || '';
    const thumbHtml = imgSrc 
      ? `<img class="thumb" src="${escapeAttr(imgSrc)}" alt="${escapeHtml(p.name)}" onerror="this.outerHTML='<div style=\\"background:#f0f0f0;width:100%;aspect-ratio:1/1;display:flex;align-items:center;justify-content:center;color:#999;font-size:12px;text-align:center;\\"><span>画像未設定</span></div>'" />`
      : `<div style="background:#f0f0f0;width:100%;aspect-ratio:1/1;display:flex;align-items:center;justify-content:center;color:#999;font-size:12px;text-align:center;"><span>画像未設定</span></div>`;
    return `
    <a class="card" href="product-detail.php?id=${p.id}" aria-label="${escapeHtml(p.name)}の詳細へ">
      ${thumbHtml}
      <div class="body">
        <div class="name">${escapeHtml(p.name)}</div>
        <div class="caption">売上: ${Number(p.total_sold||0)} 件 / 評価: ${(p.avg_rating ? p.avg_rating + '★ (' + p.review_count + '件)' : '未評価')}</div>
        <div class="price">¥${Number(p.price||0).toLocaleString()}</div>
      </div>
    </a>
  `;
  }).join('');

  const countText = document.getElementById('countText');
  countText.textContent = `${total}件中 ${(start+1)}〜${Math.min(start+slice.length, total)}件を表示`;

  renderPager('pager', page, pages);
  renderPager('pagerBottom', page, pages);
}

function renderPager(id, current, pages){
  const el = document.getElementById(id);
  const btn = (label, go, disabled=false, active=false) => `
    <button ${disabled?'disabled':''} ${active?'class="is-active"':''}
      onclick="${disabled?'void(0)':`renderPage(${go})`}">${label}</button>
  `;
  let html = '';
  html += btn('«', 1, current===1);
  html += btn('‹', current-1, current===1);

  const windowSize = 5;
  let start = Math.max(1, current - Math.floor(windowSize/2));
  let end   = Math.min(pages, start + windowSize - 1);
  start = Math.max(1, end - windowSize + 1);

  for(let i=start; i<=end; i++){
    html += btn(i, i, false, i===current);
  }

  html += btn('›', current+1, current===pages);
  html += btn('»', pages, current===pages);
  el.innerHTML = html;
}

function escapeHtml(s){ return String(s).replace(/[&<>"']/g, m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m])); }
function escapeAttr(s){ return escapeHtml(s).replace(/`/g,'&#96;'); }

renderPage(Number(new URL(location.href).searchParams.get('page')) || 1);
</script>

<script>
  // プルダウンでソートを変更したときに GET パラメータで再読み込み
  (function(){
    const sel = document.getElementById('sortSelect');
    if (!sel) return;
    sel.addEventListener('change', function(){
      const sp = new URLSearchParams(location.search);
      sp.set('sort', this.value);
      sp.delete('page'); // ソート変更時はページを先頭に戻す
      const q = sp.toString();
      location.search = q ? ('?' + q) : '';
    });
  })();
</script>

<?php
// フッターがない環境でのエラー抑止（存在するときだけ読み込む）
$footerPath = __DIR__ . '/footer.php';
if (is_file($footerPath)) { require $footerPath; }
?>



