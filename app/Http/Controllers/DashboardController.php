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
        if ((int) $user->level === 0) {

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

            // checklist
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

            // warnings
            $warnings = [];
            if (!$hasPrediksiToday) {
                $warnings[] = [
                    'title' => 'Saran produksi hari ini belum dibuat',
                    'text'  => 'Buat saran produksi untuk membantu menentukan jumlah produksi hari ini.',
                    'type'  => 'warn',
                    'url'   => route('prediksi.index'),
                ];
            }

            // stok terbaru (pakai DataTraining)
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
        // Hanya lihat prediksi/saran (tanpa hasil_aktual)
        // =========================
        if ((int) $user->level === 1) {

            // Prediksi hari ini (untuk tabel atas)
            $prediksiHariIni = HasilPrediksi::with('produk')
                ->whereDate('tanggal', $today)
                ->orderBy('id_hasil_prediksi', 'desc')
                ->get();

            // Prediksi terbaru (riwayat singkat di bawah)
            $latestPrediksi = HasilPrediksi::with('produk')
                ->orderBy('tanggal', 'desc')
                ->orderBy('id_hasil_prediksi', 'desc')
                ->limit(10)
                ->get();

            // KPI sederhana
            $totalPrediksiHariIni = $prediksiHariIni->count();
            $totalPrediksi        = HasilPrediksi::count();

            return view('dashboard.kepala_produksi', compact(
                'latestPrediksi',
                'prediksiHariIni',
                'totalPrediksiHariIni',
                'totalPrediksi'
            ));
        }

        // =========================
        // ADMIN (level 2)
        // Fokus: Training Harian
        // =========================
        if ((int) $user->level === 2) {

            // total record training di hari ini
            $totalTrainingHariIni = DataTraining::whereDate('tanggal', $today)->count();

            // berapa produk yang sudah punya training di hari ini
            $totalProdukTrainingHariIni = DataTraining::whereDate('tanggal', $today)
                ->distinct('id_produk')
                ->count('id_produk');

            // beberapa baris training terbaru (untuk tabel bawah)
            $latestTraining = DataTraining::with('produk')
                ->orderBy('tanggal', 'desc')
                ->orderBy('id_data_training', 'desc')
                ->limit(5)
                ->get();

            $warnings = [];
            if ($totalTrainingHariIni === 0) {
                $warnings[] = [
                    'title' => 'Data training hari ini belum ada',
                    'text'  => 'Isi atau import data training harian agar sistem memiliki data terbaru.',
                    'type'  => 'warn',
                    'url'   => route('training.harian.index'),
                ];
            }

            return view('dashboard.admin', compact(
                'totalTrainingHariIni',
                'totalProdukTrainingHariIni',
                'latestTraining',
                'warnings'
            ));
        }

        // fallback kalau level tidak dikenal
        return redirect()->route('dashboard');
    }
}
