<x-layouts.guest-layout>
  <style>
    .login-stage {
      padding: 30px 0
    }

    .login-stage .panel {
      max-width: 1120px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: 1fr .9fr;
      gap: 24px;
      align-items: stretch;
      padding: 0 18px
    }

    .stats-left {
      border-radius: 22px;
      padding: 34px 30px;
      background: rgba(255, 255, 255, .10);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, .12)
    }

    .stats-left h1 {
      margin: 0 0 8px;
      font-size: 40px;
      line-height: 1.05;
      color: #eaf2ff;
      letter-spacing: -.5px
    }

    .stats-left p {
      margin: 0 0 18px;
      color: rgba(234, 242, 255, .80);
      max-width: 52ch
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
      margin-top: 16px
    }

    .s-card {
      border-radius: 18px;
      padding: 14px 14px;
      background: rgba(255, 255, 255, .08);
      border: 1px solid rgba(255, 255, 255, .12)
    }

    .s-val {
      font-size: 22px;
      font-weight: 900;
      color: #eaf2ff
    }

    .s-lbl {
      margin-top: 6px;
      font-size: 12.5px;
      color: rgba(234, 242, 255, .75)
    }

    .stats-note {
      margin-top: 14px;
      font-size: 12.5px;
      color: rgba(234, 242, 255, .70)
    }

    .login-card {
      border-radius: 22px;
      overflow: hidden;
      background: #fff;
      box-shadow: 0 18px 60px rgba(0, 0, 0, .25)
    }

    .login-card .cardhead {
      padding: 20px 22px;
      border-bottom: 1px solid #eef2f7
    }

    .login-card .title {
      font-weight: 800;
      font-size: 26px;
      color: #0f172a
    }

    .login-card .sub {
      margin-top: 4px;
      color: #64748b
    }

    .login-card form {
      padding: 18px 22px 22px
    }

    .login-card .field {
      margin-bottom: 14px
    }

    .login-card label {
      display: block;
      font-size: 13px;
      color: #475569;
      margin-bottom: 6px
    }

    .login-card .input {
      width: 100%;
      padding: 12px 14px;
      border-radius: 14px;
      border: 1px solid #e5e7eb;
      outline: none
    }

    .login-card .input:focus {
      border-color: #86efac;
      box-shadow: 0 0 0 4px rgba(34, 197, 94, .12)
    }

    .login-card .row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: 6px 0 14px
    }

    .login-card .msg {
      background: #fee2e2;
      color: #991b1b;
      padding: 10px 12px;
      border-radius: 14px;
      margin: 14px 22px 0;
      border: 1px solid #fecaca
    }

    .login-card .btn {
      width: 100%;
      border: 0;
      border-radius: 16px;
      padding: 12px 14px;
      font-weight: 800;
      color: #fff;
      background: linear-gradient(180deg, #22c55e, #16a34a);
      box-shadow: 0 14px 30px rgba(34, 197, 94, .25);
      cursor: pointer;
    }

    .login-card .divider {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 0 22px;
      margin: 0 0 16px;
      color: #94a3b8;
      font-size: 12.5px;
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
      margin: 0 22px 22px;
      padding: 12px 14px;
      border-radius: 16px;
      border: 1px solid #e5e7eb;
      background: #fff;
      color: #0f172a;
      font-size: 16px;
      font-weight: 800;
      text-decoration: none;
      box-shadow: none;
    }

    .login-card .google-icon {
      width: 20px;
      height: 20px;
      display: block;
    }

    .login-card .helper {
      margin-top: 12px;
      font-size: 12.5px;
      color: #64748b;
      line-height: 1.45
    }

    @media (max-width: 900px) {
      .login-stage .panel {
        grid-template-columns: 1fr
      }

      .stats-grid {
        grid-template-columns: 1fr
      }

      .stats-left h1 {
        font-size: 36px
      }
    }

    [x-cloak] { display: none !important; }
  </style>

  <div class="login-stage">
    <div class="panel">
      <section class="stats-left">
        <h1>Ringkasan sistem.</h1>
        <p>Login untuk melihat detail ruangan dan mengajukan peminjaman.</p>

        <div class="stats-grid">
          @foreach ($stats as $s)
            <div class="s-card">
              <div class="s-val">{{ $s['value'] }}</div>
              <div class="s-lbl">{{ $s['label'] }}</div>
            </div>
          @endforeach
        </div>

        <div class="stats-note">
          Ringkasan ini menunjukkan gambaran cepat ruangan yang tersedia di sistem.
          <br><br>
          Setelah login, kamu bisa:
          <ul class="list-disc ml-5 mt-2 space-y-1">
            <li>Melihat detail ruangan (foto, fasilitas, kapasitas, lokasi).</li>
            <li>Mengajukan peminjaman sesuai tanggal dan jam yang dibutuhkan.</li>
            <li>Memantau status pengajuan langsung dari riwayat pengajuan.</li>
          </ul>
        </div>
      </section>

      <section class="login-card" x-data="{ view: '{{ $defaultTab ?? 'login' }}' }"
      @popstate.window="view = window.location.pathname.includes('register') ? 'register' : 'login'">
      <div x-show="view === 'login'" x-transition>
        <div class="cardhead">
          <div class="title">Form Login</div>
          <div class="sub">Silakan login untuk melanjutkan</div>
        </div>

        <!-- Session Status -->
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

          <div class="row">
            <div class="showpw" style="display:flex;gap:10px;align-items:center">
              <input id="showpw" type="checkbox">
              <label for="showpw" style="margin:0;color:#334155;font-size:13px">Show password</label>
            </div>
            <div class="remember-me" style="display:flex;gap:10px;align-items:center">
              <input id="remember_me" type="checkbox" name="remember">
              <label for="remember_me" style="margin:0;color:#334155;font-size:13px">Remember me</label>
            </div>
          </div>

          <button class="btn" type="submit">Masuk</button>

          <div class="helper" style="margin-bottom: 15px;">
            Gunakan akun yang sudah terdaftar. Jika gagal login, pastikan username/password benar. 
            Jika belum punya akun, maka 
            <a href="/register" style="color: #18a84d;" @click.prevent="view = 'register'; window.history.pushState({}, '', '/register')">
              daftar di sini
            </a>
          </div>
        </form>
      </div>
      <div x-show="view === 'register'" x-transition x-cloak>
        <div class="cardhead">
          <div class="title">Form Register</div>
          <div class="sub">Silakan register untuk membuat akun</div>
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
            <label for="username">Username</label>
            <input type="text" name="username" id="username" class="input" placeholder="Masukkan username" required autofocus>
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
          <div class="row">
            <div class="showpw" style="display:flex;gap:10px;align-items:center">
              <input id="showpwre" type="checkbox">
              <label for="showpwre" style="margin:0;color:#334155;font-size:13px">Show password</label>
            </div>
          </div>
          <button class="btn" type="submit">Daftar</button>
          <div class="helper" style="margin-bottom: 15px;">
            Daftarkan akun agar dapat melakukan peminjaman. Jika sudah punya, 
            <a href="/login" style="color: #18a84d;" @click.prevent="view = 'login'; window.history.pushState({}, '', '/login')">
              silakan masuk 
            </a>
            dengan akun anda.
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
    const pwconf = document.getElementById('pwconf')
    if (cb && pw) cb.addEventListener('change', () => { pw.type = cb.checked ? 'text' : 'password'; });
    if (cbre && pwre) cbre.addEventListener('change', () => { pwre.type = cbre.checked ? 'text' : 'password'; });
    if (cbre && pwconf) cbre.addEventListener('change', () => { pwconf.type = cbre.checked ? 'text' : 'password'; });
  </script>
</x-layouts.guest-layout>