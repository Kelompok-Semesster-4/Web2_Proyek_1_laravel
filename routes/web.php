<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminRuanganController;
use App\Http\Controllers\AdminGedungController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminLaporanController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::controller(MahasiswaController::class)->group(function () {
    // Home boleh diakses siapa pun tanpa login
    Route::get('/', 'dashboard')->name('home');

    // Route lama tetap disediakan agar link lama tidak error
    Route::get('/mahasiswa/dashboard', 'dashboard')->name('mahasiswa.dashboard');
});

// Alias /ruangan agar tidak 404
Route::get('/ruangan', function () {
    return redirect()->route('mahasiswa.ruangan');
})->name('ruangan');

// Alias /peminjaman agar tidak 404
Route::get('/peminjaman', function () {
    return redirect()->route('mahasiswa.peminjaman');
})->name('peminjaman');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');

    // Account Registration
    Route::get('/register', 'registerForm')->name('register');
    Route::post('/register', 'register');
});

// Google Login
Route::prefix('auth/google')
    ->name('google.')
    ->controller(AuthController::class)
    ->group(function () {
        Route::get('/redirect', 'redirectToGoogle')->name('redirect');
        Route::get('/callback', 'handleGoogleCallback')->name('callback');
    });

/*
|--------------------------------------------------------------------------
| Redirect Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('home');
})->name('dashboard');

/*
|--------------------------------------------------------------------------
| Mahasiswa Routes
|--------------------------------------------------------------------------
| Wajib login.
| Admin boleh akses untuk preview halaman user.
*/

Route::middleware(['auth', 'role:mahasiswa,admin'])
    ->prefix('mahasiswa')
    ->name('mahasiswa.')
    ->controller(MahasiswaController::class)
    ->group(function () {
        Route::get('/ruangan', 'ruangan')->name('ruangan');
        Route::get('/ruangan/{ruangan}', 'detailRuangan')->name('ruangan.detail');

        Route::get('/peminjaman', 'peminjaman')->name('peminjaman');
        Route::post('/peminjaman/store', 'storePeminjaman')->name('peminjaman.store');
        Route::post('/peminjaman/cancel', 'cancelPeminjaman')->name('peminjaman.cancel');

        Route::get('/profil', 'profil')->name('profil');
        Route::patch('/profil', 'ubahProfil')->name('ubahProfil');
    });

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::controller(AdminController::class)->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard');
            Route::get('/persetujuan', 'persetujuan')->name('persetujuan');
            Route::post('/approve', 'processApproval')->name('approve.process');
        });

        Route::resource('ruangan', AdminRuanganController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        Route::resource('gedung', AdminGedungController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        Route::resource('user', AdminUserController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        Route::get('/laporan', [AdminLaporanController::class, 'index'])->name('laporan');
    });
