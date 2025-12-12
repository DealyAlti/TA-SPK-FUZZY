<?php

use App\Http\Controllers\{
    DashboardController,
    KategoriController,
    ProdukController,
    UserController,
    DataTrainingController,
    HasilPrediksiController,
    PenjualanController
};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/**
 * Redirect setelah login berdasarkan level user
 * (Kalau tidak mau pakai level, bisa dihapus)
 */
Route::get('/redirect-after-login', function () {
    $user = Auth::user();

    if ($user->level == 0) {
        return redirect()->route('dashboard');
    } elseif ($user->level == 1) {
        return redirect()->route('dashboard'); // sementara semua ke dashboard
    } elseif ($user->level == 2) {
        return redirect()->route('dashboard');
    }

    return redirect('/');
})->middleware('auth');


/*
|--------------------------------------------------------------------------
| ROUTE SETELAH LOGIN
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // USER
    Route::get('/user/data', [UserController::class, 'data'])
        ->name('user.data');
    Route::resource('/user', UserController::class);

    // PROFIL USER
    Route::get('/profil', [UserController::class, 'profil'])
        ->name('user.profil');

    // PRODUK
    Route::get('/produk/data', [ProdukController::class, 'data'])
        ->name('produk.data');
    Route::resource('/produk', ProdukController::class);

    // KATEGORI
    Route::get('/kategori/data', [KategoriController::class, 'data'])
        ->name('kategori.data');
    Route::resource('/kategori', KategoriController::class);

    // DATA TRAINING
    Route::get('/data-training', [DataTrainingController::class, 'index'])
        ->name('training.index');
    Route::get('/data-training/{id_produk}/data', [DataTrainingController::class, 'data'])
        ->name('training.data');
    Route::post('/data-training', [DataTrainingController::class, 'store'])
        ->name('training.store');
    Route::get('/data-training/{id}', [DataTrainingController::class, 'show'])
        ->name('training.show');
    Route::put('/data-training/{id}', [DataTrainingController::class, 'update'])
        ->name('training.update');
    Route::delete('/data-training/{id}', [DataTrainingController::class, 'destroy'])
        ->name('training.destroy');
    Route::get('training/{id_produk}/export-template', [DataTrainingController::class, 'exportTemplate'])
    ->name('training.template');
    Route::post('training/import', [DataTrainingController::class, 'import'])->name('training.import');
    Route::post('/data-training/generate-harian', [DataTrainingController::class, 'generateHarian'])
    ->name('training.generateHarian');


    // PREDIKSI
    Route::get('/prediksi', [HasilPrediksiController::class, 'index'])->name('prediksi.index');
    Route::post('/prediksi/hitung', [HasilPrediksiController::class, 'hitung'])->name('prediksi.hitung');
    Route::get('/prediksi/hasil', [HasilPrediksiController::class, 'hasil'])->name('prediksi.hasil');
    Route::get('/prediksi/perhitungan', [HasilPrediksiController::class, 'detail'])->name('prediksi.detail');

    // ================== RIWAYAT PREDIKSI ==================
    Route::get('/prediksi/riwayat', [HasilPrediksiController::class, 'riwayat'])->name('prediksi.riwayat');
    // form input aktual (halaman sendiri)
    Route::get('/prediksi/riwayat/{id}/aktual', [HasilPrediksiController::class, 'formAktual'])->name('prediksi.riwayat.formAktual');
    // simpan aktual + otomatis tambah stok
    Route::post('/prediksi/riwayat/{id}/aktual', [HasilPrediksiController::class, 'updateAktual'])->name('prediksi.riwayat.aktual');

    Route::get('/penjualan/riwayat', [PenjualanController::class, 'riwayat'])
        ->name('penjualan.riwayat');

    Route::get('/penjualan', [PenjualanController::class, 'index'])
        ->name('penjualan.index');

    Route::post('/penjualan', [PenjualanController::class, 'store'])
        ->name('penjualan.store');
});

