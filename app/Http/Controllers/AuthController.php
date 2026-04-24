<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

        $stats = DB::selectOne("
            SELECT
                COUNT(r.id)                         AS total_ruangan,
                COUNT(DISTINCT g.id)                AS total_gedung,
                COALESCE(SUM(r.kapasitas), 0)       AS total_kapasitas
            FROM ruangan r
            LEFT JOIN lantai l ON l.id = r.lantai_id
            LEFT JOIN gedung g ON g.id = l.gedung_id
        ");

        $totalRuangan = number_format((int) ($stats->total_ruangan ?? 0), 0, ',', '.');
        $totalGedung = number_format((int) ($stats->total_gedung ?? 0), 0, ',', '.');
        $totalKapas = number_format((int) ($stats->total_kapasitas ?? 0), 0, ',', '.') . ' org';

        $statsArray = [
            ['label' => 'Total Ruangan', 'value' => $totalRuangan],
            ['label' => 'Gedung', 'value' => $totalGedung],
            ['label' => 'Kapasitas', 'value' => $totalKapas],
        ];

        return view('auth.login', ['stats' => $statsArray]);
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

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
