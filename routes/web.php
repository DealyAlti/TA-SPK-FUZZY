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
    TrainingHarianController,
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
    // TRAINING HARIAN
    // OWNER (0) + ADMIN (2)
    // =========================
    Route::middleware('level:0,2')->group(function () {
        Route::get('/training-harian', [TrainingHarianController::class, 'index'])
            ->name('training.harian.index');

        Route::get('/training-harian/template', [TrainingHarianController::class, 'exportTemplate'])
            ->name('training.harian.template');

        Route::post('/training-harian/import', [TrainingHarianController::class, 'import'])
            ->name('training.harian.import');

        Route::post('/training-harian/generate', [TrainingHarianController::class, 'generate'])
            ->name('training.harian.generate');
    });

    // =========================
    // AKSES BERSAMA: OWNER + KEPALA PRODUKSI
    // (boleh lihat hasil/riwayat perhitungan)
    // =========================
    Route::middleware('level:0,1')->group(function () {

        // lihat hasil/riwayat/detail perhitungan
        Route::get('/prediksi/hasil', [HasilPrediksiController::class, 'hasil'])->name('prediksi.hasil');
        Route::get('/prediksi/riwayat', [HasilPrediksiController::class, 'riwayat'])->name('prediksi.riwayat');
        Route::get('/prediksi/perhitungan', [HasilPrediksiController::class, 'detail'])->name('prediksi.detail');
        Route::get('/prediksi/perhitungan/{id}', [HasilPrediksiController::class, 'detailById'])->name('prediksi.detailById');

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

        // DATA TRAINING (tanpa tambah manual & tanpa generate)
        Route::get('/data-training', [DataTrainingController::class, 'index'])->name('training.index');
        Route::get('/data-training/{id_produk}/data', [DataTrainingController::class, 'data'])->name('training.data');

        // aksi yang masih dipakai UI
        Route::delete('/data-training/{id}', [DataTrainingController::class, 'destroy'])->name('training.destroy');

        Route::get('/training/{id_produk}/export-template', [DataTrainingController::class, 'exportTemplate'])
            ->name('training.template');

        Route::post('/training/import', [DataTrainingController::class, 'import'])
            ->name('training.import');

        // PREDIKSI (owner boleh hitung)
        Route::get('/prediksi', [HasilPrediksiController::class, 'index'])->name('prediksi.index');
        Route::post('/prediksi/hitung', [HasilPrediksiController::class, 'hitung'])->name('prediksi.hitung');
    });

});
