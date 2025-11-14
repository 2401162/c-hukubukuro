<footer class="site-footer">
  <div class="footer-inner">
    <div class="footer-brand">
      <span class="footer-title">福袋販売サイト</span>
      <small class="copyright">&copy; <?= date('Y') ?> Fukubukuro Store</small>
    </div>
    <nav class="footer-links" aria-label="フッターナビ">
      <a href="/about.php">運営情報</a>
      <a href="/guide.php">ご利用ガイド</a>
      <a href="/contact.php">お問い合わせ</a>
      <a href="/terms.php">利用規約</a>
      <a href="/privacy.php">プライバシー</a>
    </nav>
  </div>
</footer>

<style>
.site-footer{
  background:#ec4c4cff; color:#fff;
  margin-top:auto;  /* ← .page が flex なので最下部へ押し出される */
}
.site-footer .footer-inner{
  max-width:1200px; margin:0 auto; padding:16px 12px;
  display:flex; align-items:center; justify-content:space-between; gap:16px;
}
.footer-title{ font-weight:700; letter-spacing:.04em; }
.copyright{ display:block; opacity:.9; }
.footer-links{ display:flex; gap:16px; flex-wrap:wrap; }
.footer-links a{ color:#fff; text-decoration:none; border-bottom:1px solid transparent; padding-bottom:1px; }
.footer-links a:hover{ opacity:.9; border-bottom-color:rgba(255,255,255,.6); }

@media (max-width:640px){
  .site-footer .footer-inner{ flex-direction:column; align-items:flex-start; }
}
</style>


