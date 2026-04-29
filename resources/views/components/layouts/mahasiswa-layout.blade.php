<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>{{ $title ?? 'Peminjaman Ruangan' }}</title>

  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v=8">
  <link rel="shortcut icon" href="{{ asset('assets/icons/favicon.ico') }}" type="image/x-icon">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('styles')
</head>

<body>
  @include('components.navbar')
  <main>
    {{ $slot }}
  </main>

  <footer class="footer">
    <div class="footerin">
      <div>© {{ date('Y') }} Peminjaman Ruangan</div>
      <div class="contact">
        <div class="pill">☎ <span>+62 857-6941-0695</span></div>
        <div class="pill">✉ <span>info@unsri.ac.id</span></div>
      </div>
    </div>
  </footer>

  <script>
    const burgerBtn = document.getElementById('burgerBtn');
    const mobileMenu = document.getElementById('mobileMenu');

    function closeMenu() {
      if (!mobileMenu || !burgerBtn) return;
      mobileMenu.classList.remove('show');
      burgerBtn.setAttribute('aria-expanded', 'false');
    }
    function toggleMenu() {
      if (!mobileMenu || !burgerBtn) return;
      const isOpen = mobileMenu.classList.toggle('show');
      burgerBtn.setAttribute('aria-expanded', String(isOpen));
    }

    if (burgerBtn) {
      burgerBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleMenu();
      });

      document.addEventListener('click', () => closeMenu());
      document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeMenu(); });
      if (mobileMenu) mobileMenu.addEventListener('click', (e) => e.stopPropagation());
    }
  </script>

  @stack('scripts')
</body>

</html>