<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use App\Models\DataTraining;
use App\Models\HasilPrediksi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user  = auth()->user();
        $today = Carbon::today()->toDateString();

        // =========================
        // OWNER (level 0)
        // =========================
        if ((int)$user->level === 0) {

            $totalProduk   = Produk::count();
            $totalKategori = Kategori::count();
            $totalTraining = DataTraining::count();

            // prediksi terbaru
            $latestPrediksi = HasilPrediksi::with('produk')
                ->orderBy('tanggal', 'desc')
                ->orderBy('id_hasil_prediksi', 'desc')
                ->limit(6)
                ->get();

            // status hari ini: apakah sudah ada prediksi/saran
            $hasPrediksiToday = HasilPrediksi::whereDate('tanggal', $today)->exists();

            // checklist (tanpa aktual & tanpa stok harian)
            $checklist = [
                [
                    'label' => 'Buat saran produksi hari ini',
                    'done'  => $hasPrediksiToday,
                    'hint'  => $hasPrediksiToday ? 'Saran produksi tersedia' : 'Belum ada saran hari ini',
                    'url'   => route('prediksi.index'),
                ],
                [
                    'label' => 'Pastikan data training terbaru sudah lengkap',
                    'done'  => ($totalTraining > 0),
                    'hint'  => ($totalTraining > 0) ? 'Data training tersedia' : 'Belum ada data training',
                    'url'   => route('training.index'),
                ],
            ];

            // warnings (tanpa aktual)
            $warnings = [];
            if (!$hasPrediksiToday) {
                $warnings[] = [
                    'title' => 'Saran produksi hari ini belum dibuat',
                    'text'  => 'Buat saran produksi untuk membantu menentukan jumlah produksi hari ini.',
                    'type'  => 'warn',
                    'url'   => route('prediksi.index'),
                ];
            }

            // stok terbaru (pakai DataTraining, bukan stok_harian)
            // Ambil 10 baris terakhir data training (untuk tampil di tabel kanan)
            $latestStokTraining = DataTraining::with('produk')
                ->orderBy('tanggal', 'desc')
                ->orderBy('id_data_training', 'desc')
                ->limit(10)
                ->get();

            return view('dashboard.index', compact(
                'totalProduk',
                'totalKategori',
                'totalTraining',
                'latestPrediksi',
                'checklist',
                'warnings',
                'latestStokTraining'
            ));
        }

        // =========================
        // KEPALA PRODUKSI (level 1)
        // (tanpa aktual)
        // =========================
        if ((int)$user->level === 1) {

            $latestPrediksi = HasilPrediksi::with('produk')
                ->orderBy('tanggal', 'desc')
                ->orderBy('id_hasil_prediksi', 'desc')
                ->limit(10)
                ->get();

            $prediksiHariIni = HasilPrediksi::with('produk')
                ->whereDate('tanggal', $today)
                ->orderBy('id_hasil_prediksi', 'desc')
                ->get();

            return view('dashboard.kepala_produksi', compact(
                'latestPrediksi',
                'prediksiHariIni'
            ));
        }

        // =========================
        // ADMIN (level 2)
        // (tanpa stok_harian & tanpa aktual)
        // =========================
        if ((int)$user->level === 2) {

            $totalProduk   = Produk::count();
            $totalTraining = DataTraining::count();

            $latestPrediksi = HasilPrediksi::with('produk')
                ->orderBy('tanggal', 'desc')
                ->orderBy('id_hasil_prediksi', 'desc')
                ->limit(10)
                ->get();

            $latestStokTraining = DataTraining::with('produk')
                ->orderBy('tanggal', 'desc')
                ->orderBy('id_data_training', 'desc')
                ->limit(10)
                ->get();

            $warnings = [];
            if ($totalTraining === 0) {
                $warnings[] = [
                    'title' => 'Data training masih kosong',
                    'text'  => 'Import / isi data training agar sistem dapat memberi saran produksi yang lebih baik.',
                    'type'  => 'warn',
                    'url'   => route('training.index'),
                ];
            }

            return view('dashboard.admin', compact(
                'totalProduk',
                'totalTraining',
                'latestPrediksi',
                'latestStokTraining',
                'warnings'
            ));
        }

        return redirect()->route('dashboard');
    }
}
