<?php

use App\Http\Controllers\Admin\AbsensiController as AdminAbsensiController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\JurnalController as AdminJurnalController;
use App\Http\Controllers\Admin\PerusahaanController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\Pembimbing\DashboardController as PembimbingDashboardController;
use App\Http\Controllers\Pembimbing\AbsensiController as PembimbingAbsensiController;
use App\Http\Controllers\Pembimbing\JurnalController as PembimbingJurnalController;
use App\Http\Controllers\Pembimbing\SiswaController as PembimbingSiswaController;
use App\Http\Controllers\Siswa\AbsensiController as SiswaAbsensiController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\DokumentasiController;
use App\Http\Controllers\Siswa\JurnalController as SiswaJurnalController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


Route::get('/', function () {
    if (Auth::check()) {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    return redirect()->route('login');
});


Route::middleware('guest')->group(function (): void {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::post('logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {

        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Siswa
        Route::resource('siswa', SiswaController::class)->except(['show']);

        Route::prefix('siswa-import')->name('siswa.')->group(function () {
            Route::get('/', [SiswaController::class, 'import'])->name('import');
            Route::post('/', [SiswaController::class, 'processImport'])->name('processImport');
        });

        // Perusahaan
        Route::resource('perusahaan', PerusahaanController::class)->except(['show']);

        // Absensi
        Route::prefix('absensi')->name('absensi.')->group(function () {
            Route::get('/', [AdminAbsensiController::class, 'index'])->name('index');
            Route::get('/rekap', [AdminAbsensiController::class, 'rekap'])->name('rekap');
            Route::get('/rekap/download', [AdminAbsensiController::class, 'downloadRekap'])->name('rekap.download');
            Route::get('/export-pdf', [AdminAbsensiController::class, 'exportPdf'])->name('exportPdf');
        });

        // Jurnal
        Route::prefix('jurnal')->name('jurnal.')->group(function () {
            Route::get('/', [AdminJurnalController::class, 'index'])->name('index');
            Route::get('/export-pdf', [AdminJurnalController::class, 'exportPdf'])->name('exportPdf');
        });

        // Chart
        Route::get('grafik-kehadiran', [ChartController::class, 'admin'])->name('chart');
    });


Route::middleware(['auth', 'role:siswa', 'siswa.access.window'])
    ->prefix('siswa')
    ->name('siswa.')
    ->group(function (): void {

        Route::get('dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');

        // Absensi
        Route::prefix('absensi')->name('absensi.')->group(function () {
            Route::get('/', [SiswaAbsensiController::class, 'index'])->name('index');
            Route::post('/checkin', [SiswaAbsensiController::class, 'checkin'])->name('checkin');
            Route::post('/checkout', [SiswaAbsensiController::class, 'checkout'])->name('checkout');
            Route::post('/izin', [SiswaAbsensiController::class, 'izin'])->name('izin');
        });

        // Jurnal
        Route::prefix('jurnal')->name('jurnal.')->group(function () {
            Route::get('/', [SiswaJurnalController::class, 'index'])->name('index');
            Route::post('/', [SiswaJurnalController::class, 'store'])->name('store');
        });

        // Dokumentasi
        Route::prefix('dokumentasi')->name('dokumentasi.')->group(function () {
            Route::get('/', [DokumentasiController::class, 'index'])->name('index');
            Route::post('/', [DokumentasiController::class, 'store'])->name('store');
            Route::delete('/{dokumentasi}', [DokumentasiController::class, 'destroy'])->name('destroy');
        });

        // Chart
        Route::get('grafik-kehadiran', [ChartController::class, 'siswa'])->name('chart');
    });


Route::middleware(['auth', 'role:guru_pembimbing,pembimbing_perusahaan'])
    ->prefix('pembimbing')
    ->name('pembimbing.')
    ->group(function (): void {

        Route::get('dashboard', [PembimbingDashboardController::class, 'index'])->name('dashboard');

        // Absensi
        Route::prefix('absensi')->name('absensi.')->group(function () {
            Route::get('/', [PembimbingAbsensiController::class, 'index'])->name('index');
        });

        // Jurnal
        Route::prefix('jurnal')->name('jurnal.')->group(function () {
            Route::get('/', [PembimbingJurnalController::class, 'index'])->name('index');
        });

        Route::middleware('role:pembimbing_perusahaan')->group(function (): void {
            Route::get('siswa/create', [PembimbingSiswaController::class, 'create'])->name('siswa.create');
            Route::post('siswa', [PembimbingSiswaController::class, 'store'])->name('siswa.store');

            Route::get('absensi/download-pdf', [PembimbingAbsensiController::class, 'downloadPdf'])->name('absensi.downloadPdf');
            Route::post('absensi/{absensi}/approve', [PembimbingAbsensiController::class, 'approve'])->name('absensi.approve');
            Route::post('absensi/{absensi}/reject', [PembimbingAbsensiController::class, 'reject'])->name('absensi.reject');

            Route::get('jurnal/download-pdf', [PembimbingJurnalController::class, 'downloadPdf'])->name('jurnal.downloadPdf');
            Route::post('jurnal/{jurnal}/approve', [PembimbingJurnalController::class, 'approve'])->name('jurnal.approve');
            Route::post('jurnal/{jurnal}/reject', [PembimbingJurnalController::class, 'reject'])->name('jurnal.reject');
        });
    });

if (app()->environment('local')) {
    Route::middleware('auth')->get('/debug/auth', function () {
        $user = Auth::user();
        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
        ]);
    });
}

// Fallback untuk hosting yang belum memiliki symbolic link public/storage.
Route::get('/storage/{path}', function (string $path) {
    $normalizedPath = ltrim(str_replace('\\', '/', $path), '/');
    $normalizedPath = preg_replace('#^(public|storage)/#', '', $normalizedPath) ?? $normalizedPath;

    if (str_contains($normalizedPath, '..') || ! Storage::disk('public')->exists($normalizedPath)) {
        abort(404);
    }

    return response()->file(Storage::disk('public')->path($normalizedPath));
})->where('path', '.*')->name('storage.public');

// Endpoint media publik yang tidak bergantung pada symlink /public/storage.
Route::get('/media/{path}', function (string $path) {
    $normalizedPath = ltrim(str_replace('\\', '/', $path), '/');
    $normalizedPath = preg_replace('#^(public|storage|media)/#', '', $normalizedPath) ?? $normalizedPath;

    if (str_contains($normalizedPath, '..')) {
        abort(404);
    }

    if (Storage::disk('public')->exists($normalizedPath)) {
        return response()->file(Storage::disk('public')->path($normalizedPath));
    }

    $publicMediaPath = public_path('media/' . $normalizedPath);
    if (is_file($publicMediaPath)) {
        return response()->file($publicMediaPath);
    }

    abort(404);

})->where('path', '.*')->name('media.public');