 <div class="topnav">
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
            @if (Auth::user()->role === 'mahasiswa')
              <a class="{{ request()->routeIs('mahasiswa.profil') ? 'active' : '' }}" href="{{ route('mahasiswa.profil') }}">Profil Pengguna</a>
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