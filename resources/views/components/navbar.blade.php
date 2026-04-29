 <div class="topnav">
    @php
        $currentUser = Auth::user();
        $profileName = $currentUser ? ($currentUser->nama ?: $currentUser->username ?: 'User') : 'User';
        $profileInitial = strtoupper(substr($profileName, 0, 1));
    @endphp
    <div class="topnavin">
      <a class="brand" href="{{ route('mahasiswa.dashboard') }}">
        <img class="logo" src="{{ asset('assets/icons/logo_big.svg') }}" alt="SIPERU">
      </a>

      <nav class="mainmenu" aria-label="Primary">
        {{-- <a class="{{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}" href="{{ route('mahasiswa.dashboard') }}">Home</a> --}}
        <a class="{{ request()->routeIs('home', 'mahasiswa.dashboard') ? 'active' : '' }}" 
        href="{{ route('home') }}">Home</a>
        <a class="{{ request()->routeIs('mahasiswa.ruangan*') ? 'active' : '' }}" href="{{ route('mahasiswa.ruangan') }}">Ruangan</a>
        <a class="{{ request()->routeIs('mahasiswa.peminjaman') ? 'active' : '' }}" href="{{ route('mahasiswa.peminjaman') }}">Peminjaman</a>
        
        @auth
            @if(Auth::user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
            @endif
            <div class="profile-menu">
              <button class="profile-avatar {{ request()->routeIs('mahasiswa.profil') ? 'active' : '' }}" type="button" aria-label="Buka menu profil" aria-expanded="false">
                <span>{{ $profileInitial }}</span>
              </button>
              <div class="profile-dropdown">
                <div class="profile-summary">
                  <div class="profile-summary-avatar">{{ $profileInitial }}</div>
                  <div>
                    <strong>{{ $profileName }}</strong>
                    <small>{{ $currentUser->role ?? '' }}</small>
                  </div>
                </div>
                @if (Auth::user()->role === 'mahasiswa')
                  <a class="{{ request()->routeIs('mahasiswa.profil') ? 'active' : '' }}" href="{{ route('mahasiswa.profil') }}">
                    <i class="bi bi-person-circle"></i>
                    <span>Profil Pengguna</span>
                  </a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="profile-logout-form">
                    @csrf
                    <button type="submit">
                      <i class="bi bi-box-arrow-right"></i>
                      <span>Logout</span>
                    </button>
                </form>
              </div>
            </div>
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
            @if (Auth::user()->role === 'mahasiswa')
              <a class="mobile-profile-link {{ request()->routeIs('mahasiswa.profil') ? 'active' : '' }}" href="{{ route('mahasiswa.profil') }}" role="menuitem">
                <span class="mobile-profile-avatar">{{ $profileInitial }}</span>
                <span>Profil Pengguna</span>
              </a>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="mobile-logout-form">
                @csrf
                <button type="submit" class="mobile-logout-btn" role="menuitem">
                  <span class="mobile-profile-avatar danger"><i class="bi bi-box-arrow-right"></i></span>
                  <span>Logout</span>
                </button>
            </form>
        @else
            <a class="{{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}" role="menuitem">Login</a>
        @endauth
      </div>
    </div>
  </div>
