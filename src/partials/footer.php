</div><!-- /.container -->
</main>
<footer class="border-top py-4 mt-auto">
  <div class="container d-flex justify-content-between small text-secondary">
    <span>© <?=date('Y')?> Bookstore</span>
    <span>Demo build · PHP + PostgreSQL</span>
  </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Простая тема: auto / light / dark (локально)
  (function() {
    const key='theme';
    const btn=document.getElementById('themeToggle');
    const sun=document.getElementById('iconSun');
    const moon=document.getElementById('iconMoon');
    function apply(theme){
      document.documentElement.setAttribute('data-bs-theme', theme);
      if (theme==='dark'){ sun.classList.add('d-none'); moon.classList.remove('d-none'); }
      else { sun.classList.remove('d-none'); moon.classList.add('d-none'); }
    }
    const saved = localStorage.getItem(key) || 'auto';
    apply(saved);
    if (btn) btn.addEventListener('click', ()=>{
      const current = document.documentElement.getAttribute('data-bs-theme') || saved;
      const next = current==='dark' ? 'light' : 'dark';
      localStorage.setItem(key, next);
      apply(next);
    });
  })();
</script>
</body>
</html>
