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

        // map draft biar gampang dipakai di blade (key by id_produk)
        $draftMap = $draft->keyBy('id_produk');

        // cek apakah sudah pernah digenerate ke data_training
        $sudahGenerate = DataTraining::whereDate('tanggal', $tanggal)->exists();

        return view('training_harian.index', compact(
            'tanggal',
            'produk',
            'draftMap',
            'sudahGenerate'
        ));
    }

    /**
     * Export 1 template berisi semua produk + tanggal di B1
     */
    public function exportTemplate(Request $request)
    {
        // tanggal optional, kalau kosong default hari ini
        $tanggal = $request->get('tanggal', Carbon::today()->format('Y-m-d'));

        return Excel::download(
            new TrainingHarianTemplateExport($tanggal),
            'template_training_harian.xlsx'
        );
    }

    /**
     * Import template (updateOrCreate per tanggal+produk)
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $sheets = Excel::toArray([], $request->file('file'));
        $rows   = $sheets[0] ?? [];

        if (count($rows) < 3) {
            return back()->with('error', 'File kosong / format tidak sesuai template.');
        }

        // Tanggal ada di B1 (row 0 col 1)
        $rawTanggal = $rows[0][1] ?? null;
        $tanggal = $this->normalizeExcelDate($rawTanggal);

        if (!$tanggal) {
            return back()->with('error', 'Tanggal belum diisi / format salah. Isi tanggal di kolom B baris 1.');
        }

        // jika sudah digenerate -> kunci (biar draft tidak berubah)
        $sudahGenerate = DataTraining::whereDate('tanggal', $tanggal)->exists();
        if ($sudahGenerate) {
            return back()->with('error', "Tanggal {$tanggal} sudah pernah di-generate ke Data Training. Tidak bisa import ulang.");
        }

        DB::transaction(function () use ($rows, $tanggal) {

            // data mulai baris ke-3 (index 2)
            for ($i = 2; $i < count($rows); $i++) {

                $namaProduk = trim((string)($rows[$i][0] ?? ''));
                $penjualan  = (int)($rows[$i][1] ?? 0);
                $hasil      = (int)($rows[$i][2] ?? 0);

                // kolom hidden ID_PRODUK (index 3) kalau ada
                $idProdukFromFile = $rows[$i][3] ?? null;

                if ($namaProduk === '' && empty($idProdukFromFile)) {
                    continue;
                }

                $produk = null;

                if (!empty($idProdukFromFile)) {
                    $produk = Produk::where('id_produk', (int)$idProdukFromFile)->first();
                }

                // fallback by nama
                if (!$produk && $namaProduk !== '') {
                    $produk = Produk::where('nama_produk', $namaProduk)->first();
                }

                if (!$produk) continue;

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
            }
        });

        return redirect()
            ->route('training.harian.index', ['tanggal' => $tanggal])
            ->with('success', "Import berhasil untuk tanggal {$tanggal}. (Jika sudah ada, otomatis UPDATE)");
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

        $inserted = 0;
        $skipped  = 0;

        DB::transaction(function () use ($draft, $tanggal, &$inserted, &$skipped) {

            foreach ($draft as $d) {

                $penjualan = (int) $d->penjualan;
                $hasil     = (int) $d->hasil_produksi;

                // ✅ RULE: kalau 0 & 0 => skip, tidak masuk data_training, stok tidak berubah
                if ($penjualan <= 0 && $hasil <= 0) {
                    $skipped++;
                    continue;
                }

                $produk = Produk::lockForUpdate()->where('id_produk', $d->id_produk)->first();

                // stok sebelumnya:
                // - ambil stok akhir terakhir di data_training sebelum tanggal ini
                // - kalau tidak ada, pakai stok realtime produk saat ini
                $lastTraining = DataTraining::where('id_produk', $produk->id_produk)
                    ->where('tanggal', '<', $tanggal)
                    ->orderBy('tanggal', 'desc')
                    ->orderBy('id_data_training', 'desc')
                    ->first();

                $stokSebelumnya = $lastTraining
                    ? (int) $lastTraining->stok_barang_jadi
                    : (int) ($produk->stok ?? 0);

                // stok akhir = stok sebelum - jual + hasil
                $stokAkhir = $stokSebelumnya - $penjualan + $hasil;
                if ($stokAkhir < 0) $stokAkhir = 0;

                // create data_training (1 baris per produk per tanggal)
                DataTraining::create([
                    'id_produk'        => $produk->id_produk,
                    'tanggal'          => $tanggal,
                    'penjualan'        => max(0, $penjualan),
                    'hasil_produksi'   => max(0, $hasil),
                    'stok_barang_jadi' => $stokAkhir,
                ]);

                // update stok realtime produk mengikuti stok akhir tanggal tsb
                $produk->stok = $stokAkhir;
                $produk->save();

                $inserted++;
            }
        });

        return redirect()
            ->route('training.harian.index', ['tanggal' => $tanggal])
            ->with(
                'success',
                "Generate berhasil ({$tanggal}). Masuk Data Training: {$inserted} produk. Dilewati (0 & 0): {$skipped} produk."
            );
    }


    protected function normalizeExcelDate($value): ?string
    {
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->format('Y-m-d');
        }

        if (is_numeric($value)) {
            $base = Carbon::createFromDate(1899, 12, 30);
            return $base->copy()->addDays((int)$value)->format('Y-m-d');
        }

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
