<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\DataTraining;
use App\Models\HasilPrediksi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HasilPrediksiController extends Controller
{
    public function index()
    {
        $produk = Produk::orderBy('nama_produk')->get();

        return view('prediksi.index', [
            'produk' => $produk,
            'hasil'  => null,
        ]);
    }

    public function hitung(Request $request)
    {
        $request->validate([
            'id_produk'          => 'required|exists:produk,id_produk',
            'tanggal'            => 'required|date',
            'penjualan'          => 'required|numeric|min:0',
            'waktu_produksi'     => 'required|numeric|min:0',
            'stok_barang_jadi'   => 'required|numeric|min:0',
            'kapasitas_produksi' => 'required|numeric|min:0',
        ]);

        $produk = Produk::with('kategori')->findOrFail($request->id_produk);
        $kategori = $produk->kategori; // pastikan relasi Produk->kategori sudah dibuat

        /**
         * ================== MIN–MAX DINAMIS ==================
         * - Penjualan, stok, produksi: dari tabel data_training untuk produk ini
         * - 0 TIDAK ikut dihitung (karena artinya tidak produksi / tidak ada transaksi)
         * - Waktu & kapasitas: dari tabel kategori (field: waktu_min, waktu_max, kapasitas_min, kapasitas_max)
         */
        $trainingQuery = DataTraining::where('id_produk', $produk->id_produk);

        $penjualanMin = (clone $trainingQuery)->where('penjualan', '>', 0)->min('penjualan');
        $penjualanMax = (clone $trainingQuery)->where('penjualan', '>', 0)->max('penjualan');

        $stokMin      = (clone $trainingQuery)->where('stok_barang_jadi', '>', 0)->min('stok_barang_jadi');
        $stokMax      = (clone $trainingQuery)->where('stok_barang_jadi', '>', 0)->max('stok_barang_jadi');

        $produksiMin  = (clone $trainingQuery)->where('hasil_produksi', '>', 0)->min('hasil_produksi');
        $produksiMax  = (clone $trainingQuery)->where('hasil_produksi', '>', 0)->max('hasil_produksi');

        // fallback kalau data training belum ada
        if (is_null($penjualanMin) || is_null($penjualanMax)) {
            $penjualanMin = 0;
            $penjualanMax = 1;
        }
        if (is_null($stokMin) || is_null($stokMax)) {
            $stokMin = 0;
            $stokMax = 1;
        }
        if (is_null($produksiMin) || is_null($produksiMax)) {
            $produksiMin = 0;
            $produksiMax = 1;
        }

        $minmax = [
            'penjualan' => ['min' => $penjualanMin,                'max' => $penjualanMax],
            'waktu'     => ['min' => $kategori->waktu_min ?? 0,    'max' => $kategori->waktu_max ?? 1],
            'stok'      => ['min' => $stokMin,                     'max' => $stokMax],
            'kapasitas' => ['min' => $kategori->kapasitas_min ?? 0,'max' => $kategori->kapasitas_max ?? 1],
            'produksi'  => ['min' => $produksiMin,                 'max' => $produksiMax],
        ];

        // ============== INPUT CRISP ==============
        $input = [
            'penjualan'          => (float) $request->penjualan,
            'waktu_produksi'     => (float) $request->waktu_produksi,
            'stok_barang_jadi'   => (float) $request->stok_barang_jadi,
            'kapasitas_produksi' => (float) $request->kapasitas_produksi,
        ];

        // ============== 1. FUZZYFIKASI ==============
        $mu = [
            'penjualan' => [
                'rendah' => $this->muLow($input['penjualan'], $minmax['penjualan']['min'], $minmax['penjualan']['max']),
                'tinggi' => $this->muHigh($input['penjualan'], $minmax['penjualan']['min'], $minmax['penjualan']['max']),
            ],
            'waktu' => [
                'cepat' => $this->muLow($input['waktu_produksi'], $minmax['waktu']['min'], $minmax['waktu']['max']),
                'lama'  => $this->muHigh($input['waktu_produksi'], $minmax['waktu']['min'], $minmax['waktu']['max']),
            ],
            'stok' => [
                'sedikit' => $this->muLow($input['stok_barang_jadi'], $minmax['stok']['min'], $minmax['stok']['max']),
                'banyak'  => $this->muHigh($input['stok_barang_jadi'], $minmax['stok']['min'], $minmax['stok']['max']),
            ],
            'kapasitas' => [
                'rendah' => $this->muLow($input['kapasitas_produksi'], $minmax['kapasitas']['min'], $minmax['kapasitas']['max']),
                'tinggi' => $this->muHigh($input['kapasitas_produksi'], $minmax['kapasitas']['min'], $minmax['kapasitas']['max']),
            ],
        ];

        // ============== 2. RULE BASE (16 RULE) ==============
        $rules = [
            ['kode' => 'R1',  'P' => 'tinggi',  'W' => 'cepat', 'S' => 'sedikit', 'K' => 'rendah', 'out' => 'banyak'],
            ['kode' => 'R2',  'P' => 'tinggi',  'W' => 'cepat', 'S' => 'sedikit', 'K' => 'tinggi', 'out' => 'sedikit'],
            ['kode' => 'R3',  'P' => 'tinggi',  'W' => 'cepat', 'S' => 'banyak',  'K' => 'rendah', 'out' => 'sedikit'],
            ['kode' => 'R4',  'P' => 'tinggi',  'W' => 'cepat', 'S' => 'banyak',  'K' => 'tinggi', 'out' => 'sedikit'],
            ['kode' => 'R5',  'P' => 'tinggi',  'W' => 'lama',  'S' => 'sedikit', 'K' => 'rendah', 'out' => 'sedikit'],
            ['kode' => 'R6',  'P' => 'tinggi',  'W' => 'lama',  'S' => 'sedikit', 'K' => 'tinggi', 'out' => 'sedikit'],
            ['kode' => 'R7',  'P' => 'tinggi',  'W' => 'lama',  'S' => 'banyak',  'K' => 'rendah', 'out' => 'sedikit'],
            ['kode' => 'R8',  'P' => 'tinggi',  'W' => 'lama',  'S' => 'banyak',  'K' => 'tinggi', 'out' => 'sedikit'],

            ['kode' => 'R9',  'P' => 'rendah', 'W' => 'cepat', 'S' => 'sedikit', 'K' => 'rendah', 'out' => 'sedikit'],
            ['kode' => 'R10', 'P' => 'rendah', 'W' => 'cepat', 'S' => 'sedikit', 'K' => 'tinggi', 'out' => 'sedikit'],
            ['kode' => 'R11', 'P' => 'rendah', 'W' => 'cepat', 'S' => 'banyak',  'K' => 'rendah', 'out' => 'sedikit'],
            ['kode' => 'R12', 'P' => 'rendah', 'W' => 'cepat', 'S' => 'banyak',  'K' => 'tinggi', 'out' => 'sedikit'],
            ['kode' => 'R13', 'P' => 'rendah', 'W' => 'lama',  'S' => 'sedikit', 'K' => 'rendah', 'out' => 'sedikit'],
            ['kode' => 'R14', 'P' => 'rendah', 'W' => 'lama',  'S' => 'sedikit', 'K' => 'tinggi', 'out' => 'sedikit'],
            ['kode' => 'R15', 'P' => 'rendah', 'W' => 'lama',  'S' => 'banyak',  'K' => 'rendah', 'out' => 'sedikit'],
            ['kode' => 'R16', 'P' => 'rendah', 'W' => 'lama',  'S' => 'banyak',  'K' => 'tinggi', 'out' => 'sedikit'],
        ];

        $detailRules = [];
        $sumAlphaZ   = 0;
        $sumAlpha    = 0;
        $minOut  = $minmax['produksi']['min'];
        $maxOut  = $minmax['produksi']['max'];
        $rangeOut = $maxOut - $minOut;

        foreach ($rules as $rule) {
            $alpha = min(
                $mu['penjualan'][$rule['P']],
                $mu['waktu'][$rule['W']],
                $mu['stok'][$rule['S']],
                $mu['kapasitas'][$rule['K']]
            );

            if ($rule['out'] === 'banyak') {
                $z = $minOut + $alpha * $rangeOut;     // fungsi naik
            } else {
                $z = $maxOut - $alpha * $rangeOut;     // fungsi turun
            }

            $detailRules[] = [
                'kode'  => $rule['kode'],
                'P'     => $rule['P'],
                'W'     => $rule['W'],
                'S'     => $rule['S'],
                'K'     => $rule['K'],
                'out'   => $rule['out'],
                'alpha' => $alpha,
                'z'     => $z,
            ];

            $sumAlphaZ += $alpha * $z;
            $sumAlpha  += $alpha;
        }

        $zAkhir = $sumAlpha > 0 ? $sumAlphaZ / $sumAlpha : 0;

        // opsional: simpan ke tabel hasil_prediksi
// SEBELUM
// SESUDAH
        HasilPrediksi::create([
            'id_produk'          => $produk->id_produk,
            'tanggal'            => $request->tanggal,
            'penjualan'          => $input['penjualan'],
            'waktu_produksi'     => $input['waktu_produksi'],
            'stok_barang_jadi'   => $input['stok_barang_jadi'],
            'kapasitas_produksi' => $input['kapasitas_produksi'],
            'jumlah_produksi'    => $zAkhir,   // <-- pakai nama kolom di DB
        ]);



        $hasil = [
            'produk'       => $produk,
            'input'        => $input,
            'minmax'       => $minmax,
            'mu'           => $mu,
            'rules'        => $detailRules,
            'z_akhir'      => $zAkhir,
            'sum_alpha'    => $sumAlpha,
            'sum_alpha_z'  => $sumAlphaZ,
        ];

        $produkList = Produk::orderBy('nama_produk')->get();

        return view('prediksi.index', [
            'produk' => $produkList,
            'hasil'  => $hasil,
        ]);
    }

    // ===== helper fungsi keanggotaan linear =====
    private function muLow(float $x, float $min, float $max): float
    {
        if ($x <= $min) return 1.0;
        if ($x >= $max) return 0.0;
        return ($max - $x) / max($max - $min, 0.000001);
    }

    private function muHigh(float $x, float $min, float $max): float
    {
        if ($x <= $min) return 0.0;
        if ($x >= $max) return 1.0;
        return ($x - $min) / max($max - $min, 0.000001);
    }
}
