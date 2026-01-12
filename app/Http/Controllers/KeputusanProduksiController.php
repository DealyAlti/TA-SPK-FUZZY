<?php

namespace App\Http\Controllers;

use App\Models\HasilPrediksi;
use App\Models\KeputusanProduksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KeputusanProduksiController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->level != 0) abort(403);

        $tz = 'Asia/Jakarta';
        $tanggal = $request->get('tanggal', Carbon::today($tz)->format('Y-m-d'));

        // hanya produk yang ada hasil saran (hasil_prediksi) pada tanggal itu
        $saran = HasilPrediksi::with('produk')
            ->whereDate('tanggal', $tanggal)
            ->orderBy('id_produk', 'asc')
            ->get();

        // keputusan yang sudah pernah dikirim
        $keputusanMap = KeputusanProduksi::whereDate('tanggal', $tanggal)
            ->get()
            ->keyBy('id_produk');

        $now = Carbon::now($tz);

        // ✅ batas maksimal jam 09:00 pada tanggal yang dipilih
        $limit = Carbon::parse($tanggal.' 09:00:00', $tz);

        // ✅ terkunci kalau lewat 09:00 pada tanggal tsb
        $locked = $now->gt($limit);

        return view('keputusan.index', compact('tanggal','saran','keputusanMap','now','locked','limit'));
    }

    public function lihat(Request $request)
    {
        if (auth()->user()->level != 1) abort(403);

        $tz = 'Asia/Jakarta';
        $tanggal = $request->get('tanggal', Carbon::today($tz)->format('Y-m-d'));

        $data = KeputusanProduksi::with('produk')
            ->whereDate('tanggal', $tanggal)
            ->orderBy('id_produk', 'asc')
            ->get();

        return view('keputusan.lihat', compact('tanggal', 'data'));
    }

    public function kirim(Request $request)
    {
        if (auth()->user()->level != 0) abort(403);

        $tz = 'Asia/Jakarta';

        $request->validate([
            'tanggal' => 'required|date',
            'rows'    => 'required|array',
        ]);

        $tanggal = $request->tanggal;

        // ✅ maksimal jam 09:00 pada tanggal tersebut
        $now   = Carbon::now($tz);
        $limit = Carbon::parse($tanggal.' 09:00:00', $tz);

        if ($now->gt($limit)) {
            return back()->withInput()->withErrors([
                'tanggal' => "Keputusan untuk tanggal {$tanggal} terkunci (maksimal kirim/update sampai 09:00 WIB)."
            ]);
        }

        // harus ada saran dulu
        $saranMap = HasilPrediksi::whereDate('tanggal', $tanggal)
            ->get()
            ->keyBy('id_produk');

        if ($saranMap->isEmpty()) {
            return back()->withInput()->withErrors([
                'tanggal' => "Belum ada hasil saran untuk tanggal {$tanggal}."
            ]);
        }

        $rows = $request->rows;

        DB::transaction(function () use ($rows, $tanggal, $saranMap) {

            foreach ($rows as $idProduk => $row) {
                $idProduk = (int) $idProduk;

                // hanya produk yg ada sarannya
                if (!$saranMap->has($idProduk)) continue;

                $hp = $saranMap[$idProduk];
                $jumlahSaran = (float) $hp->jumlah_produksi;

                $pakaiSaran = isset($row['pakai_saran']) && $row['pakai_saran'] == '1';

                $manualRaw = $row['jumlah_keputusan'] ?? null;
                $manualRaw = is_string($manualRaw) ? trim($manualRaw) : $manualRaw;

                // kalau tidak pakai saran, wajib isi manual
                if (!$pakaiSaran && ($manualRaw === null || $manualRaw === '')) {
                    throw ValidationException::withMessages([
                        'rows' => 'Ada keputusan yang belum diisi (checkbox tidak dicentang).'
                    ]);
                }

                $manual = ($manualRaw === null || $manualRaw === '') ? null : (float) $manualRaw;

                $jumlahKeputusan = $pakaiSaran ? $jumlahSaran : (float) $manual;
                if ($jumlahKeputusan < 0) $jumlahKeputusan = 0;

                // ✅ update kalau sudah ada (jadi bisa kirim ulang sebelum jam 9)
                KeputusanProduksi::updateOrCreate(
                    [
                        'tanggal'   => $tanggal,
                        'id_produk' => $idProduk,
                    ],
                    [
                        'id_hasil_prediksi' => $hp->id_hasil_prediksi,
                        'id_user'           => auth()->id(),
                        'jumlah_saran'      => $jumlahSaran,
                        'jumlah_keputusan'  => $jumlahKeputusan,
                        'pakai_saran'       => $pakaiSaran,
                        'diputuskan_pada'   => now(), // update timestamp keputusan
                    ]
                );
            }
        });

        return redirect()
            ->route('keputusan.index', ['tanggal' => $tanggal])
            ->with('success', "Keputusan produksi untuk tanggal {$tanggal} berhasil disimpan/diupdate.");
    }
}
