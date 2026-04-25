<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    // Traffic Director Dashboard
    Route::get('/dashboard', function () {
        if (\Illuminate\Support\Facades\Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('mahasiswa.dashboard');
    })->name('dashboard');

    // Rute Admin
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/persetujuan', [\App\Http\Controllers\AdminController::class, 'persetujuan'])->name('admin.persetujuan');
        Route::get('/laporan', [\App\Http\Controllers\AdminLaporanController::class, 'index'])->name('admin.laporan');
        Route::post('/approve', [\App\Http\Controllers\AdminController::class, 'processApproval'])->name('admin.approve.process');
        
        // Kelola Gedung
        Route::get('/gedung', [\App\Http\Controllers\AdminGedungController::class, 'index'])->name('admin.gedung.index');
        Route::post('/gedung', [\App\Http\Controllers\AdminGedungController::class, 'store'])->name('admin.gedung.store');
        Route::put('/gedung', [\App\Http\Controllers\AdminGedungController::class, 'update'])->name('admin.gedung.update');
        Route::delete('/gedung/{id}', [\App\Http\Controllers\AdminGedungController::class, 'destroy'])->name('admin.gedung.destroy');
        
        // Kelola Lantai
        Route::get('/lantai', [\App\Http\Controllers\AdminLantaiController::class, 'index'])->name('admin.lantai.index');
        Route::post('/lantai', [\App\Http\Controllers\AdminLantaiController::class, 'store'])->name('admin.lantai.store');
        Route::put('/lantai', [\App\Http\Controllers\AdminLantaiController::class, 'update'])->name('admin.lantai.update');
        Route::delete('/lantai/{id}', [\App\Http\Controllers\AdminLantaiController::class, 'destroy'])->name('admin.lantai.destroy');
        
        // Kelola Ruangan
        Route::get('/ruangan', [\App\Http\Controllers\AdminRuanganController::class, 'index'])->name('admin.ruangan.index');
        Route::post('/ruangan', [\App\Http\Controllers\AdminRuanganController::class, 'store'])->name('admin.ruangan.store');
        Route::put('/ruangan', [\App\Http\Controllers\AdminRuanganController::class, 'update'])->name('admin.ruangan.update');
        Route::delete('/ruangan/{id}', [\App\Http\Controllers\AdminRuanganController::class, 'destroy'])->name('admin.ruangan.destroy');
        
        // Kelola User
        Route::get('/user', [\App\Http\Controllers\AdminUserController::class, 'index'])->name('admin.user.index');
        Route::post('/user', [\App\Http\Controllers\AdminUserController::class, 'store'])->name('admin.user.store');
        Route::put('/user', [\App\Http\Controllers\AdminUserController::class, 'update'])->name('admin.user.update');
        Route::delete('/user/{id}', [\App\Http\Controllers\AdminUserController::class, 'destroy'])->name('admin.user.destroy');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rute Mahasiswa
Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\MahasiswaController::class, 'dashboard'])->name('dashboard');
    Route::get('/ruangan', [App\Http\Controllers\MahasiswaController::class, 'ruangan'])->name('ruangan');
    Route::get('/ruangan/{id}', [App\Http\Controllers\MahasiswaController::class, 'detailRuangan'])->name('ruangan.detail');
    
    Route::get('/peminjaman', [App\Http\Controllers\MahasiswaController::class, 'peminjaman'])->name('peminjaman');
    Route::post('/peminjaman/store', [App\Http\Controllers\MahasiswaController::class, 'storePeminjaman'])->name('peminjaman.store');
    Route::post('/peminjaman/cancel', [App\Http\Controllers\MahasiswaController::class, 'cancelPeminjaman'])->name('peminjaman.cancel');
});

// Route to dashboard for guests
Route::get('/dashboard', [App\Http\Controllers\MahasiswaController::class, 'dashboard'])->name('dashboard');

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/test-url-generation', function () {
    return response()->json([
        'ruangan_detail' => route('mahasiswa.ruangan.detail', 1),
        'dashboard' => route('mahasiswa.dashboard'),
        'peminjaman' => route('mahasiswa.peminjaman')
    ]);
});
