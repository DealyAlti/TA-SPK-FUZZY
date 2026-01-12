<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\DataTraining;
use App\Models\HasilPrediksi;
use App\Models\HasilPrediksiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HasilPrediksiController extends Controller
{
    /* ==============================
     *  INDEX
     * ============================== */
    public function index()
    {
        $produk = Produk::orderBy('nama_produk')->get();
        return view('prediksi.index', compact('produk'));
    }

    /* ==============================
     *  HITUNG
     * ============================== */
    public function hitung(Request $request)
    {
        $request->validate(
            [
                'id_produk'          => 'required|exists:produk,id_produk',
                'tanggal'            => 'required|date',
                'penjualan'          => 'required|numeric|min:0',
                'waktu_produksi'     => 'required|numeric|min:0',
                'stok_barang_jadi'   => 'required|numeric|min:0',
                'kapasitas_produksi' => 'required|numeric|min:0',
            ],
            [
                // Produk
                'id_produk.required' => 'Nama produk wajib diisi',

                // min 0
                'penjualan.min'          => 'Angka tidak boleh kurang dr 0',
                'waktu_produksi.min'     => 'Angka tidak boleh kurang dr 0',
                'stok_barang_jadi.min'   => 'Angka tidak boleh kurang dr 0',
                'kapasitas_produksi.min' => 'Angka tidak boleh kurang dr 0',
            ]
        );

        // cegah duplikat
        $exists = HasilPrediksi::where('id_produk', $request->id_produk)
            ->whereDate('tanggal', $request->tanggal)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'tanggal' => 'Saran untuk produk ini pada tanggal tersebut sudah pernah dibuat.'
            ]);
        }

        $produk   = Produk::with('kategori')->findOrFail($request->id_produk);
        $kategori = $produk->kategori;

        $input = [
            'tanggal'            => $request->tanggal,
            'penjualan'          => (float) $request->penjualan,
            'waktu_produksi'     => (float) $request->waktu_produksi,
            'stok_barang_jadi'   => (float) $request->stok_barang_jadi,
            'kapasitas_produksi' => (float) $request->kapasitas_produksi,
        ];

        // hitung fuzzy
        $snap = $this->computeFuzzy($produk->id_produk, $kategori, $input);

        $prediksi = null;

        DB::transaction(function () use ($produk, $input, $snap, &$prediksi) {
            $prediksi = HasilPrediksi::create([
                'id_produk'          => $produk->id_produk,
                'tanggal'            => $input['tanggal'],
                'penjualan'          => $input['penjualan'],
                'waktu_produksi'     => $input['waktu_produksi'],
                'stok_barang_jadi'   => $input['stok_barang_jadi'],
                'kapasitas_produksi' => $input['kapasitas_produksi'],
                'jumlah_produksi'    => $snap['z_bulat'],
            ]);

            HasilPrediksiDetail::create([
                'id_hasil_prediksi'  => $prediksi->id_hasil_prediksi,
                'detail_minmax'      => $snap['minmax'],
                'detail_mu'          => $snap['mu'],
                'detail_rules'       => $snap['rules'],
                'detail_sum_alpha'   => $snap['sum_alpha'],
                'detail_sum_alpha_z' => $snap['sum_alpha_z'],
                'detail_z_akhir'     => $snap['z_akhir'],
            ]);
        });

        // session untuk halaman hasil/detail
        session(['hasil_prediksi' => [
            'produk'      => $produk,
            'input'       => $input,
            'minmax'      => $snap['minmax'],
            'mu'          => $snap['mu'],
            'rules'       => $snap['rules'],
            'z_akhir'     => $snap['z_akhir'],
            'z_bulat'     => $snap['z_bulat'],
            'sum_alpha'   => $snap['sum_alpha'],
            'sum_alpha_z' => $snap['sum_alpha_z'],
        ]]);

        return redirect()->route('prediksi.hasil');
    }

    /* ==============================
     *  HASIL (SESSION)
     * ============================== */
    public function hasil()
    {
        $hasil = session('hasil_prediksi');
        if (!$hasil) {
            return redirect()->route('prediksi.index')->with('error', 'Silakan lakukan saran terlebih dahulu.');
        }
        return view('prediksi.hasil', compact('hasil'));
    }

    /* ==============================
     *  DETAIL (SESSION)
     * ============================== */
    public function detail()
    {
        $hasil = session('hasil_prediksi');
        if (!$hasil) {
            return redirect()->route('prediksi.index')->with('error', 'Silakan lakukan saran terlebih dahulu.');
        }
        return view('prediksi.detail', compact('hasil'));
    }

    /* ==============================
     *  RIWAYAT
     * ============================== */
    public function riwayat(Request $request)
    {
        $query = HasilPrediksi::with('produk')
            ->orderBy('tanggal', 'desc')
            ->orderBy('id_hasil_prediksi', 'desc');

        if ($request->filled('id_produk')) {
            $query->where('id_produk', $request->id_produk);
        }
        if ($request->filled('from')) {
            $query->whereDate('tanggal', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('tanggal', '<=', $request->to);
        }

        $riwayat = $query->paginate(15)->withQueryString();
        $produk  = Produk::orderBy('nama_produk')->get();

        return view('prediksi.riwayat', compact('riwayat', 'produk'));
    }

    /* ==============================
     *  DETAIL BY ID
     * ============================== */
    public function detailById($id)
    {
        $prediksi = HasilPrediksi::with(['produk', 'produk.kategori', 'detail'])->findOrFail($id);

        if (!$prediksi->detail) {
            $produk   = $prediksi->produk;
            $kategori = $produk->kategori;

            $input = [
                'tanggal'            => $prediksi->tanggal,
                'penjualan'          => (float) $prediksi->penjualan,
                'waktu_produksi'     => (float) $prediksi->waktu_produksi,
                'stok_barang_jadi'   => (float) $prediksi->stok_barang_jadi,
                'kapasitas_produksi' => (float) $prediksi->kapasitas_produksi,
            ];

            $snap = $this->computeFuzzy($produk->id_produk, $kategori, $input);

            $hasil = [
                'produk'      => $produk,
                'input'       => $input,
                'minmax'      => $snap['minmax'],
                'mu'          => $snap['mu'],
                'rules'       => $snap['rules'],
                'z_akhir'     => $snap['z_akhir'],
                'z_bulat'     => (int) $prediksi->jumlah_produksi,
                'sum_alpha'   => $snap['sum_alpha'],
                'sum_alpha_z' => $snap['sum_alpha_z'],
            ];

            return view('prediksi.detail', compact('hasil'));
        }

        $hasil = [
            'produk'      => $prediksi->produk,
            'input'       => [
                'tanggal'            => $prediksi->tanggal,
                'penjualan'          => (float) $prediksi->penjualan,
                'waktu_produksi'     => (float) $prediksi->waktu_produksi,
                'stok_barang_jadi'   => (float) $prediksi->stok_barang_jadi,
                'kapasitas_produksi' => (float) $prediksi->kapasitas_produksi,
            ],
            'minmax'      => $prediksi->detail->detail_minmax,
            'mu'          => $prediksi->detail->detail_mu,
            'rules'       => $prediksi->detail->detail_rules,
            'sum_alpha'   => $prediksi->detail->detail_sum_alpha ?? 0,
            'sum_alpha_z' => $prediksi->detail->detail_sum_alpha_z ?? 0,
            'z_akhir'     => $prediksi->detail->detail_z_akhir ?? 0,
            'z_bulat'     => (int) $prediksi->jumlah_produksi,
        ];

        return view('prediksi.detail', compact('hasil'));
    }

    /* ==============================
     *  EDIT
     * ============================== */
    public function edit($id)
    {
        $prediksi = HasilPrediksi::with('produk.kategori')->findOrFail($id);
        if (auth()->user()->level != 0) abort(403);

        $stokSnapshot = (float) $prediksi->stok_barang_jadi;
        return view('prediksi.edit', compact('prediksi', 'stokSnapshot'));
    }

    /* ==============================
     *  UPDATE
     * ============================== */
    public function update(Request $request, $id)
    {
        $prediksi = HasilPrediksi::with(['produk.kategori'])->findOrFail($id);
        if (auth()->user()->level != 0) abort(403);

        $request->validate(
            [
                'penjualan'          => 'required|numeric|min:0',
                'waktu_produksi'     => 'required|numeric|min:0',
                'kapasitas_produksi' => 'required|numeric|min:0',
            ],
            [
                'penjualan.min'          => 'Angka tidak boleh kurang dr 0',
                'waktu_produksi.min'     => 'Angka tidak boleh kurang dr 0',
                'kapasitas_produksi.min' => 'Angka tidak boleh kurang dr 0',
            ]
        );

        $produk   = $prediksi->produk;
        $kategori = $produk->kategori;

        $stokSnapshot = (float) $prediksi->stok_barang_jadi;

        $input = [
            'tanggal'            => $prediksi->tanggal,
            'penjualan'          => (float) $request->penjualan,
            'waktu_produksi'     => (float) $request->waktu_produksi,
            'stok_barang_jadi'   => $stokSnapshot,
            'kapasitas_produksi' => (float) $request->kapasitas_produksi,
        ];

        $snap = $this->computeFuzzy($produk->id_produk, $kategori, $input);

        DB::transaction(function () use ($prediksi, $input, $snap) {
            $prediksi->update([
                'penjualan'          => $input['penjualan'],
                'waktu_produksi'     => $input['waktu_produksi'],
                'stok_barang_jadi'   => $input['stok_barang_jadi'],
                'kapasitas_produksi' => $input['kapasitas_produksi'],
                'jumlah_produksi'    => $snap['z_bulat'],
            ]);

            HasilPrediksiDetail::updateOrCreate(
                ['id_hasil_prediksi' => $prediksi->id_hasil_prediksi],
                [
                    'detail_minmax'      => $snap['minmax'],
                    'detail_mu'          => $snap['mu'],
                    'detail_rules'       => $snap['rules'],
                    'detail_sum_alpha'   => $snap['sum_alpha'],
                    'detail_sum_alpha_z' => $snap['sum_alpha_z'],
                    'detail_z_akhir'     => $snap['z_akhir'],
                ]
            );
        });

        return redirect()->route('prediksi.riwayat')
            ->with('success', 'Data riwayat berhasil diupdate.');
    }

    /* =========================================================
     *  CORE: HITUNG FUZZY
     * ========================================================= */
    private function computeFuzzy(int $idProduk, $kategori, array $input): array
    {
        // MIN–MAX dari training
        $q = DataTraining::where('id_produk', $idProduk);

        $penjualanMin = (clone $q)->where('penjualan', '>', 0)->min('penjualan');
        $penjualanMax = (clone $q)->where('penjualan', '>', 0)->max('penjualan');

        $stokMin = (clone $q)->where('stok_barang_jadi', '>', 0)->min('stok_barang_jadi');
        $stokMax = (clone $q)->where('stok_barang_jadi', '>', 0)->max('stok_barang_jadi');

        $produksiMin = (clone $q)->where('hasil_produksi', '>', 0)->min('hasil_produksi');
        $produksiMax = (clone $q)->where('hasil_produksi', '>', 0)->max('hasil_produksi');

        // fallback aman
        if (is_null($penjualanMin) || is_null($penjualanMax) || $penjualanMax <= $penjualanMin) {
            $penjualanMin = 0; $penjualanMax = 1;
        }
        if (is_null($stokMin) || is_null($stokMax) || $stokMax <= $stokMin) {
            $stokMin = 0; $stokMax = 1;
        }
        if (is_null($produksiMin) || is_null($produksiMax) || $produksiMax <= $produksiMin) {
            $produksiMin = 0; $produksiMax = 1;
        }

        // max output dibatasi kapasitas input
        $produksiMaxEfektif = min((float) $produksiMax, (float) $input['kapasitas_produksi']);
        if ($produksiMaxEfektif <= (float) $produksiMin) $produksiMaxEfektif = (float) $produksiMin + 1;

        $minmax = [
            'penjualan' => ['min' => (float) $penjualanMin, 'max' => (float) $penjualanMax],
            'waktu'     => ['min' => (float) ($kategori->waktu_min ?? 0), 'max' => (float) ($kategori->waktu_max ?? 1)],
            'stok'      => ['min' => (float) $stokMin, 'max' => (float) $stokMax],
            'kapasitas' => ['min' => (float) ($kategori->kapasitas_min ?? 0), 'max' => (float) ($kategori->kapasitas_max ?? 1)],
            'produksi'  => ['min' => (float) $produksiMin, 'max' => (float) $produksiMaxEfektif],
        ];

        // μ
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

        // RULE BASE
        $rulesBase = [
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

        $sumAlpha = 0.0;
        $sumAlphaZ = 0.0;
        $detailRules = [];

        $minOut = $minmax['produksi']['min'];
        $maxOut = $minmax['produksi']['max'];
        $rangeOut = max($maxOut - $minOut, 0.0);

        foreach ($rulesBase as $r) {
            $alpha = min(
                $mu['penjualan'][$r['P']],
                $mu['waktu'][$r['W']],
                $mu['stok'][$r['S']],
                $mu['kapasitas'][$r['K']]
            );

            $z = ($r['out'] === 'banyak')
                ? $minOut + $alpha * $rangeOut
                : $maxOut - $alpha * $rangeOut;

            $detailRules[] = [
                'kode'  => $r['kode'],
                'P'     => $r['P'],
                'W'     => $r['W'],
                'S'     => $r['S'],
                'K'     => $r['K'],
                'out'   => $r['out'],
                'alpha' => $alpha,
                'z'     => $z,
            ];

            $sumAlpha  += $alpha;
            $sumAlphaZ += $alpha * $z;
        }

        $zAkhir = $sumAlpha > 0 ? ($sumAlphaZ / $sumAlpha) : 0.0;
        $zBulat = (int) round($zAkhir);
        if ($zBulat < 0) $zBulat = 0;

        return [
            'minmax'      => $minmax,
            'mu'          => $mu,
            'rules'       => $detailRules,
            'sum_alpha'   => $sumAlpha,
            'sum_alpha_z' => $sumAlphaZ,
            'z_akhir'     => $zAkhir,
            'z_bulat'     => $zBulat,
        ];
    }

    /* ==============================
     *  MEMBERSHIP
     * ============================== */
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
