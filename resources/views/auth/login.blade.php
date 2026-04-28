<x-layouts.guest-layout>
  <style>
    .login-stage {
      padding: 36px 22px 56px;
    }

    .login-stage .panel {
      max-width: 1100px;
      width: 100%;
      min-height: 78vh;
      margin: 0 auto;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 36px 22px;
      border-radius: 28px;
      background: rgba(255, 255, 255, .96);
      box-shadow: 0 22px 60px rgba(15, 23, 42, .18);
    }

    .login-card {
      width: 100%;
      max-width: 420px;
      background: transparent;
    }

    .login-card .cardhead {
      text-align: center;
      padding: 0 0 18px;
      background: transparent;
      border: 0;
      box-shadow: none;
    }

    .login-mark {
      width: 56px;
      height: 56px;
      margin: 0 auto 14px;
      display: grid;
      place-items: center;
      border-radius: 18px;
      color: #1f2937;
      background: linear-gradient(145deg, #f8fafc, #eef6f3);
      box-shadow: inset 0 0 0 1px rgba(148, 163, 184, .2);
    }

    .login-mark i {
      font-size: 1.55rem;
    }

    .login-card .title {
      font-weight: 800;
      font-size: 2rem;
      line-height: 1.12;
      color: #1f2937;
      letter-spacing: -.02em;
    }

    .login-card .sub {
      margin-top: 8px;
      color: #94a3b8;
      font-size: .96rem;
    }

    .login-card .msg {
      background: #fee2e2;
      color: #991b1b;
      padding: 10px 12px;
      border-radius: 14px;
      margin: 0 0 14px;
      border: 1px solid #fecaca;
      font-size: .92rem;
    }

    .login-card form {
      padding: 0;
    }

    .login-card .field {
      margin-bottom: 18px;
    }

    .login-card label {
      display: block;
      font-size: .85rem;
      font-weight: 600;
      color: #475569;
      margin-bottom: 7px;
    }

    .login-card .input {
      width: 100%;
      padding: 12px 14px;
      border-radius: 12px;
      border: 1px solid #e2e8f0;
      outline: none;
      background: #fff;
      color: #0f172a;
      transition: border-color .2s ease, box-shadow .2s ease;
    }

    .login-card .input::placeholder {
      color: #cbd5e1;
    }

    .login-card .input:focus {
      border-color: #22c55e;
      box-shadow: 0 0 0 4px rgba(34, 197, 94, .12);
    }

    .login-card .login-options-row {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 38px;
      width: 100%;
      margin: 10px 0 18px;
      flex-wrap: nowrap;
    }

    .login-card .checkline {
      display: flex;
      align-items: center;
      gap: 8px;
      color: #475569;
      font-size: .86rem;
    }

    .login-card .checkline label {
      margin: 0;
      font-weight: 500;
      color: #475569;
    }

    .login-card .btn {
      width: 100%;
      border: 0;
      border-radius: 12px;
      padding: 12px 14px;
      margin-top: 0;
      font-weight: 800;
      font-size: .96rem;
      color: #fff;
      background: linear-gradient(180deg, #22c55e, #16a34a);
      box-shadow: 0 14px 30px rgba(34, 197, 94, .22);
      cursor: pointer;
    }

    .login-card .switch-text {
      margin: 16px 0 0;
      text-align: center;
      font-size: .9rem;
      color: #64748b;
    }

    .login-card .switch-text a {
      color: #16a34a;
      font-weight: 700;
      text-decoration: none;
    }

    .login-card .divider {
      display: flex;
      align-items: center;
      gap: 12px;
      margin: 18px 0 16px;
      color: #94a3b8;
      font-size: .8rem;
      text-transform: uppercase;
      letter-spacing: .08em;
    }

    .login-card .divider::before,
    .login-card .divider::after {
      content: "";
      flex: 1;
      height: 1px;
      background: #e5e7eb;
    }

    .login-card .google-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      width: 100%;
      padding: 12px 14px;
      border-radius: 12px;
      border: 1px solid #e2e8f0;
      background: #fff;
      color: #0f172a;
      font-size: .95rem;
      font-weight: 700;
      text-decoration: none;
    }

    .login-card .google-icon {
      width: 20px;
      height: 20px;
      display: block;
    }

    @media (max-width: 768px) {
      .login-stage {
        padding: 24px 14px 40px;
      }

      .login-stage .panel {
        min-height: auto;
        padding: 26px 14px;
        border-radius: 22px;
      }

      .login-card .title {
        font-size: 1.7rem;
      }

      .login-card .login-options-row {
        flex-wrap: wrap;
        gap: 12px;
      }
    }

    [x-cloak] {
      display: none !important;
    }
  </style>

  <div class="login-stage">
    <div class="panel">
      <section class="login-card" x-data="{ view: '{{ $defaultTab ?? 'login' }}' }"
        @popstate.window="view = window.location.pathname.includes('register') ? 'register' : 'login'">

        <div x-show="view === 'login'" x-transition>
          <div class="cardhead">
            <div class="login-mark">
              <i class="bi bi-box-arrow-in-right"></i>
            </div>
            <div class="title">Log in to your account</div>
            <div class="sub">Enter your username and password below to log in</div>
          </div>

          <x-auth-session-status class="msg" :status="session('status')" />

          @if ($errors->any())
            <div class="msg">
              {{ $errors->first() }}
            </div>
          @endif

          <form action="{{ route('login') }}" method="post" autocomplete="off">
            @csrf

            <div class="field">
              <label for="username">Username</label>
              <input class="input" id="username" type="text" name="username" value="{{ old('username') }}"
                placeholder="Masukkan username" required autofocus>
            </div>

            <div class="field">
              <label for="pw">Password</label>
              <input class="input" id="pw" type="password" name="password" placeholder="Masukkan password" required>
            </div>

            <div class="login-options-row">
              <div class="checkline">
                <input id="showpw" type="checkbox">
                <label for="showpw">Show password</label>
              </div>
              <div class="checkline">
                <input id="remember_me" type="checkbox" name="remember">
                <label for="remember_me">Remember me</label>
              </div>
            </div>

            <button class="btn" type="submit">Masuk</button>

            <div class="switch-text">
              Belum punya akun?
              <a href="/register" @click.prevent="view = 'register'; window.history.pushState({}, '', '/register')">Daftar</a>
            </div>
          </form>
        </div>

        <div x-show="view === 'register'" x-transition x-cloak>
          <div class="cardhead">
            <div class="login-mark">
              <i class="bi bi-person-plus"></i>
            </div>
            <div class="title">Create your account</div>
            <div class="sub">Lengkapi data berikut untuk membuat akun baru</div>
          </div>

          <x-auth-session-status class="msg" :status="session('status')" />

          @if ($errors->any())
            <div class="msg">
              {{ $errors->first() }}
            </div>
          @endif

          <form action="{{ route('register') }}" method="post" autocomplete="off">
            @csrf

            <div class="field">
              <label for="username_register">Username</label>
              <input type="text" name="username" id="username_register" class="input" placeholder="Masukkan username" required autofocus>
            </div>

            <div class="field">
              <label for="email">Email</label>
              <input type="email" name="email" id="email" class="input" placeholder="Masukkan email" required>
            </div>

            <div class="field">
              <label for="prodi">Prodi</label>
              <input type="text" name="prodi" id="prodi" class="input" placeholder="Masukkan prodi" required>
            </div>

            <div class="field">
              <label for="pwre">Password</label>
              <input type="password" name="password" id="pwre" class="input" placeholder="Masukkan password" required>
            </div>

            <div class="field">
              <label for="pwconf">Konfirmasi Password</label>
              <input type="password" name="password_confirmation" id="pwconf" class="input" placeholder="Konfirmasi password" required>
            </div>

            <div class="login-options-row">
              <div class="checkline">
                <input id="showpwre" type="checkbox">
                <label for="showpwre">Show password</label>
              </div>
            </div>

            <button class="btn" type="submit">Daftar</button>

            <div class="switch-text">
              Sudah punya akun?
              <a href="/login" @click.prevent="view = 'login'; window.history.pushState({}, '', '/login')">Masuk</a>
            </div>
          </form>
        </div>

        <div class="divider">atau</div>

        <a href="{{ route('google.redirect') }}" class="google-btn">
          <svg class="google-icon" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path fill="#FFC107"
              d="M43.611 20.083H42V20H24v8h11.303C33.654 32.657 29.243 36 24 36c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.27 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z" />
            <path fill="#FF3D00"
              d="M6.306 14.691l6.571 4.819C14.655 16.108 19.002 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.27 4 24 4c-7.682 0-14.313 4.337-17.694 10.691z" />
            <path fill="#4CAF50"
              d="M24 44c5.166 0 9.86-1.977 13.409-5.197l-6.19-5.238C29.144 35.091 26.672 36 24 36c-5.222 0-9.619-3.329-11.283-7.946l-6.522 5.025C9.541 39.556 16.227 44 24 44z" />
            <path fill="#1976D2"
              d="M43.611 20.083H42V20H24v8h11.303c-.793 2.274-2.25 4.235-4.094 5.565.001-.001 6.19 5.238 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z" />
          </svg>
          <span>Masuk / Daftar dengan Google</span>
        </a>
      </section>
    </div>
  </div>

  <script>
    const cb = document.getElementById('showpw');
    const cbre = document.getElementById('showpwre');
    const pw = document.getElementById('pw');
    const pwre = document.getElementById('pwre');
    const pwconf = document.getElementById('pwconf');

    if (cb && pw) cb.addEventListener('change', () => { pw.type = cb.checked ? 'text' : 'password'; });
    if (cbre && pwre) cbre.addEventListener('change', () => { pwre.type = cbre.checked ? 'text' : 'password'; });
    if (cbre && pwconf) cbre.addEventListener('change', () => { pwconf.type = cbre.checked ? 'text' : 'password'; });
  </script>
</x-layouts.guest-layout>
