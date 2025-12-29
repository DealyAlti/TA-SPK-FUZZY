<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\DataTraining;
use App\Models\HasilPrediksi;
use Illuminate\Http\Request;

class HasilPrediksiController extends Controller
{
    public function index()
    {
        $produk = Produk::orderBy('nama_produk')->get();
        return view('prediksi.index', compact('produk'));
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

        // Cegah double input di tanggal & produk yang sama
        $exists = HasilPrediksi::where('id_produk', $request->id_produk)
            ->whereDate('tanggal', $request->tanggal)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'tanggal' => 'Saran untuk produk ini pada tanggal tersebut sudah pernah dibuat.'
                ]);
        }

        $produk   = Produk::with('kategori')->findOrFail($request->id_produk);
        $kategori = $produk->kategori;

        // ================== MIN–MAX DINAMIS dari Data Training ==================
        $trainingQuery = DataTraining::where('id_produk', $produk->id_produk);

        // >0 supaya nilai 0 (tidak produksi / kosong) tidak ikut min-max
        $penjualanMin = (clone $trainingQuery)->where('penjualan', '>', 0)->min('penjualan');
        $penjualanMax = (clone $trainingQuery)->where('penjualan', '>', 0)->max('penjualan');

        $stokMin      = (clone $trainingQuery)->where('stok_barang_jadi', '>', 0)->min('stok_barang_jadi');
        $stokMax      = (clone $trainingQuery)->where('stok_barang_jadi', '>', 0)->max('stok_barang_jadi');

        $produksiMin  = (clone $trainingQuery)->where('hasil_produksi', '>', 0)->min('hasil_produksi');
        $produksiMax  = (clone $trainingQuery)->where('hasil_produksi', '>', 0)->max('hasil_produksi');

        // fallback kalau kosong / tidak valid
        if (is_null($penjualanMin) || is_null($penjualanMax) || $penjualanMax <= $penjualanMin) {
            $penjualanMin = 0; $penjualanMax = 1;
        }
        if (is_null($stokMin) || is_null($stokMax) || $stokMax <= $stokMin) {
            $stokMin = 0; $stokMax = 1;
        }
        if (is_null($produksiMin) || is_null($produksiMax) || $produksiMax <= $produksiMin) {
            $produksiMin = 0; $produksiMax = 1;
        }

        // ============== 1. INPUT CRISP ==============
        $input = [
            'tanggal'            => $request->tanggal,
            'penjualan'          => (float) $request->penjualan,
            'waktu_produksi'     => (float) $request->waktu_produksi,
            'stok_barang_jadi'   => (float) $request->stok_barang_jadi,
            'kapasitas_produksi' => (float) $request->kapasitas_produksi,
        ];
        $kapasitasInput = $input['kapasitas_produksi'];

        // batas atas produksi disesuaikan kapasitas
        $produksiMaxEfektif = min((float) $produksiMax, $kapasitasInput);
        if ($produksiMaxEfektif <= $produksiMin) {
            // jaga-jaga biar tidak degenerate
            $produksiMaxEfektif = $produksiMin + 1;
        }

        $minmax = [
            'penjualan' => [
                'min' => (float) $penjualanMin,
                'max' => (float) $penjualanMax
            ],
            'waktu'     => [
                'min' => (float) ($kategori->waktu_min ?? 0),
                'max' => (float) ($kategori->waktu_max ?? 1)
            ],
            'stok'      => [
                'min' => (float) $stokMin,
                'max' => (float) $stokMax
            ],
            'kapasitas' => [
                'min' => (float) ($kategori->kapasitas_min ?? 0),
                'max' => (float) ($kategori->kapasitas_max ?? 1)
            ],
            'produksi'  => [
                'min' => (float) $produksiMin,
                'max' => (float) $produksiMaxEfektif, // ⚡ sudah dibatasi kapasitas
            ],
        ];

        // ============== 2. FUZZYFIKASI ==============
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

        // ============== 3. RULE BASE (16 RULE) ==============
        $rules = [
            ['kode' => 'R1',  'P' => 'tinggi', 'W' => 'cepat', 'S' => 'sedikit', 'K' => 'rendah', 'out' => 'sedikit'],
            ['kode' => 'R2',  'P' => 'tinggi', 'W' => 'cepat', 'S' => 'sedikit', 'K' => 'tinggi', 'out' => 'banyak'],
            ['kode' => 'R3',  'P' => 'tinggi', 'W' => 'cepat', 'S' => 'banyak',  'K' => 'rendah', 'out' => 'sedikit'],
            ['kode' => 'R4',  'P' => 'tinggi', 'W' => 'cepat', 'S' => 'banyak',  'K' => 'tinggi', 'out' => 'sedikit'],

            ['kode' => 'R5',  'P' => 'tinggi', 'W' => 'lama',  'S' => 'sedikit', 'K' => 'rendah', 'out' => 'sedikit'],
            ['kode' => 'R6',  'P' => 'tinggi', 'W' => 'lama',  'S' => 'sedikit', 'K' => 'tinggi', 'out' => 'sedikit'],
            ['kode' => 'R7',  'P' => 'tinggi', 'W' => 'lama',  'S' => 'banyak',  'K' => 'rendah', 'out' => 'sedikit'],
            ['kode' => 'R8',  'P' => 'tinggi', 'W' => 'lama',  'S' => 'banyak',  'K' => 'tinggi', 'out' => 'sedikit'],

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

        $minOut   = (float) $minmax['produksi']['min'];
        $maxOut   = (float) $minmax['produksi']['max']; // sudah efektif: <= kapasitas
        $rangeOut = max($maxOut - $minOut, 0.0);

        foreach ($rules as $rule) {
            $alpha = min(
                $mu['penjualan'][$rule['P']],
                $mu['waktu'][$rule['W']],
                $mu['stok'][$rule['S']],
                $mu['kapasitas'][$rule['K']]
            );

            if ($rule['out'] === 'banyak') {
                $z = $minOut + $alpha * $rangeOut; // naik (monoton)
            } else {
                $z = $maxOut - $alpha * $rangeOut; // turun (monoton)
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

        // ============== 4. DEFUZZIFIKASI ==============
        $zAkhir = $sumAlpha > 0 ? ($sumAlphaZ / $sumAlpha) : 0.0;
        $zBulat = (int) round($zAkhir);
        if ($zBulat < 0) $zBulat = 0;

        // ================== SIMPAN + SNAPSHOT ==================
        HasilPrediksi::create([
            'id_produk'          => $produk->id_produk,
            'tanggal'            => $input['tanggal'],
            'penjualan'          => $input['penjualan'],
            'waktu_produksi'     => $input['waktu_produksi'],
            'stok_barang_jadi'   => $input['stok_barang_jadi'],
            'kapasitas_produksi' => $input['kapasitas_produksi'],
            'jumlah_produksi'    => $zBulat,

            'detail_minmax'      => $minmax,
            'detail_mu'          => $mu,
            'detail_rules'       => $detailRules,
            'detail_sum_alpha'   => $sumAlpha,
            'detail_sum_alpha_z' => $sumAlphaZ,
            'detail_z_akhir'     => $zAkhir,
        ]);

        // simpan ke session (buat halaman hasil/detail)
        $hasil = [
            'produk'      => $produk,
            'input'       => $input,
            'minmax'      => $minmax,
            'mu'          => $mu,
            'rules'       => $detailRules,
            'z_akhir'     => $zAkhir,
            'z_bulat'     => $zBulat,
            'sum_alpha'   => $sumAlpha,
            'sum_alpha_z' => $sumAlphaZ,
        ];

        session(['hasil_prediksi' => $hasil]);

        return redirect()->route('prediksi.hasil');
    }

    public function hasil()
    {
        $hasil = session('hasil_prediksi');
        if (!$hasil) {
            return redirect()->route('prediksi.index')->with('error', 'Silakan lakukan saran terlebih dahulu.');
        }
        return view('prediksi.hasil', compact('hasil'));
    }

    public function detail()
    {
        $hasil = session('hasil_prediksi');
        if (!$hasil) {
            return redirect()->route('prediksi.index')->with('error', 'Silakan lakukan saran terlebih dahulu.');
        }
        return view('prediksi.detail', compact('hasil'));
    }

    public function riwayat(Request $request)
    {
        $query = HasilPrediksi::with('produk')
            ->orderBy('tanggal', 'desc')
            ->orderBy('id_hasil_prediksi', 'desc');

        if ($request->filled('id_produk')) {
            $query->where('id_produk', $request->id_produk);
        }

        $riwayat = $query->paginate(15)->withQueryString();
        $produk  = Produk::orderBy('nama_produk')->get();

        return view('prediksi.riwayat', compact('riwayat', 'produk'));
    }

    public function detailById($id)
    {
        $prediksi = HasilPrediksi::with('produk.kategori')->findOrFail($id);
        $produk   = $prediksi->produk;
        $kategori = $produk->kategori;

        // ===================== 1. Kalau ada SNAPSHOT → pakai ini (tidak hitung ulang) =====================
        if ($prediksi->detail_minmax && $prediksi->detail_mu && $prediksi->detail_rules) {
            $hasil = [
                'produk'      => $produk,
                'input'       => [
                    'tanggal'            => $prediksi->tanggal,
                    'penjualan'          => (float) $prediksi->penjualan,
                    'waktu_produksi'     => (float) $prediksi->waktu_produksi,
                    'stok_barang_jadi'   => (float) $prediksi->stok_barang_jadi,
                    'kapasitas_produksi' => (float) $prediksi->kapasitas_produksi,
                ],
                'minmax'      => $prediksi->detail_minmax,
                'mu'          => $prediksi->detail_mu,
                'rules'       => $prediksi->detail_rules,
                'z_akhir'     => $prediksi->detail_z_akhir ?? 0,
                'z_bulat'     => (int) $prediksi->jumlah_produksi,
                'sum_alpha'   => $prediksi->detail_sum_alpha ?? 0,
                'sum_alpha_z' => $prediksi->detail_sum_alpha_z ?? 0,
            ];

            return view('prediksi.detail', compact('hasil'));
        }

        // ===================== 2. Fallback (data lama tanpa snapshot) → hitung ulang =====================
        $trainingQuery = DataTraining::where('id_produk', $produk->id_produk);

        $penjualanMin = (clone $trainingQuery)->where('penjualan','>',0)->min('penjualan');
        $penjualanMax = (clone $trainingQuery)->where('penjualan','>',0)->max('penjualan');

        $stokMin = (clone $trainingQuery)->where('stok_barang_jadi','>',0)->min('stok_barang_jadi');
        $stokMax = (clone $trainingQuery)->where('stok_barang_jadi','>',0)->max('stok_barang_jadi');

        $produksiMin = (clone $trainingQuery)->where('hasil_produksi','>',0)->min('hasil_produksi');
        $produksiMax = (clone $trainingQuery)->where('hasil_produksi','>',0)->max('hasil_produksi');

        if (is_null($penjualanMin) || is_null($penjualanMax) || $penjualanMax <= $penjualanMin) { $penjualanMin = 0; $penjualanMax = 1; }
        if (is_null($stokMin) || is_null($stokMax) || $stokMax <= $stokMin) { $stokMin = 0; $stokMax = 1; }
        if (is_null($produksiMin) || is_null($produksiMax) || $produksiMax <= $produksiMin) { $produksiMin = 0; $produksiMax = 1; }

        $input = [
            'tanggal'            => $prediksi->tanggal,
            'penjualan'          => (float) $prediksi->penjualan,
            'waktu_produksi'     => (float) $prediksi->waktu_produksi,
            'stok_barang_jadi'   => (float) $prediksi->stok_barang_jadi,
            'kapasitas_produksi' => (float) $prediksi->kapasitas_produksi,
        ];
        $kapasitasInput = $input['kapasitas_produksi'];

        $produksiMaxEfektif = min((float) $produksiMax, $kapasitasInput);
        if ($produksiMaxEfektif <= $produksiMin) {
            $produksiMaxEfektif = $produksiMin + 1;
        }

        $minmax = [
            'penjualan' => ['min'=>(float)$penjualanMin,'max'=>(float)$penjualanMax],
            'waktu'     => ['min'=>(float)($kategori->waktu_min ?? 0),'max'=>(float)($kategori->waktu_max ?? 1)],
            'stok'      => ['min'=>(float)$stokMin,'max'=>(float)$stokMax],
            'kapasitas' => ['min'=>(float)($kategori->kapasitas_min ?? 0),'max'=>(float)($kategori->kapasitas_max ?? 1)],
            'produksi'  => ['min'=>(float)$produksiMin,'max'=>(float)$produksiMaxEfektif],
        ];

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

        $rules = [
            ['kode'=>'R1','P'=>'tinggi','W'=>'cepat','S'=>'sedikit','K'=>'rendah','out'=>'sedikit'],
            ['kode'=>'R2','P'=>'tinggi','W'=>'cepat','S'=>'sedikit','K'=>'tinggi','out'=>'banyak'],
            ['kode'=>'R3','P'=>'tinggi','W'=>'cepat','S'=>'banyak','K'=>'rendah','out'=>'sedikit'],
            ['kode'=>'R4','P'=>'tinggi','W'=>'cepat','S'=>'banyak','K'=>'tinggi','out'=>'sedikit'],
            ['kode'=>'R5','P'=>'tinggi','W'=>'lama','S'=>'sedikit','K'=>'rendah','out'=>'sedikit'],
            ['kode'=>'R6','P'=>'tinggi','W'=>'lama','S'=>'sedikit','K'=>'tinggi','out'=>'sedikit'],
            ['kode'=>'R7','P'=>'tinggi','W'=>'lama','S'=>'banyak','K'=>'rendah','out'=>'sedikit'],
            ['kode'=>'R8','P'=>'tinggi','W'=>'lama','S'=>'banyak','K'=>'tinggi','out'=>'sedikit'],
            ['kode'=>'R9','P'=>'rendah','W'=>'cepat','S'=>'sedikit','K'=>'rendah','out'=>'sedikit'],
            ['kode'=>'R10','P'=>'rendah','W'=>'cepat','S'=>'sedikit','K'=>'tinggi','out'=>'sedikit'],
            ['kode'=>'R11','P'=>'rendah','W'=>'cepat','S'=>'banyak','K'=>'rendah','out'=>'sedikit'],
            ['kode'=>'R12','P'=>'rendah','W'=>'cepat','S'=>'banyak','K'=>'tinggi','out'=>'sedikit'],
            ['kode'=>'R13','P'=>'rendah','W'=>'lama','S'=>'sedikit','K'=>'rendah','out'=>'sedikit'],
            ['kode'=>'R14','P'=>'rendah','W'=>'lama','S'=>'sedikit','K'=>'tinggi','out'=>'sedikit'],
            ['kode'=>'R15','P'=>'rendah','W'=>'lama','S'=>'banyak','K'=>'rendah','out'=>'sedikit'],
            ['kode'=>'R16','P'=>'rendah','W'=>'lama','S'=>'banyak','K'=>'tinggi','out'=>'sedikit'],
        ];

        $detailRules = [];
        $sumAlphaZ   = 0;
        $sumAlpha    = 0;

        $minOut   = (float)$minmax['produksi']['min'];
        $maxOut   = (float)$minmax['produksi']['max']; // sudah <= kapasitas
        $rangeOut = max($maxOut - $minOut, 0.0);

        foreach ($rules as $rule) {
            $alpha = min(
                $mu['penjualan'][$rule['P']],
                $mu['waktu'][$rule['W']],
                $mu['stok'][$rule['S']],
                $mu['kapasitas'][$rule['K']]
            );

            $z = ($rule['out'] === 'banyak')
                ? ($minOut + $alpha * $rangeOut)
                : ($maxOut - $alpha * $rangeOut);

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

        $zAkhir = $sumAlpha > 0 ? ($sumAlphaZ / $sumAlpha) : 0.0;

        $hasil = [
            'produk'      => $produk,
            'input'       => $input,
            'minmax'      => $minmax,
            'mu'          => $mu,
            'rules'       => $detailRules,
            'z_akhir'     => $zAkhir,
            'z_bulat'     => (int) $prediksi->jumlah_produksi,
            'sum_alpha'   => $sumAlpha,
            'sum_alpha_z' => $sumAlphaZ,
        ];

        return view('prediksi.detail', compact('hasil'));
    }

    // ================= helper fungsi keanggotaan linear =================
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
