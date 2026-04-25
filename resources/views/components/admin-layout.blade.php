<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>{{ config('app.name', 'Admin') }}</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v=8">
  <link rel="shortcut icon" href="{{ asset('assets/icons/favicon.ico') }}" type="image/x-icon">
  @stack('styles')
</head>

<body class="admin-body">

  <aside class="asb" id="adminSidebar" aria-label="Admin sidebar">
    <div class="asb-head">
      <a class="asb-brand" href="{{ route('admin.dashboard') }}">
        <img class="asb-logo" src="{{ asset('assets/icons/admin_logo.svg') }}" alt="NF">
      </a>

      <button class="asb-burger" id="asbBurger" aria-label="Toggle sidebar" aria-expanded="false">
        <span class="asb-lines" aria-hidden="true">
          <span class="asb-line"></span>
          <span class="asb-line"></span>
          <span class="asb-line"></span>
        </span>
      </button>
    </div>

    <nav class="asb-nav">
      <a class="asb-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
        href="{{ route('admin.dashboard') }}">
        <span class="dot"></span> Dashboard
      </a>



      <a class="asb-link {{ request()->routeIs('admin.ruangan.*') ? 'active' : '' }}"
        href="{{ route('admin.ruangan.index') }}">
        <span class="dot"></span> Kelola Ruangan
      </a>

      <a class="asb-link {{ request()->routeIs('admin.gedung.*') ? 'active' : '' }}"
        href="{{ route('admin.gedung.index') }}">
        <span class="dot"></span> Kelola Gedung
      </a>

      <a class="asb-link {{ request()->routeIs('admin.persetujuan') ? 'active' : '' }}"
        href="{{ route('admin.persetujuan') }}">
        <span class="dot"></span> Approve Peminjaman
      </a>

      <a class="asb-link {{ request()->routeIs('admin.user.*') ? 'active' : '' }}"
        href="{{ route('admin.user.index') }}">
        <span class="dot"></span> Kelola User
      </a>

      <a class="asb-link {{ request()->routeIs('admin.laporan') ? 'active' : '' }}" href="{{ route('admin.laporan') }}">
        <span class="dot"></span> Laporan
      </a>
    </nav>

    <div class="asb-foot asb-actions">
      <a class="btn btn-success btn-sm asb-action-btn" href="{{ route('home') }}">
        <i class="bi bi-eye me-2"></i> Preview Mahasiswa
      </a>
      <form method="POST" action="{{ route('logout') }}" class="asb-action-form">
        @csrf
        <button type="submit" class="btn btn-success btn-sm asb-action-btn">
          <i class="bi bi-box-arrow-right me-2"></i> Logout
        </button>
      </form>
    </div>
  </aside>

  <div class="asb-overlay" id="asbOverlay" aria-hidden="true"></div>
  <button class="asb-fab" id="asbMobileToggle" aria-label="Buka menu admin" aria-expanded="false">
    <span class="asb-lines" aria-hidden="true">
      <span class="asb-line"></span>
      <span class="asb-line"></span>
      <span class="asb-line"></span>
    </span>
  </button>

  <script>
    (function () {
      const sidebar = document.getElementById('adminSidebar');
      const overlay = document.getElementById('asbOverlay');
      const sidebarBtn = document.getElementById('asbBurger');
      const mobileBtn = document.getElementById('asbMobileToggle');

      if (!sidebar) return;

      const syncState = (open) => {
        sidebar.classList.toggle('open', open);
        document.body.classList.toggle('asb-open', open);
        if (overlay) overlay.classList.toggle('show', open);
        if (sidebarBtn) sidebarBtn.setAttribute('aria-expanded', String(open));
        if (mobileBtn) mobileBtn.setAttribute('aria-expanded', String(open));
        if (sidebarBtn) sidebarBtn.classList.toggle('is-open', open);
        if (mobileBtn) mobileBtn.classList.toggle('is-open', open);
      };

      const toggle = () => syncState(!sidebar.classList.contains('open'));
      const close = () => syncState(false);

      if (sidebarBtn) sidebarBtn.addEventListener('click', toggle);
      if (mobileBtn) mobileBtn.addEventListener('click', toggle);
      if (overlay) overlay.addEventListener('click', close);

      sidebar.querySelectorAll('.asb-link, .asb-logout').forEach((el) => {
        el.addEventListener('click', () => {
          if (window.matchMedia('(max-width: 992px)').matches) close();
        });
      });

      window.addEventListener('resize', () => {
        if (window.innerWidth > 992) syncState(false);
      });
    })();
  </script>

  <div class="wrap admin-wrap">
    <main class="admin-main">
      {{ $slot }}
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>

</html>
