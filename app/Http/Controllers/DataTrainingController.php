<?php

namespace App\Http\Controllers;

use App\Models\DataTraining;
use App\Models\Produk;
use App\Models\Penjualan;
use App\Models\HasilPrediksi;
use App\Models\StokHarian;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DataTrainingTemplateExport;
use Illuminate\Support\Facades\DB;

class DataTrainingController extends Controller
{
    /**
     * Halaman utama: pilih produk + tabel data training.
     */
    public function index()
    {
        $produk = Produk::orderBy('nama_produk')->get();

        return view('data_training.index', compact('produk'));
    }

    /**
     * DataTables untuk 1 produk tertentu.
     * route: training.data
     */
    public function data($id_produk)
    {
        $training = DataTraining::where('id_produk', $id_produk)
            ->orderBy('tanggal', 'desc')
            ->get();

        return DataTables::of($training)
            ->addIndexColumn()
            ->editColumn('tanggal', function ($row) {
                return Carbon::parse($row->tanggal)->format('d-m-Y');
            })
            ->addColumn('stok_akhir', function ($row) {
                return $row->stok_barang_jadi;
            })
            ->addColumn('aksi', function ($row) {
                // HANYA HAPUS
                $del  = route('training.destroy', $row->id_data_training);

                return '
                    <div class="btn-group">
                        <button type="button" onclick="deleteData(`'.$del.'`)" 
                                class="btn btn-xs btn-danger btn-flat">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    /**
     * Simpan data training harian (input manual).
     * stok_akhir dihitung otomatis dari stok sebelumnya.
     * route: training.store (POST)
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_produk'      => 'required|exists:produk,id_produk',
            'tanggal'        => 'required|date',
            'penjualan'      => 'required|integer|min:0',
            'hasil_produksi' => 'required|integer|min:0',
        ]);

        // Cek duplikat per produk + tanggal
        $exists = DataTraining::where('id_produk', $request->id_produk)
            ->where('tanggal', $request->tanggal)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Data training untuk tanggal ini sudah ada.'
            ], 422);
        }

        $produk = Produk::findOrFail($request->id_produk);

        // Cari stok sebelumnya:
        // - jika sudah ada data training: pakai stok_barang_jadi terakhir <= tanggal ini
        // - kalau belum ada: pakai stok realtime di tabel produk (stok awal dari import)
        $lastTraining = DataTraining::where('id_produk', $request->id_produk)
            ->where('tanggal', '<=', $request->tanggal)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id_data_training', 'desc')
            ->first();

        if ($lastTraining) {
            $stokSebelumnya = (int) $lastTraining->stok_barang_jadi;
        } else {
            $stokSebelumnya = (int) ($produk->stok ?? 0);
        }

        $penjualan     = (int) $request->penjualan;
        $hasilProduksi = (int) $request->hasil_produksi;

        $stokAkhir = $stokSebelumnya - $penjualan + $hasilProduksi;
        if ($stokAkhir < 0) $stokAkhir = 0;

        $data = DataTraining::create([
            'id_produk'        => $request->id_produk,
            'tanggal'          => $request->tanggal,
            'penjualan'        => $penjualan,
            'hasil_produksi'   => $hasilProduksi,
            'stok_barang_jadi' => $stokAkhir,
        ]);

        // Update stok realtime produk => stok akhir terakhir
        $latest = DataTraining::where('id_produk', $request->id_produk)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id_data_training', 'desc')
            ->first();

        if ($latest) {
            $produk->stok = $latest->stok_barang_jadi;
            $produk->save();
        }

        return response()->json([
            'message' => 'Data training berhasil disimpan',
            'data'    => $data,
        ], 200);
    }

    /**
     * Tampilkan 1 baris data training (untuk edit).
     * route: training.show (kalau dipakai) – di js kamu pakai langsung route update untuk GET,
     * tapi kalau mau bisa arahkan ini.
     */
    public function show($id)
    {
        $data = DataTraining::findOrFail($id);

        return response()->json($data);
    }

    /**
     * Update data training harian.
     * stok_akhir dihitung ulang.
     * route: training.update (PUT)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal'        => 'required|date',
            'penjualan'      => 'required|integer|min:0',
            'hasil_produksi' => 'required|integer|min:0',
        ]);

        $data   = DataTraining::findOrFail($id);
        $produk = Produk::findOrFail($data->id_produk);

        // stok sebelumnya = stok akhir record sebelum tanggal ini
        $last = DataTraining::where('id_produk', $data->id_produk)
            ->where('tanggal', '<', $request->tanggal)
            ->where('id_data_training', '!=', $id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id_data_training', 'desc')
            ->first();

        if ($last) {
            $stokSebelumnya = (int) $last->stok_barang_jadi;
        } else {
            $stokSebelumnya = (int) ($produk->stok ?? 0);
        }

        $penjualan     = (int) $request->penjualan;
        $hasilProduksi = (int) $request->hasil_produksi;

        $stokAkhir = $stokSebelumnya - $penjualan + $hasilProduksi;
        if ($stokAkhir < 0) $stokAkhir = 0;

        $data->update([
            'tanggal'          => $request->tanggal,
            'penjualan'        => $penjualan,
            'hasil_produksi'   => $hasilProduksi,
            'stok_barang_jadi' => $stokAkhir,
        ]);

        // kalau ini record paling terakhir → update stok produk
        $latest = DataTraining::where('id_produk', $data->id_produk)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id_data_training', 'desc')
            ->first();

        if ($latest && $latest->id_data_training == $data->id_data_training) {
            $produk->stok = $stokAkhir;
            $produk->save();
        }

        return response()->json('Data training berhasil diupdate', 200);
    }

    /**
     * Hapus data training.
     * Kalau yang dihapus adalah baris terakhir → stok produk diset ke stok_akhir sebelumnya.
     * route: training.destroy (DELETE)
     */
    public function destroy($id)
    {
        $data   = DataTraining::findOrFail($id);
        $produk = Produk::findOrFail($data->id_produk);

        $data->delete();

        // setelah hapus, cari data training terakhir untuk update stok produk
        $latest = DataTraining::where('id_produk', $produk->id_produk)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id_data_training', 'desc')
            ->first();

        $produk->stok = $latest ? $latest->stok_barang_jadi : 0;
        $produk->save();

        return response(null, 204);
    }

    /**
     * EXPORT TEMPLATE – terkunci ke produk yang dipilih.
     * route: training.template (GET)
     */
    public function exportTemplate($id_produk)
    {
        $produk = Produk::findOrFail($id_produk);
        $fileName = 'template_data_training_'.$produk->nama_produk.'.xlsx';

        return Excel::download(new DataTrainingTemplateExport($produk), $fileName);
    }

    /**
     * IMPORT DATA TRAINING dari Excel.
     * Template harus sesuai produk (dicek dari header baris pertama).
     * route: training.import (POST)
     */
    public function import(Request $request)
    {
        $request->validate([
            'id_produk' => 'required|exists:produk,id_produk',
            'file'      => 'required|mimes:xlsx,xls,csv',
        ]);

        $produk = Produk::findOrFail($request->id_produk);

        $sheets = Excel::toArray([], $request->file('file'));
        $rows   = $sheets[0] ?? [];

        if (count($rows) <= 2) {
            return back()->with('error', 'File kosong atau format tidak sesuai.');
        }

        // ===== 🔒 CEK PRODUK DI TEMPLATE (HEADER BARIS 0) =====
        // Contoh header baris 0:
        // "PRODUCT: Mawar KM (ID: 1)"
        if (!isset($rows[0][0]) || stripos($rows[0][0], 'PRODUCT:') === false) {
            return back()->with('error', 'Template tidak valid atau sudah diubah.');
        }

        preg_match('/ID:\s*(\d+)/', $rows[0][0], $match);
        $templateProductId = $match[1] ?? null;

        if ($templateProductId != $produk->id_produk) {
            return back()->with('error', 'Template ini bukan untuk produk: '.$produk->nama_produk);
        }
        // =====================================================

        // Mulai dari baris ke-2 (index 1 adalah heading: tanggal, penjualan, stok, hasil_produksi)
        foreach ($rows as $index => $row) {
            if ($index < 2) continue; // skip header & headings

            // kalau baris kosong, lewati
            if (
                (!isset($row[0]) || $row[0] === null || $row[0] === '') &&
                (!isset($row[1]) || $row[1] === null || $row[1] === '') &&
                (!isset($row[2]) || $row[2] === null || $row[2] === '') &&
                (!isset($row[3]) || $row[3] === null || $row[3] === '')
            ) {
                continue;
            }

            $rawTanggal      = $row[0] ?? null;
            $penjualan       = (int)($row[1] ?? 0);
            $stokBarangJadi  = (int)($row[2] ?? 0);
            $hasilProduksi   = (int)($row[3] ?? 0);

            $tanggal = $this->normalizeExcelDate($rawTanggal);
            if (!$tanggal) {
                // kalau tanggal gagal diparsing, lewati/bariskan pesan
                continue;
            }

            DataTraining::updateOrCreate(
                [
                    'id_produk' => $produk->id_produk,
                    'tanggal'   => $tanggal,
                ],
                [
                    'penjualan'        => $penjualan,
                    'hasil_produksi'   => $hasilProduksi,
                    'stok_barang_jadi' => $stokBarangJadi,
                ]
            );
        }

        // set stok realtime produk = stok_akhir terakhir
        $latest = DataTraining::where('id_produk', $produk->id_produk)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id_data_training', 'desc')
            ->first();

        $produk->stok = $latest ? $latest->stok_barang_jadi : 0;
        $produk->save();

        return back()->with('success', 'Data training berhasil diimport.');
    }

    /**
     * Helper: normalisasi nilai tanggal dari Excel (serial number / string) -> 'Y-m-d'.
     */
    protected function normalizeExcelDate($value): ?string
    {
        // Jika sudah DateTime
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->format('Y-m-d');
        }

        // Jika numeric (serial Excel, mis: 45474)
        if (is_numeric($value)) {
            // Excel serial: hari sejak 1899-12-30
            $base = Carbon::createFromDate(1899, 12, 30);
            return $base->copy()->addDays((int)$value)->format('Y-m-d');
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

    public function generateHarian(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
        ]);

        $tanggal = $request->tanggal;

        DB::transaction(function () use ($tanggal) {

            // loop semua produk (atau kalau mau, bisa filter id_produk)
            $produkList = Produk::orderBy('nama_produk')->get();

            foreach ($produkList as $prd) {

                // 1) total penjualan hari itu
                $totalJual = Penjualan::where('tanggal', $tanggal)
                    ->where('id_produk', $prd->id_produk)
                    ->sum('jumlah'); // kalau gak ada, hasilnya 0

                // 2) ambil produksi aktual hari itu (kalau ada)
                // kalau ada banyak prediksi dalam 1 hari, ambil yang terbaru
                $prediksi = HasilPrediksi::where('tanggal', $tanggal)
                    ->where('id_produk', $prd->id_produk)
                    ->orderBy('id_hasil_prediksi', 'desc')
                    ->first();

                $waktuProduksi = $prediksi ? (float)$prediksi->waktu_produksi : 0;
                $kapasitas     = $prediksi ? (float)$prediksi->kapasitas_produksi : 0;

                // hasil produksi = hasil_aktual kalau ada, kalau tidak 0 (karena kamu mau sementara 0)
                $hasilProduksi = 0;
                if ($prediksi && !is_null($prediksi->hasil_aktual)) {
                    $hasilProduksi = (float)$prediksi->hasil_aktual;
                }

                // 3) stok barang jadi hari itu ambil dari stok_harian (stok akhir)
                $stokAkhir = StokHarian::where('tanggal', $tanggal)
                    ->where('id_produk', $prd->id_produk)
                    ->value('stok_akhir');

                // fallback kalau belum ada record stok harian
                if (is_null($stokAkhir)) {
                    $stokAkhir = (float)$prd->stok;
                }

                // 4) simpan ke data training
                DataTraining::updateOrCreate(
                    [
                        'id_produk' => $prd->id_produk,
                        'tanggal'   => $tanggal,
                    ],
                    [
                        'penjualan'          => (float)$totalJual,
                        'waktu_produksi'     => (float)$waktuProduksi,
                        'stok_barang_jadi'   => (float)$stokAkhir,
                        'kapasitas_produksi' => (float)$kapasitas,
                        'hasil_produksi'     => (float)$hasilProduksi,
                    ]
                );
            }
        });

        return back()->with('success', 'Data training harian berhasil dibuat untuk tanggal ' . $tanggal);
    }
}
