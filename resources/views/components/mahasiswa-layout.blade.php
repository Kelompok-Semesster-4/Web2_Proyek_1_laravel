<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>{{ $title ?? 'Peminjaman Ruangan' }}</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v=5">
  <link rel="shortcut icon" href="{{ asset('assets/icons/favicon.ico') }}" type="image/x-icon">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('styles')
</head>

<body>

  <div class="topnav">
    <div class="topnavin">
      <a class="brand" href="{{ route('mahasiswa.dashboard') }}">
        <img class="logo" src="{{ asset('assets/icons/logo_big.svg') }}" alt="SIPERU">
      </a>

      <nav class="mainmenu" aria-label="Primary">
        <a class="{{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}" href="{{ route('mahasiswa.dashboard') }}">Home</a>
        <a class="{{ request()->routeIs('mahasiswa.ruangan*') ? 'active' : '' }}" href="{{ route('mahasiswa.ruangan') }}">Ruangan</a>
        <a class="{{ request()->routeIs('mahasiswa.peminjaman') ? 'active' : '' }}" href="{{ route('mahasiswa.peminjaman') }}">Peminjaman</a>
        
        @auth
            @if(Auth::user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
            @endif
            <form method="POST" action="{{ route('logout') }}" style="display:inline; padding:0; margin:0;" class="d-inline">
                @csrf
                <button type="submit" style="background:none; border:none; color:inherit; font:inherit; cursor:pointer; padding:0; outline:none;" class="nav-link">Logout</button>
            </form>
        @else
            <a class="{{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">Login</a>
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
        <a class="{{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}" href="{{ route('mahasiswa.dashboard') }}" role="menuitem">Home</a>
        <a class="{{ request()->routeIs('mahasiswa.ruangan*') ? 'active' : '' }}" href="{{ route('mahasiswa.ruangan') }}" role="menuitem">Ruangan</a>
        <a class="{{ request()->routeIs('mahasiswa.peminjaman') ? 'active' : '' }}" href="{{ route('mahasiswa.peminjaman') }}" role="menuitem">Peminjaman</a>
        @auth
            @if(Auth::user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" role="menuitem">Admin Dashboard</a>
            @endif
            <form method="POST" action="{{ route('logout') }}" style="display:inline; padding:0; margin:0;" class="d-inline w-100">
                @csrf
                <button type="submit" style="background:none; border:none; color:inherit; font:inherit; cursor:pointer; padding: 15px 20px; width: 100%; text-align: left;" class="nav-link">Logout</button>
            </form>
        @else
            <a class="{{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}" role="menuitem">Login</a>
        @endauth
      </div>
    </div>
  </div>

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

  @stack('scripts')
</body>
</html>
