<?php

use App\Http\Controllers\AlatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KalibrasiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\ActivityLogController;

// Redirect root ke dashboard atau login
Route::get('/', fn() => redirect()->route('dashboard'));

// Auth routes (Laravel Breeze / Jetstream / manual)
require __DIR__ . '/auth.php';

// Protected routes
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ======================
    // ALAT
    // ======================
    Route::resource('alat', AlatController::class);

    Route::prefix('alat-import')
        ->name('alat.import.')
        ->middleware('role:admin,petugas')
        ->group(function () {
            Route::get('/', [AlatController::class, 'importForm'])->name('form');
            Route::post('/preview', [AlatController::class, 'importPreview'])->name('preview');
            Route::post('/save', [AlatController::class, 'importSave'])->name('save');
            Route::get('/template', [AlatController::class, 'downloadTemplate'])->name('template');
        });

    // ======================
    // QR CODE (dipindah ke dalam auth)
    // ======================
    Route::prefix('alat')->group(function () {
        Route::get('{alat}/qrcode', [QrCodeController::class, 'show'])->name('qrcode.show');
        Route::get('{alat}/qrcode/download', [QrCodeController::class, 'download'])->name('qrcode.download');
        Route::post('qrcode/massal', [QrCodeController::class, 'cetakMassal'])->name('qrcode.massal');
    });

    // ======================
    // PEMINJAMAN — semua user authenticated
    // ======================
    Route::resource('peminjaman', PeminjamanController::class)
        ->except(['edit', 'update', 'destroy']);

    // PEMINJAMAN — khusus admin & petugas (tidak pakai prefix ulang)
    Route::middleware('role:admin,petugas')->group(function () {
        Route::patch('peminjaman/{peminjaman}/approve', [PeminjamanController::class, 'approve'])
            ->name('peminjaman.approve');
        Route::patch('peminjaman/{peminjaman}/reject',  [PeminjamanController::class, 'reject'])
            ->name('peminjaman.reject');
        Route::patch('peminjaman/{peminjaman}/kembali', [PeminjamanController::class, 'kembali'])
            ->name('peminjaman.kembali');
    });

    // ======================
    // KALIBRASI
    // ======================
    Route::resource('kalibrasi', KalibrasiController::class);

    // ======================
    // LAPORAN
    // ======================
    Route::prefix('laporan')
        ->name('laporan.')
        ->middleware('role:admin,petugas')
        ->group(function () {
            Route::get('peminjaman', [LaporanController::class, 'peminjaman'])->name('peminjaman');
            Route::get('alat', [LaporanController::class, 'alat'])->name('alat');
            Route::get('kalibrasi', [LaporanController::class, 'kalibrasi'])->name('kalibrasi');

            Route::get('peminjaman/pdf', [LaporanController::class, 'exportPdfPeminjaman'])->name('peminjaman.pdf');
            Route::get('alat/pdf', [LaporanController::class, 'exportPdfAlat'])->name('alat.pdf');
            Route::get('kalibrasi/pdf', [LaporanController::class, 'exportPdfKalibrasi'])->name('kalibrasi.pdf');

            Route::get('peminjaman/excel', [LaporanController::class, 'exportExcelPeminjaman'])->name('peminjaman.excel');
            Route::get('alat/excel', [LaporanController::class, 'exportExcelAlat'])->name('alat.excel');
            Route::get('kalibrasi/excel', [LaporanController::class, 'exportExcelKalibrasi'])->name('kalibrasi.excel');
        });

    // ======================
    // NOTIFIKASI (hapus duplikat)
    // ======================
    Route::prefix('notifikasi')->name('notifikasi.')->group(function () {
        Route::get('/', [NotifikasiController::class, 'index'])->name('index');
        Route::get('{notifikasi}/baca', [NotifikasiController::class, 'baca'])->name('baca');
        Route::post('baca-semua', [NotifikasiController::class, 'bacaSemua'])->name('baca-semua');
        Route::delete('{notifikasi}', [NotifikasiController::class, 'destroy'])->name('destroy');
        Route::delete('hapus-semua/all', [NotifikasiController::class, 'hapusSemua'])->name('hapus-semua');
    });

    // ======================
    // USER (ADMIN)
    // ======================
    Route::resource('users', UserController::class)->middleware('role:admin');

    // ======================
    // ACTIVITY LOG (ADMIN)
    // ======================
    Route::prefix('activity-log')
        ->name('activity-log.')
        ->middleware('role:admin')
        ->group(function () {
            Route::get('/', [ActivityLogController::class, 'index'])->name('index');
            Route::get('{activityLog}', [ActivityLogController::class, 'show'])->name('show');
            Route::delete('{activityLog}', [ActivityLogController::class, 'destroy'])->name('destroy');
            Route::delete('hapus/semua', [ActivityLogController::class, 'hapusSemua'])->name('hapus-semua');
        });

});