<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // Jika sudah login, redirect sesuai role
        if (Auth::check()) {
            $role = Auth::user()->role;
            return redirect($role === 'admin' ? '/admin/dashboard' : '/dashboard');
        }

        return view('auth.login', ['defaultTab' => 'login']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        $user = User::where('username', $username)->first();

        if (!$user) {
            return back()->withErrors(['username' => 'Username atau password salah.'])->withInput();
        }

        $stored = $user->password;
        $ok = false;

        // Cek bcrypt
        if (preg_match('/^\$2[aby]\$/', $stored)) {
            $ok = Hash::check($password, $stored);
        }
        // Cek md5 (upgrade otomatis)
        else if (preg_match('/^[a-f0-9]{32}$/i', $stored)) {
            $ok = (md5($password) === strtolower($stored));
            if ($ok) {
                $user->password = Hash::make($password);
                $user->save();
            }
        }

        if (!$ok) {
            return back()->withErrors(['username' => 'Username atau password salah.'])->withInput();
        }

        // Login sukses
        Auth::login($user, $remember);
        $request->session()->regenerate();

        // Redirect sesuai role
        if ($user->role === 'admin') {
            return redirect()->intended('/admin/dashboard');
        }

        return redirect()->intended('/dashboard');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $googleId = $googleUser->getId();
            $email = $googleUser->getEmail();
            $nama = $googleUser->getName();

            // 1. Cek apakah akun dengan google_id ini sudah pernah login
            $user = User::where('google_id', $googleId)->first();

            // 2. Kalau belum ada, cek apakah email Google ini sudah ada di akun lama
            if (!$user && $email) {
                $user = User::where('email', $email)->first();
            }

            // 3. Kalau user sudah ada, hubungkan akun lama dengan Google
            if ($user) {
                $user->google_id = $googleId;

                if (!$user->email && $email) {
                    $user->email = $email;
                }

                if (!$user->nama && $nama) {
                    $user->nama = $nama;
                }

                $user->save();
            }

            // 4. Kalau user belum ada, buat akun baru otomatis
            else {
                $baseUsername = $email
                    ? Str::slug(explode('@', $email)[0], '')
                    : Str::slug($nama ?: 'user', '');

                if (!$baseUsername) {
                    $baseUsername = 'user';
                }

                $username = $baseUsername;
                $counter = 1;

                while (User::where('username', $username)->exists()) {
                    $username = $baseUsername . $counter;
                    $counter++;
                }

                $user = User::create([
                    'nama' => $nama ?: $username,
                    'username' => $username,
                    'email' => $email,
                    'google_id' => $googleId,
                    'password' => Hash::make(Str::random(32)),
                    'role' => 'mahasiswa',
                    'prodi' => null,
                ]);
            }

            // 5. Login user ke aplikasi
            Auth::login($user);
            $request->session()->regenerate();

            // 6. Redirect sesuai role
            if ($user->role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            }

            return redirect()->intended('/dashboard');

        } catch (\Throwable $e) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'google' => 'Login Google gagal: ' . $e->getMessage(),
                ]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function registerForm()
    {   if (Auth::check())
        {   $role = Auth::user()->role;
            return redirect($role === 'admin' ? '/admin/dashboard' : '/dashboard');
        }

        return view('auth.login', ['defaultTab' => 'register']);
    }

    public function register(Request $request)
    {   $validated_request = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'prodi' => ['required', 'string', 'max:100'],
        ]);

        $user = User::create([
            'nama' => $validated_request['username'],
            'username' => $validated_request['username'],
            'email' => $validated_request['email'],
            'password' => Hash::make($validated_request['password']),
            'role' => 'mahasiswa',
            'prodi' => $validated_request['prodi'],
        ]);

        Auth::login($user, false);
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }
}
