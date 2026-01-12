<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use App\Models\DataTraining;
use App\Models\HasilPrediksi;
use App\Models\KeputusanProduksi;
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

            $tz = 'Asia/Jakarta';

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

            // =========================
            // ✅ KEPUTUSAN PRODUKSI (BARU)
            // =========================
            $hasKeputusanToday = KeputusanProduksi::whereDate('tanggal', $today)->exists();

            $now   = Carbon::now($tz);
            $limit = Carbon::parse($today . ' 09:00:00', $tz);

            $minutesToDeadline = $now->diffInMinutes($limit, false); // bisa negatif kalau lewat
            $isAfterDeadline   = $now->gt($limit);
            $isNearDeadline    = (!$isAfterDeadline && $minutesToDeadline <= 30); // <= 30 menit

            // checklist (ditambah keputusan)
            $checklist = [
                [
                    'label' => 'Buat saran produksi hari ini',
                    'done'  => $hasPrediksiToday,
                    'hint'  => $hasPrediksiToday ? 'Saran produksi tersedia' : 'Belum ada saran hari ini',
                    'url'   => route('prediksi.index'),
                ],
                [
                    'label' => 'Kirim keputusan produksi hari ini',
                    'done'  => $hasKeputusanToday,
                    'hint'  => $hasKeputusanToday
                        ? 'Keputusan sudah dikirim'
                        : ($isAfterDeadline ? 'Lewat batas 09:00 (belum ada keputusan)' : 'Belum ada keputusan hari ini'),
                    'url'   => route('keputusan.index', ['tanggal' => $today]),
                ],
                [
                    'label' => 'Pastikan data training terbaru sudah lengkap',
                    'done'  => ($totalTraining > 0),
                    'hint'  => ($totalTraining > 0) ? 'Data training tersedia' : 'Belum ada data training',
                    'url'   => route('training.index'),
                ],
            ];

            // warnings (ditambah keputusan)
            $warnings = [];

            // warning saran
            if (!$hasPrediksiToday) {
                $warnings[] = [
                    'title' => 'Saran produksi hari ini belum dibuat',
                    'text'  => 'Buat saran produksi agar bisa menentukan jumlah produksi hari ini.',
                    'type'  => 'warn',
                    'url'   => route('prediksi.index'),
                ];
            }

            // warning keputusan
            if (!$hasKeputusanToday) {
                if ($isAfterDeadline) {
                    $warnings[] = [
                        'title' => 'Keputusan produksi belum dikirim (lewat batas 09:00 WIB)',
                        'text'  => 'Keputusan belum dibuat. Sistem terkunci setelah jam 09:00.',
                        'type'  => 'danger',
                        'url'   => route('keputusan.index', ['tanggal' => $today]),
                    ];
                } elseif ($isNearDeadline) {
                    $warnings[] = [
                        'title' => 'Batas keputusan produksi hampir habis',
                        'text'  => 'Segera kirim keputusan sebelum jam 09:00 WIB.',
                        'type'  => 'warn',
                        'url'   => route('keputusan.index', ['tanggal' => $today]),
                    ];
                } else {
                    $warnings[] = [
                        'title' => 'Keputusan produksi hari ini belum dikirim',
                        'text'  => 'Setelah saran dibuat, kirim keputusan produksi untuk dipakai kepala produksi.',
                        'type'  => 'warn',
                        'url'   => route('keputusan.index', ['tanggal' => $today]),
                    ];
                }
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
        // Fokus: Keputusan Produksi
        // =========================
        if ((int) $user->level === 1) {

            $keputusanHariIni = KeputusanProduksi::with('produk')
                ->whereDate('tanggal', $today)
                ->orderBy('id_produk', 'asc')
                ->get();

            $totalKeputusanHariIni = $keputusanHariIni->count();
            $totalKeputusan        = KeputusanProduksi::count();

            return view('dashboard.kepala_produksi', compact(
                'keputusanHariIni',
                'totalKeputusanHariIni',
                'totalKeputusan'
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
