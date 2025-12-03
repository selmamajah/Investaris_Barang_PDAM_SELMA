<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\StrukController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PegawaiController;

// ------------------- AUTH ROUTES -------------------
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ------------------- FORGOT PASSWORD -------------------
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

// ------------------- PROFILE -------------------
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::get('/pegawai/{id}/divisi', [PegawaiController::class, 'getDivisi']);

// ------------------- PROTECTED ROUTES (AUTH REQUIRED) -------------------
Route::middleware('auth')->group(function () {

    // ------------------- REDIRECT HOME -------------------
    Route::get('/', fn() => redirect()->route('dashboard'));

    // ------------------- DASHBOARD -------------------
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ------------------- TRANSAKSI GABUNGAN -------------------
    Route::prefix('transaksi')->group(function () {
        Route::get('/create', [TransaksiController::class, 'create'])->name('transaksi.create');
        Route::post('/struk', [TransaksiController::class, 'storeStruk'])->name('transaksi.store.struk');
        Route::post('/pengeluaran', [TransaksiController::class, 'storePengeluaran'])->name('transaksi.store.pengeluaran');
    });

    // ------------------- STRUK (PEMASUKAN) -------------------
    Route::prefix('struks')->group(function () {
        Route::delete('/bulk-delete', [StrukController::class, 'bulkDelete'])->name('struks.bulk-delete');

        Route::get('/', [StrukController::class, 'index'])->name('struks.index');
        Route::get('/create', [StrukController::class, 'create'])->name('struks.create');
        Route::post('/', [StrukController::class, 'store'])->name('struks.store');
        Route::get('/{struk}', [StrukController::class, 'show'])->name('struks.show');
        Route::get('/{struk}/edit', [StrukController::class, 'edit'])->name('struks.edit');

        // ✅ cukup satu update route, gak dobel lagi
        Route::put('/{struk}', [StrukController::class, 'update'])->name('struks.update');

        Route::delete('/{struk}', [StrukController::class, 'destroy'])->name('struks.destroy');

        // Item routes
        Route::get('/{struk}/items', [StrukController::class, 'getItems'])->name('struks.items');
        Route::post('/{id}/item', [StrukController::class, 'addItem'])->name('struks.addItem');
        Route::put('/{struk}/item/{index}', [StrukController::class, 'updateItem'])->name('struks.updateItem');
        Route::put('/{struk}/update-items', [StrukController::class, 'updateItems'])->name('struks.updateItems');
        Route::delete('/{struk}/item/{index}', [StrukController::class, 'deleteItem'])->name('struks.deleteItem');

        // Search routes
        Route::get('/autocomplete-items', [StrukController::class, 'autocompleteItems'])->name('struks.autocomplete-items');
        Route::get('/search-barang', [StrukController::class, 'searchBarang'])->name('struks.search-barang');

        // Export routes
        Route::get('/export/excel', [StrukController::class, 'exportExcel'])->name('struks.export.excel');
        Route::get('/export/csv', [StrukController::class, 'exportCSV'])->name('struks.export.csv');
    });

    // Helpers / ajax / generate
    Route::get('/pengeluarans/generate-nomor-struk', [PengeluaranController::class, 'ajaxGenerateNomorStruk']);
    Route::post('/generate-spk', [PengeluaranController::class, 'generateNamaSpkString']);

    // ------------------- PENGELUARAN -------------------
    Route::prefix('pengeluarans')->group(function () {

        // ✅ cukup satu mass-delete, gak dobel lagi
        Route::delete('/mass-delete', [PengeluaranController::class, 'massDelete'])->name('pengeluarans.massDelete');

        Route::get('/', [PengeluaranController::class, 'index'])->name('pengeluarans.index');
        Route::post('/', [PengeluaranController::class, 'store'])->name('pengeluarans.store');
        Route::get('/{pengeluaran}', [PengeluaranController::class, 'show'])->name('pengeluarans.show');
        Route::get('/{pengeluaran}/edit', [PengeluaranController::class, 'edit'])->name('pengeluarans.edit');
        Route::put('/{pengeluaran}', [PengeluaranController::class, 'update'])->name('pengeluarans.update');

        // ✅ cukup satu destroy, gak dobel lagi
        Route::delete('/{pengeluaran}', [PengeluaranController::class, 'destroy'])->name('pengeluarans.destroy');

        // Route khusus untuk pengeluaran dari struk
        Route::get('/from-struk/{struk}', [PengeluaranController::class, 'createByStruk'])
            ->name('pengeluarans.create-by-struk');
        Route::post('/from-struk/{struk}', [PengeluaranController::class, 'storeByStruk'])
            ->name('pengeluarans.store-by-struk');

        // Export routes (lebih rapi kalau di dalam prefix)
        Route::get('/export/excel', [PengeluaranController::class, 'exportExcel'])->name('pengeluarans.export.excel');
        Route::get('/export/csv', [PengeluaranController::class, 'exportCsv'])->name('pengeluarans.export.csv');
    });

});
