<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use App\Models\DataTraining;
use App\Models\Penjualan;
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
        // OWNER (level 0) -> dashboard/index.blade.php (punya kamu)
        // =========================
        if ((int)$user->level === 0) {

            $totalProduk     = Produk::count();
            $totalKategori   = Kategori::count();
            $totalTraining   = DataTraining::count();
            $verifikasiCount = HasilPrediksi::whereNotNull('hasil_aktual')->count();

            $latestPrediksi = HasilPrediksi::with('produk')
                ->orderBy('tanggal', 'desc')
                ->orderBy('id_hasil_prediksi', 'desc')
                ->limit(6)->get();

            $latestSales = Penjualan::with('produk')
                ->orderBy('tanggal', 'desc')
                ->orderBy('id_penjualan', 'desc')
                ->limit(6)->get();

            $hasSalesToday = Penjualan::whereDate('tanggal', $today)->exists();
            $hasPrediksiToday = HasilPrediksi::whereDate('tanggal', $today)->exists();
            $hasAktualToday = HasilPrediksi::whereDate('tanggal', $today)->whereNotNull('hasil_aktual')->exists();
            $pendingAktualToday = HasilPrediksi::whereDate('tanggal', $today)->whereNull('hasil_aktual')->count();

            $checklist = [
                [
                    'label' => 'Input penjualan hari ini',
                    'done'  => $hasSalesToday,
                    'hint'  => $hasSalesToday ? 'Sudah diinput' : 'Belum ada penjualan hari ini',
                    'url'   => route('penjualan.index'),
                ],
                [
                    'label' => 'Buat prediksi produksi hari ini',
                    'done'  => $hasPrediksiToday,
                    'hint'  => $hasPrediksiToday ? 'Prediksi tersedia' : 'Belum ada prediksi hari ini',
                    'url'   => route('prediksi.index'),
                ],
                [
                    'label' => 'Input produksi aktual hari ini',
                    'done'  => $hasAktualToday,
                    'hint'  => $hasAktualToday ? 'Aktual sudah diisi' : 'Isi aktual dari riwayat prediksi',
                    'url'   => route('prediksi.riwayat'),
                ],
            ];

            $accRows = HasilPrediksi::whereNotNull('hasil_aktual')
                ->orderBy('tanggal', 'desc')
                ->orderBy('id_hasil_prediksi', 'desc')
                ->limit(30)
                ->get(['jumlah_produksi', 'hasil_aktual']);

            $accN = $accRows->count();
            $mae = 0;
            $errorPercent = 0;

            if ($accN > 0) {
                $sumAbs = 0;
                foreach ($accRows as $r) {
                    $pred = (float) $r->jumlah_produksi;
                    $akt  = (float) $r->hasil_aktual;
                    $sumAbs += abs($akt - $pred);
                }
                $mae = $sumAbs / $accN;

                $avgAktual = (float) $accRows->avg('hasil_aktual');
                if ($avgAktual > 0) {
                    $errorPercent = ($mae / $avgAktual) * 100;
                }
            }

            $warnings = [];

            if (!$hasSalesToday) {
                $warnings[] = [
                    'title' => 'Belum ada penjualan hari ini',
                    'text'  => 'Jika ada transaksi, input dulu agar stok & laporan akurat.',
                    'type'  => 'warn',
                    'url'   => route('penjualan.index'),
                ];
            }

            if (!$hasPrediksiToday) {
                $warnings[] = [
                    'title' => 'Prediksi produksi hari ini belum dibuat',
                    'text'  => 'Buat prediksi untuk membantu penentuan jumlah produksi.',
                    'type'  => 'warn',
                    'url'   => route('prediksi.index'),
                ];
            }

            if ($pendingAktualToday > 0) {
                $warnings[] = [
                    'title' => 'Aktual produksi hari ini belum lengkap',
                    'text'  => "Masih ada {$pendingAktualToday} data prediksi yang belum diisi aktual.",
                    'type'  => 'danger',
                    'url'   => route('prediksi.riwayat'),
                ];
            }

            if ($accN >= 10 && $mae > 100) {
                $warnings[] = [
                    'title' => 'Akurasi prediksi perlu dicek',
                    'text'  => 'Nilai MAE cukup tinggi. Pertimbangkan cek data training / aturan fuzzy / input variabel.',
                    'type'  => 'danger',
                    'url'   => route('training.index'),
                ];
            }

            return view('dashboard.index', compact(
                'totalProduk',
                'totalKategori',
                'totalTraining',
                'verifikasiCount',
                'latestPrediksi',
                'latestSales',
                'checklist',
                'pendingAktualToday',
                'mae',
                'accN',
                'errorPercent',
                'warnings'
            ));
        }

        // =========================
        // KEPALA PRODUKSI (level 1)
        // - fokus prediksi + input aktual
        // =========================
        if ((int)$user->level === 1) {

            $pendingAktualToday = HasilPrediksi::whereDate('tanggal', $today)
                ->whereNull('hasil_aktual')
                ->count();

            $verifikasiCount = HasilPrediksi::whereNotNull('hasil_aktual')->count();

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
                'pendingAktualToday',
                'verifikasiCount',
                'latestPrediksi',
                'prediksiHariIni'
            ));
        }

        // =========================
        // ADMIN (level 2)
        // - fokus penjualan
        // =========================
        if ((int)$user->level === 2) {

            $hasSalesToday = Penjualan::whereDate('tanggal', $today)->exists();

            $totalTerjualHariIni = (float) Penjualan::whereDate('tanggal', $today)->sum('jumlah');
            $totalItemHariIni    = (int)   Penjualan::whereDate('tanggal', $today)->count(); // jumlah baris item

            $latestSales = Penjualan::with('produk')
                ->orderBy('tanggal', 'desc')
                ->orderBy('id_penjualan', 'desc')
                ->limit(10)
                ->get();

            $warnings = [];
            if (!$hasSalesToday) {
                $warnings[] = [
                    'title' => 'Belum ada penjualan hari ini',
                    'text'  => 'Jika ada transaksi, segera input penjualan hari ini.',
                    'type'  => 'warn',
                    'url'   => route('penjualan.index'),
                ];
            }

            return view('dashboard.admin', compact(
                'totalTerjualHariIni',
                'totalItemHariIni',
                'latestSales',
                'warnings'
            ));
        }

        return redirect()->route('dashboard');
    }
}
