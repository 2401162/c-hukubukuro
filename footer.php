<footer class="site-footer">
  <div class="footer-inner">
    <div class="footer-brand">
      <span class="footer-title">福袋販売サイト</span>
      <small class="copyright">&copy; <?= date('Y') ?> Fukubukuro Store</small>
    </div>
   
  </div>
</footer>

<style>
html, body {
  height: 100%;
  margin: 0;
  padding: 0;
}
body {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}
.site-footer{
  background:#000; 
  color:#fff;
  margin: 0;
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
main {
  flex: 1;
}
</style>


