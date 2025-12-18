<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    DashboardController,
    KategoriController,
    ProdukController,
    UserController,
    DataTrainingController,
    HasilPrediksiController,
    PenjualanController
};

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/redirect-after-login', function () {
    return redirect()->route('dashboard');
})->middleware('auth');

Route::middleware('auth')->group(function () {

    // =========================
    // UMUM (SEMUA ROLE)
    // =========================
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profil', [UserController::class, 'profil'])->name('user.profil');

    // =========================
    // AKSES BERSAMA: OWNER + KEPALA PRODUKSI
    // (boleh lihat riwayat & input aktual)
    // =========================
    Route::middleware('level:0,1')->group(function () {

        // lihat hasil/riwayat/detail perhitungan
        Route::get('/prediksi/hasil', [HasilPrediksiController::class, 'hasil'])->name('prediksi.hasil');
        Route::get('/prediksi/riwayat', [HasilPrediksiController::class, 'riwayat'])->name('prediksi.riwayat');
        Route::get('/prediksi/perhitungan', [HasilPrediksiController::class, 'detail'])->name('prediksi.detail');
        Route::get('/prediksi/perhitungan/{id}', [HasilPrediksiController::class, 'detailById'])->name('prediksi.detailById');

        // input aktual (owner + kepala produksi)
        Route::get('/prediksi/riwayat/{id}/aktual', [HasilPrediksiController::class, 'formAktual'])
            ->name('prediksi.riwayat.formAktual');
        Route::post('/prediksi/riwayat/{id}/aktual', [HasilPrediksiController::class, 'updateAktual'])
            ->name('prediksi.riwayat.aktual');
    });

    // =========================
    // OWNER (LEVEL 0) - FULL
    // =========================
    Route::middleware('level:0')->group(function () {

        // USER
        Route::get('/user/data', [UserController::class, 'data'])->name('user.data');
        Route::resource('/user', UserController::class);

        // PRODUK
        Route::get('/produk/data', [ProdukController::class, 'data'])->name('produk.data');
        Route::resource('/produk', ProdukController::class);

        // KATEGORI
        Route::get('/kategori/data', [KategoriController::class, 'data'])->name('kategori.data');
        Route::resource('/kategori', KategoriController::class);

        // DATA TRAINING
        Route::get('/data-training', [DataTrainingController::class, 'index'])->name('training.index');
        Route::get('/data-training/{id_produk}/data', [DataTrainingController::class, 'data'])->name('training.data');
        Route::post('/data-training', [DataTrainingController::class, 'store'])->name('training.store');
        Route::get('/data-training/{id}', [DataTrainingController::class, 'show'])->name('training.show');
        Route::put('/data-training/{id}', [DataTrainingController::class, 'update'])->name('training.update');
        Route::delete('/data-training/{id}', [DataTrainingController::class, 'destroy'])->name('training.destroy');

        Route::get('training/{id_produk}/export-template', [DataTrainingController::class, 'exportTemplate'])
            ->name('training.template');
        Route::post('training/import', [DataTrainingController::class, 'import'])
            ->name('training.import');
        Route::post('/data-training/generate-harian', [DataTrainingController::class, 'generateHarian'])
            ->name('training.generateHarian');

        // PREDIKSI (owner boleh hitung)
        Route::get('/prediksi', [HasilPrediksiController::class, 'index'])->name('prediksi.index');
        Route::post('/prediksi/hitung', [HasilPrediksiController::class, 'hitung'])->name('prediksi.hitung');
    });

    // =========================
    // ADMIN (LEVEL 2) - PENJUALAN
    // =========================
    Route::middleware('level:2')->group(function () {

        Route::get('/penjualan/riwayat', [PenjualanController::class, 'riwayat'])->name('penjualan.riwayat');
        Route::get('/penjualan/detail/{tanggal}', [PenjualanController::class, 'detail'])->name('penjualan.detail');
        Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
        Route::post('/penjualan', [PenjualanController::class, 'store'])->name('penjualan.store');
    });

});
