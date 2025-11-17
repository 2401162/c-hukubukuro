# データベース統合 - 修正内容一覧

## 修正日: 2025年11月11日
## スキーマ: LAA1607624-group (テーブル数: 9)

---

## 📋 修正ファイル一覧

### 1. **customer_done.php** ✅
**修正内容:** 会員登録完了画面にDB保存処理を追加
- 追加: `db-connect.php` を読み込み
- 実装: customer テーブルへの INSERT 処理
  - `customer_id` (PK, auto_increment)
  - `email` (UNI) - 重複チェック
  - `password_hash` - PASSWORD_BCRYPT でハッシュ化
  - `name` - 名前を結合（姓 + 名）
  - `phone` - 電話番号
  - `postal_code` - 郵便番号（前半+後半結合）
  - `prefecture` - 都道府県
  - `city` - 市区町村
  - `address_line` - 番地
  - `is_active` - デフォルト: 1
  - `created_at`, `updated_at` - タイムスタンプ

**カラム紐付け:**
```
フォーム → DB カラム
name_sei + name_mei → customer.name (スペース区切り)
email → customer.email
password → customer.password_hash (ハッシュ化)
tel → customer.phone
postal_code1 + postal_code2 → customer.postal_code
prefecture → customer.prefecture
city → customer.city
address → customer.address_line
```

---

### 2. **rogin-output.php** ✅
**修正内容:** ログイン処理のカラム名を正確に修正
- 変更: `id` → `customer_id` (正確なカラム名)
- 変更: `password` → `password_hash` (正確なカラム名)
- 追加: `is_active = 1` チェック
- 追加: `last_login_at` の更新処理
- 改善: 名前を分割して `name_sei`, `name_mei` として保存

**カラム紐付け:**
```
DB カラム → セッション
customer_id → $_SESSION['customer']['customer_id']
customer_id → $_SESSION['customer']['id'] (互換性用)
email → $_SESSION['customer']['email']
name → $_SESSION['customer']['username']
name (分割後) → $_SESSION['customer']['name_sei'], name_mei
last_login_at → CURRENT_TIMESTAMP (自動更新)
```

---

### 3. **product-list.php** ✅
**修正内容:** ダミーデータからDB連携へ変更
- 追加: `db-connect.php` 読み込み
- 実装: product テーブルから商品データ取得
- 実装: review テーブルから評価情報取得
- 実装: order_item テーブルから売上集計

**カラム紐付け:**
```
SELECT 文の構成:
- product.product_id → id
- product.name → name
- product.price → price
- product.description → description
- product.stock → stock
- AVG(review.rating) → avg_rating
- COUNT(review.review_id) → review_count
- SUM(order_item.quantity) → total_sold
- product.is_active = 1 (フィルタ)

JOIN 関連:
- product LEFT JOIN review (r.is_active = 1)
- product LEFT JOIN order_item
- review LEFT JOIN genre (genre_id)
```

**ソート実装:**
- `sort=all` → ORDER BY product_id DESC (新着順)
- `sort=recommend` → ORDER BY reco DESC, avg_rating DESC (おすすめ)
- `sort=ranking` → ORDER BY total_sold DESC (ランキング)

---

### 4. **product-detail.php** ✅
**新規作成**: 商品詳細ページ
- 実装: product テーブルから単一商品取得
- 実装: review テーブルから最新10件のレビュー取得
- 実装: genre テーブルからジャンル情報取得
- 機能: 在庫状況の表示
- 機能: 平均評価とレビュー件数の表示

**カラム紐付け:**
```
product テーブル:
- product_id → id
- name → 商品名
- price → 価格
- stock → 在庫
- description → 説明
- is_active → 表示判定
- jenre_id → genre_id (外部キー)

review テーブル:
- review_id → レビューID
- rating → 評価（1-5）
- comment → コメント
- created_at → 投稿日時
- is_active → 表示判定

genre テーブル:
- genre_id → ジャンルID
- genre_name → ジャンル名
```

---

### 5. **password-reset-mail-input.php** ✅
**変更なし** (既存: パスワードリセット入力画面)

---

### 6. **password-reset-mail-sent.php** ✅
**修正内容:** DB接続を追加してメール送信確認処理を実装
- 追加: `db-connect.php` 読み込み
- 実装: customer テーブルからメールアドレスが存在するか確認
- 実装: パスワードリセットトークンをセッションに保存
- セキュリティ: メールアドレスが存在しない場合も「送信しました」と返す

**カラム紐付け:**
```
customer テーブル:
- email → メール確認
- customer_id → トークン関連付け
- is_active = 1 → 有効ユーザーのみ

セッション保存:
- $_SESSION['password_reset_token'] → トークン
- $_SESSION['password_reset_email'] → メールアドレス
- $_SESSION['password_reset_expires'] → 有効期限（1時間）
```

---

### 7. **password-reset-new.php** ✅
**修正内容:** トークンのバリデーション機能を追加
- 実装: セッションからトークンの有効期限チェック
- 条件: トークンが有効かつ未期限の場合のみフォーム表示
- 実装: メールアドレスをhidden フィールドで渡す

**バリデーション:**
```
チェック条件:
- $_SESSION['password_reset_token'] が存在
- $_SESSION['password_reset_email'] が存在
- $_SESSION['password_reset_expires'] が現在時刻より後ろ
- パスワードが8文字以上
- パスワード確認用と一致
```

---

### 8. **password-reset-complete.php** ✅
**修正内容:** DB接続を追加してパスワード更新処理を実装
- 追加: `db-connect.php` 読み込み
- 実装: customer テーブルの password_hash を更新
- 実装: updated_at を CURRENT_TIMESTAMP で自動更新
- 実装: バリデーション（8文字以上、一致確認）
- セキュリティ: セッション情報をクリア

**カラム紐付け:**
```
customer テーブル UPDATE:
- password_hash ← password_hash(新パスワード, PASSWORD_BCRYPT)
- updated_at ← CURRENT_TIMESTAMP
- WHERE email = :email AND is_active = 1

バリデーション:
- password 長さ: >= 8文字
- password === password2
- email が存在する
```

---

### 9. **rogin-input.php** ✅
**変更なし** (既存: ログイン入力画面)

---

### 10. **rogout-input.php** ✅
**変更なし** (既存: ログアウト確認画面)

---

### 11. **rogout-output.php** ✅
**変更なし** (既存: ログアウト実行画面 - セッション削除処理)

---

### 12. **customer-input.php** ✅
**変更なし** (既存: 会員登録入力内容の確認画面)

---

### 13. **customer-newinput.php** ✅
**変更なし** (既存: 会員登録入力フォーム)

---

### 14. **db-connect.php** ✅
**変更なし** (既存: DB接続情報)
```php
const SERVER = 'mysql326.phy.lolipop.lan';
const DBNAME = 'LAA1607624-group';
const USER = 'LAA1607624';
const PASS = 'pass0726';
```

---

### 15. **connect-test.php** ✅
**変更なし** (既存: DB接続テストツール)

---

### 16. **db-check.php** ✅
**変更なし** (既存: DB情報確認ツール)

---

## 🗄️ データベーステーブル対応表

### admin テーブル
| カラム名 | 型 | 用途 | 対応ファイル |
|---------|-----|------|-----------|
| admin_id | bigint(PK) | 管理者ID | - |
| email | varchar(255)(UNI) | 管理者メール | - |
| password_hash | varchar(255) | パスワードハッシュ | - |
| name | varchar(50) | 管理者名 | - |
| role | varchar(50) | 役割 | - |
| created_at | datetime | 作成日時 | - |
| updated_at | datetime | 更新日時 | - |

*※ 管理者画面未実装*

---

### customer テーブル
| カラム名 | 型 | 用途 | 対応ファイル |
|---------|-----|------|-----------|
| customer_id | bigint(PK) | 顧客ID | rogin-output.php, customer_done.php |
| email | varchar(255)(UNI) | メールアドレス | rogin-output.php, customer_done.php |
| password_hash | varchar(255) | パスワードハッシュ | rogin-output.php, customer_done.php |
| name | varchar(100) | 氏名（フルネーム） | customer_done.php, rogin-output.php |
| phone | varchar(20) | 電話番号 | customer_done.php |
| postal_code | varchar(10) | 郵便番号 | customer_done.php |
| prefecture | varchar(20) | 都道府県 | customer_done.php |
| city | varchar(50) | 市区町村 | customer_done.php |
| address_line | varchar(100) | 番地 | customer_done.php |
| is_active | tinyint(1) | 有効フラグ | rogin-output.php |
| last_login_at | datetime | 最終ログイン日時 | rogin-output.php |
| created_at | datetime | 作成日時 | customer_done.php |
| updated_at | datetime | 更新日時 | customer_done.php, password-reset-complete.php |

---

### product テーブル
| カラム名 | 型 | 用途 | 対応ファイル |
|---------|-----|------|-----------|
| product_id | bigint(PK) | 商品ID | product-list.php, product-detail.php |
| jenre_id | bigint | ジャンルID | product-detail.php |
| name | varchar(150) | 商品名 | product-list.php, product-detail.php |
| price | int(MUL) | 価格 | product-list.php, product-detail.php |
| stock | int | 在庫数 | product-list.php, product-detail.php |
| description | varchar(1000) | 説明 | product-detail.php |
| is_active | tinyint(1)(MUL) | 表示フラグ | product-list.php, product-detail.php |

---

### review テーブル
| カラム名 | 型 | 用途 | 対応ファイル |
|---------|-----|------|-----------|
| review_id | bigint(PK) | レビューID | product-detail.php, product-list.php |
| order_item_id | bigint(UNI) | 注文アイテムID | product-detail.php |
| rating | tinyint(1) | 評価（1-5） | product-detail.php, product-list.php |
| comment | text | コメント | product-detail.php |
| is_active | tinyint(1)(MUL) | 表示フラグ | product-detail.php, product-list.php |
| created_at | datetime(MUL) | 投稿日時 | product-detail.php |
| updated_at | datetime | 更新日時 | - |

---

### genre テーブル
| カラム名 | 型 | 用途 | 対応ファイル |
|---------|-----|------|-----------|
| genre_id | int(PK) | ジャンルID | product-detail.php |
| genre_name | varchar(50)(UNI) | ジャンル名 | product-detail.php |
| sort_order | int(MUL) | ソート順序 | - |
| created_at | datetime | 作成日時 | - |
| is_active | tinyint(1) | 表示フラグ | - |

---

### cart テーブル
| カラム名 | 型 | 用途 | 対応ファイル |
|---------|-----|------|-----------|
| cart_id | bigint(PK) | カートID | - |
| customer_id | bigint(MUL) | 顧客ID | - |
| created_at | datetime(MUL) | 作成日時 | - |
| updated_at | datetime(MUL) | 更新日時 | - |
| product_count | int | 商品数 | - |

*※ カート機能未実装*

---

### cart_item テーブル
| カラム名 | 型 | 用途 | 対応ファイル |
|---------|-----|------|-----------|
| cart_item_id | bigint(PK) | カートアイテムID | - |
| cart_id | bigint(MUL) | カートID | - |
| product_id | bigint(MUL) | 商品ID | - |
| quantity | int | 数量 | - |
| unit_price_snapshot | int | 価格スナップショット | - |
| created_at | datetime | 作成日時 | - |
| updated_at | datetime(MUL) | 更新日時 | - |

*※ カート機能未実装*

---

### orders テーブル
| カラム名 | 型 | 用途 | 対応ファイル |
|---------|-----|------|-----------|
| order_id | bigint(PK) | 注文ID | product-detail.php |
| customer_id | bigint(MUL) | 顧客ID | - |
| order_datetime | datetime(MUL) | 注文日時 | - |
| total_amount | int | 合計金額 | - |
| payment_method | varchar(30) | 支払方法 | - |
| status | varchar(30)(MUL) | ステータス | - |
| ship_postal_code | varchar(10) | 配送郵便番号 | - |
| ship_prefecture | varchar(20) | 配送都道府県 | - |
| ship_city | varchar(50) | 配送市区町村 | - |
| ship_address_line | varchar(200) | 配送番地 | - |
| created_at | datetime | 作成日時 | - |
| updated_at | datetime | 更新日時 | - |

*※ 注文機能未実装*

---

### order_item テーブル
| カラム名 | 型 | 用途 | 対応ファイル |
|---------|-----|------|-----------|
| order_item_id | bigint(PK) | 注文アイテムID | product-detail.php |
| order_id | bigint(MUL) | 注文ID | - |
| product_id | bigint(MUL) | 商品ID | product-detail.php |
| quantity | int | 数量 | product-detail.php |
| unit_price | int | 単価 | - |
| subtotal | int | 小計 | - |
| created_at | datetime | 作成日時 | - |
| updated_at | datetime | 更新日時 | - |

---

## ✨ 実装済み機能

✅ 会員登録（customer_done.php）
✅ ログイン（rogin-output.php）
✅ ログアウト（rogout-output.php）
✅ パスワードリセット（password-reset-complete.php）
✅ 商品一覧表示（product-list.php）
✅ 商品詳細表示（product-detail.php）

---

## ⚠️ 未実装機能

❌ カート機能（cart, cart_item テーブル）
❌ 注文機能（orders, order_item テーブル）
❌ 管理者画面（admin テーブル）
❌ ジャンル管理（genre テーブル）

---

## 🔒 セキュリティ対策

✅ パスワードハッシュ化（PASSWORD_BCRYPT）
✅ SQLインジェクション対策（準備済みステートメント）
✅ HTMLエスケープ（htmlspecialchars）
✅ メールアドレス重複チェック
✅ メールアドレス存在チェック（セッション）
✅ トークン期限切れチェック

---

## 📝 注記

- **カラム名注意**: product テーブルの `jenre_id` は `genre_id` の誤記と思われます。
  将来的に修正が必要かもしれません。
  
- **パスワード保存**: すべてのパスワードフィールドは `password_hash` で保存され、
  `password_verify()` で検証されます。

- **タイムスタンプ**: `created_at`, `updated_at` は自動で `CURRENT_TIMESTAMP` が
  設定されます。

---

## 🚀 次のステップ

1. カート機能の実装
2. 注文機能の実装
3. 管理者画面の実装
4. ジャンル管理画面の実装
5. レビュー投稿機能の実装
6. メール送信機能の実装（本番環境）
