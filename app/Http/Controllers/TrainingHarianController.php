<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\DataTraining;
use App\Models\TrainingHarian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TrainingHarianTemplateExport;

class TrainingHarianController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->get('tanggal', Carbon::today()->format('Y-m-d'));

        $produk = Produk::orderBy('nama_produk')->get();

        $draft = TrainingHarian::with('produk')
            ->whereDate('tanggal', $tanggal)
            ->orderBy('id_produk', 'asc')
            ->get();

        // draft map (key by id_produk)
        $draftMap = $draft->keyBy('id_produk');

        // cek apakah sudah pernah digenerate ke data_training
        $sudahGenerate = DataTraining::whereDate('tanggal', $tanggal)->exists();

        /**
         * ===== STOK AWAL (STOK TERAKHIR SEBELUM TANGGAL TERPILIH) =====
         * Tujuan: stok di tabel index TIDAK berubah walau draft di-import.
         * stokAwal = stok_barang_jadi terakhir di DataTraining sebelum tanggal tsb,
         * kalau belum ada -> fallback ke produk.stok.
         */
        $stokAwalMap = [];

        $lastBefore = DataTraining::select('id_produk', 'stok_barang_jadi', 'tanggal', 'id_data_training')
            ->where('tanggal', '<', $tanggal)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id_data_training', 'desc')
            ->get()
            ->unique('id_produk')
            ->keyBy('id_produk');

        foreach ($produk as $p) {
            $stokAwalMap[$p->id_produk] = $lastBefore->has($p->id_produk)
                ? (int) $lastBefore[$p->id_produk]->stok_barang_jadi
                : (int) ($p->stok ?? 0);
        }

        return view('training_harian.index', compact(
            'tanggal',
            'produk',
            'draftMap',
            'sudahGenerate',
            'stokAwalMap'
        ));
    }

    /**
     * Export 1 template berisi semua produk + tanggal
     * Format kolom template:
     * A: Produk
     * B: Stok Terakhir (KG) [LOCKED]
     * C: Penjualan (KG)     [INPUT]
     * D: Hasil Produksi(KG) [INPUT]
     * E: ID_PRODUK (hidden)
     */
    public function exportTemplate(Request $request)
    {
        $tanggal = $request->get('tanggal', Carbon::today()->format('Y-m-d'));

        return Excel::download(
            new TrainingHarianTemplateExport($tanggal),
            "template_training_harian_{$tanggal}.xlsx"
        );
    }

    /**
     * Import template (updateOrCreate per tanggal+produk)
     * - Row 0: Tanggal (B1)
     * - Row 1: Panduan
     * - Row 2: Header
     * - Row 3..: Data
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            $sheets = Excel::toArray([], $request->file('file'));
        } catch (\Exception $e) {
            return back()->with('error', 'File tidak dapat dibaca. Pastikan format Excel / CSV valid.');
        }

        $rows = $sheets[0] ?? [];

        // minimal 4 baris: row0(tanggal), row1(panduan), row2(header), row3(data)
        if (count($rows) < 4) {
            return back()->with('error', 'File kosong atau format tidak sesuai template.');
        }

        /**
         * ====== VALIDASI HEADER (baris ke-3 = index 2) ======
         * Ekspektasi:
         *  A: Produk
         *  B: Stok Terakhir (KG)
         *  C: Penjualan (KG)
         *  D: Hasil Produksi (KG)
         *  E: ID_PRODUK (hidden)
         */
        $header = $rows[2] ?? [];

        $colProduk    = strtolower(trim((string)($header[0] ?? '')));
        $colStok      = strtolower(trim((string)($header[1] ?? '')));
        $colPenjualan = strtolower(trim((string)($header[2] ?? '')));
        $colHasil     = strtolower(trim((string)($header[3] ?? '')));

        $headerOk =
            (strpos($colProduk, 'produk') !== false) &&
            (strpos($colStok, 'stok') !== false) &&
            (strpos($colPenjualan, 'penjualan') !== false) &&
            (strpos($colHasil, 'hasil') !== false);

        if (! $headerOk) {
            return back()->with('error', 'Header kolom tidak sesuai template.');
        }

        // Tanggal ada di B1 (row 0 col 1)
        $rawTanggal = $rows[0][1] ?? null;
        $tanggal = $this->normalizeExcelDate($rawTanggal);

        if (!$tanggal) {
            return back()->with('error', 'Tanggal belum diisi / format salah.');
        }

        // jika sudah digenerate -> kunci (biar draft tidak berubah)
        $sudahGenerate = DataTraining::whereDate('tanggal', $tanggal)->exists();
        if ($sudahGenerate) {
            return back()->with('error', "Tanggal {$tanggal} sudah pernah di-generate ke Data Training. Tidak bisa import ulang.");
        }

        // ====== VALIDASI ISI ANGKA (UNIVERSAL ERROR) ======

        $validRows         = [];
        $totalBarisData    = 0;
        $hasInvalidNumber  = false;

        $toString = function ($val): string {
            if (is_null($val)) return '';
            if (is_int($val) || is_float($val)) {
                if (floor($val) == $val) return (string) (int) $val;
                return (string) $val;
            }
            return trim((string) $val);
        };

        // 0 atau bilangan bulat positif tanpa leading zero
        $isNonNegativeInt = function (string $v): bool {
            return (bool) preg_match('/^(0|[1-9][0-9]*)$/', $v);
        };

        // Data mulai row 3 (index 3)
        for ($i = 3; $i < count($rows); $i++) {

            $namaProduk = $toString($rows[$i][0] ?? '');
            // $stokS = $toString($rows[$i][1] ?? ''); // readonly (abaikan)
            $penjualanS = $toString($rows[$i][2] ?? ''); // kolom C
            $hasilS     = $toString($rows[$i][3] ?? ''); // kolom D
            $idProdukFromFile = $rows[$i][4] ?? null;     // kolom E (hidden)

            // kalau semua kosong -> skip
            $isSemuaKosong =
                ($namaProduk === '') &&
                ($penjualanS === '') &&
                ($hasilS === '') &&
                (empty($idProdukFromFile));

            if ($isSemuaKosong) {
                continue;
            }

            $totalBarisData++;

            // JIKA KOSONG → JADI 0
            if ($penjualanS === '') $penjualanS = '0';
            if ($hasilS === '')     $hasilS     = '0';

            // VALIDASI ANGKA
            if (! $isNonNegativeInt($penjualanS) || ! $isNonNegativeInt($hasilS)) {
                $hasInvalidNumber = true;
                break;
            }

            $validRows[] = [
                'nama'        => $namaProduk,
                'penjualan_s' => $penjualanS,
                'hasil_s'     => $hasilS,
                'id_file'     => $idProdukFromFile,
            ];
        }

        if ($hasInvalidNumber) {
            return back()->with('error', 'Format angka tidak valid. Isi Penjualan & Hasil Produksi dengan angka bulat (KG), tanpa koma/titik.');
        }

        if ($totalBarisData === 0 || empty($validRows)) {
            return back()->with('error', 'Tidak ada baris data yang bisa diproses. Pastikan Penjualan & Hasil Produksi sudah diisi (minimal 0).');
        }

        // ====== MASUK DB ======

        $jumlahDipakai       = 0;
        $jumlahProdukUnknown = 0;

        DB::transaction(function () use ($validRows, $tanggal, &$jumlahDipakai, &$jumlahProdukUnknown) {

            foreach ($validRows as $r) {

                $namaProduk       = $r['nama'];
                $penjualan        = (int) $r['penjualan_s'];
                $hasil            = (int) $r['hasil_s'];
                $idProdukFromFile = $r['id_file'];

                $produk = null;

                // 1) cari dari ID_PRODUK (hidden)
                if (!empty($idProdukFromFile)) {
                    $produk = Produk::where('id_produk', (int) $idProdukFromFile)->first();
                }

                // 2) fallback by nama
                if (!$produk && $namaProduk !== '') {
                    $produk = Produk::where('nama_produk', $namaProduk)->first();
                }

                if (!$produk) {
                    $jumlahProdukUnknown++;
                    continue;
                }

                TrainingHarian::updateOrCreate(
                    [
                        'tanggal'   => $tanggal,
                        'id_produk' => $produk->id_produk,
                    ],
                    [
                        'penjualan'      => max(0, $penjualan),
                        'hasil_produksi' => max(0, $hasil),
                    ]
                );

                $jumlahDipakai++;
            }
        });

        if ($jumlahDipakai === 0) {
            if ($jumlahProdukUnknown > 0) {
                return back()->with('error', 'Tidak ada data yang bisa disimpan karena produk pada file tidak cocok dengan master produk.');
            }

            return back()->with('error', 'Tidak ada baris data yang berhasil disimpan.');
        }

        $message  = "Import training harian berhasil untuk tanggal {$tanggal}.\n";
        $message .= "Data tersimpan / ter-update: {$jumlahDipakai} produk.";

        if ($jumlahProdukUnknown > 0) {
            $message .= "\nSebagian baris dilewati karena nama/ID produk tidak ditemukan di master.";
        }

        return redirect()
            ->route('training.harian.index', ['tanggal' => $tanggal])
            ->with('success', $message);
    }

    /**
     * Generate draft -> data_training + update stok produk.
     * Kunci: sekali generate per tanggal (biar aman).
     */
    public function generate(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
        ]);

        $tanggal = $request->tanggal;

        // kalau sudah pernah generate -> stop
        $sudahGenerate = DataTraining::whereDate('tanggal', $tanggal)->exists();
        if ($sudahGenerate) {
            return back()->with('info', "Tanggal {$tanggal} sudah pernah di-generate.");
        }

        $draft = TrainingHarian::whereDate('tanggal', $tanggal)->get();
        if ($draft->count() === 0) {
            return back()->with('error', "Belum ada draft training harian untuk tanggal {$tanggal}. Import dulu templatenya.");
        }

        // cek apakah ada minimal 1 draft yang valid (tidak 0 & 0)
        $adaValid = $draft->contains(function ($d) {
            return ((int)$d->penjualan > 0) || ((int)$d->hasil_produksi > 0);
        });

        if (!$adaValid) {
            return back()->with('error', "Semua draft tanggal {$tanggal} bernilai 0 (penjualan=0 & hasil=0). Tidak ada yang bisa di-generate.");
        }

        /**
         * =========================
         * VALIDASI STOK: penjualan tidak boleh > (stok_awal + hasil)
         * stok_awal = stok terakhir sebelum tanggal (data_training) atau fallback produk.stok
         * =========================
         */
        $invalidLines = [];

        // ambil stok terakhir sebelum tanggal untuk semua produk (1x query)
        $lastBefore = DataTraining::select('id_produk', 'stok_barang_jadi', 'tanggal', 'id_data_training')
            ->where('tanggal', '<', $tanggal)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id_data_training', 'desc')
            ->get()
            ->unique('id_produk')
            ->keyBy('id_produk');

        // ambil master produk yang terlibat (biar ada nama)
        $produkMap = Produk::whereIn('id_produk', $draft->pluck('id_produk')->unique()->values())
            ->get()
            ->keyBy('id_produk');

        foreach ($draft as $d) {

            $penjualan = (int) $d->penjualan;
            $hasil     = (int) $d->hasil_produksi;

            // kalau 0 & 0 -> nanti diskip oleh proses insert (biarin lewat)
            if ($penjualan <= 0 && $hasil <= 0) {
                continue;
            }

            $p = $produkMap[$d->id_produk] ?? null;
            $namaProduk = $p ? $p->nama_produk : ('ID ' . $d->id_produk);

            $stokAwal = $lastBefore->has($d->id_produk)
                ? (int) $lastBefore[$d->id_produk]->stok_barang_jadi
                : (int) (($p && isset($p->stok)) ? $p->stok : 0);

            $maxBisaJual = $stokAwal + $hasil;

            if ($penjualan > $maxBisaJual) {
                $invalidLines[] = "{$namaProduk} (Stok Awal {$stokAwal} + Produksi {$hasil} = {$maxBisaJual}, Penjualan {$penjualan})";
            }
        }

        if (!empty($invalidLines)) {
            return back()->with(
                'error',
                'Generate gagal. Stok tidak cukup untuk memenuhi penjualan pada tanggal tersebut. 
                Periksa kembali penjualan atau hasil produksi.'
            );
        }

        // =========================
        // LANJUT GENERATE (SAMA SEPERTI PUNYAMU)
        // =========================
        $inserted = 0;
        $skipped  = 0;

        DB::transaction(function () use ($draft, $tanggal, &$inserted, &$skipped) {

            foreach ($draft as $d) {

                $penjualan = (int) $d->penjualan;
                $hasil     = (int) $d->hasil_produksi;

                if ($penjualan <= 0 && $hasil <= 0) {
                    $skipped++;
                    continue;
                }

                $produk = Produk::lockForUpdate()->where('id_produk', $d->id_produk)->first();

                $lastTraining = DataTraining::where('id_produk', $produk->id_produk)
                    ->where('tanggal', '<', $tanggal)
                    ->orderBy('tanggal', 'desc')
                    ->orderBy('id_data_training', 'desc')
                    ->first();

                $stokSebelumnya = $lastTraining
                    ? (int) $lastTraining->stok_barang_jadi
                    : (int) ($produk->stok ?? 0);

                $stokAkhir = $stokSebelumnya - $penjualan + $hasil;
                if ($stokAkhir < 0) $stokAkhir = 0;

                DataTraining::create([
                    'id_produk'        => $produk->id_produk,
                    'tanggal'          => $tanggal,
                    'penjualan'        => max(0, $penjualan),
                    'hasil_produksi'   => max(0, $hasil),
                    'stok_barang_jadi' => $stokAkhir,
                ]);

                $produk->stok = $stokAkhir;
                $produk->save();

                $inserted++;
            }
        });

        return redirect()
            ->route('training.harian.index', ['tanggal' => $tanggal])
            ->with('success', "Generate berhasil ({$tanggal}). Masuk Data Training: {$inserted} produk.");
    }

    protected function normalizeExcelDate($value): ?string
    {
        // Jika sudah DateTime (dari Excel)
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->format('Y-m-d');
        }

        // Jika numeric (serial date Excel)
        if (is_numeric($value)) {
            // Excel base date: 1899-12-30
            $base = Carbon::createFromDate(1899, 12, 30);
            return $base->copy()->addDays((int) $value)->format('Y-m-d');
        }

        // Jika string tanggal
        if (is_string($value) && trim($value) !== '') {
            try {
                return Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }


}
