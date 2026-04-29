<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>{{ config('app.name', 'Peminjaman Ruangan') }}</title>

  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v=5">
  <link rel="shortcut icon" href="{{ asset('assets/icons/favicon.ico') }}" type="image/x-icon">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body>
  <title>{{ $title ?? config('app.name', 'Peminjaman Ruangan') }}</title>
  <div class="topnav">
    <div class="topnavin">
      <a class="brand" href="{{ url('/') }}">
        <img class="logo" src="{{ asset('assets/icons/logo_big.svg') }}" alt="NF">
      </a>

      <nav class="mainmenu" aria-label="Primary">
        <a href="{{ url('/') }}">Home</a>
        <a href="{{ url('/ruangan') }}">Ruangan</a>
        <a href="{{ url('/peminjaman') }}">Peminjaman</a>
        @auth
          <form method="POST" action="{{ route('logout') }}" style="display:inline;">
              @csrf
              <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">Logout</a>
          </form>
        @else
          <a class="active" href="{{ route('login') }}">Login</a>
        @endauth
      </nav>

      <!-- Hamburger (mobile) -->
      <button class="burger" id="burgerBtn" aria-label="Buka menu" aria-expanded="false" aria-controls="mobileMenu">
        <span class="lines" aria-hidden="true">
          <span class="line"></span>
          <span class="line"></span>
          <span class="line"></span>
        </span>
      </button>

      <!-- Mobile dropdown -->
      <div class="mobilePanel" id="mobileMenu" role="menu">
        <a href="{{ url('/') }}" role="menuitem">Home</a>
        <a href="{{ url('/ruangan') }}" role="menuitem">Ruangan</a>
        <a href="{{ url('/peminjaman') }}" role="menuitem">Peminjaman</a>
        @auth
            <form method="POST" action="{{ route('logout') }}" style="display:inline; width: 100%;">
                @csrf
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" role="menuitem">Logout</a>
            </form>
        @else
            <a class="active" href="{{ route('login') }}" role="menuitem">Login</a>
        @endauth
      </div>
    </div>
  </div>

  {{ $slot }}

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

    function closeMenu(){
      if (!mobileMenu || !burgerBtn) return;
      mobileMenu.classList.remove('show');
      burgerBtn.setAttribute('aria-expanded', 'false');
    }
    function toggleMenu(){
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

</body>
</html>
