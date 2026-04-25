<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminRuanganController;
use App\Http\Controllers\AdminGedungController;
use App\Http\Controllers\AdminLantaiController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminLaporanController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Home boleh diakses siapa pun tanpa login
Route::get('/', [MahasiswaController::class, 'dashboard'])->name('mahasiswa.dashboard');

// Route lama tetap disediakan agar link lama tidak error
Route::get('/mahasiswa/dashboard', [MahasiswaController::class, 'dashboard'])
    ->name('mahasiswa.dashboard');

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

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Google Login
Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])
    ->name('google.redirect');

Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])
    ->name('google.callback');

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
    ->group(function () {
        Route::get('/ruangan', [MahasiswaController::class, 'ruangan'])->name('ruangan');
        Route::get('/ruangan/{id}', [MahasiswaController::class, 'detailRuangan'])->name('ruangan.detail');

        Route::get('/peminjaman', [MahasiswaController::class, 'peminjaman'])->name('peminjaman');
        Route::post('/peminjaman/store', [MahasiswaController::class, 'storePeminjaman'])->name('peminjaman.store');
        Route::post('/peminjaman/cancel', [MahasiswaController::class, 'cancelPeminjaman'])->name('peminjaman.cancel');
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
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        Route::get('/persetujuan', [AdminController::class, 'persetujuan'])->name('persetujuan');
        Route::post('/approve', [AdminController::class, 'processApproval'])->name('approve.process');

        Route::get('/ruangan', [AdminRuanganController::class, 'index'])->name('ruangan.index');
        Route::post('/ruangan', [AdminRuanganController::class, 'store'])->name('ruangan.store');
        Route::put('/ruangan', [AdminRuanganController::class, 'update'])->name('ruangan.update');
        Route::delete('/ruangan/{id}', [AdminRuanganController::class, 'destroy'])->name('ruangan.destroy');

        Route::get('/gedung', [AdminGedungController::class, 'index'])->name('gedung.index');
        Route::post('/gedung', [AdminGedungController::class, 'store'])->name('gedung.store');
        Route::put('/gedung', [AdminGedungController::class, 'update'])->name('gedung.update');
        Route::delete('/gedung/{id}', [AdminGedungController::class, 'destroy'])->name('gedung.destroy');

        Route::get('/user', [AdminUserController::class, 'index'])->name('user.index');
        Route::post('/user', [AdminUserController::class, 'store'])->name('user.store');
        Route::put('/user', [AdminUserController::class, 'update'])->name('user.update');
        Route::delete('/user/{id}', [AdminUserController::class, 'destroy'])->name('user.destroy');

        Route::get('/laporan', [AdminLaporanController::class, 'index'])->name('laporan');
    });