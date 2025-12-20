<?php

namespace App\Http\Controllers;

use App\Models\DataTraining;
use App\Models\Produk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DataTrainingTemplateExport;

class DataTrainingController extends Controller
{
    public function index()
    {
        $produk = Produk::orderBy('nama_produk')->get();
        return view('data_training.index', compact('produk'));
    }

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
                $del = route('training.destroy', $row->id_data_training);

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

    public function destroy($id)
    {
        $data   = DataTraining::findOrFail($id);
        $produk = Produk::findOrFail($data->id_produk);

        $data->delete();

        $latest = DataTraining::where('id_produk', $produk->id_produk)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id_data_training', 'desc')
            ->first();

        $produk->stok = $latest ? $latest->stok_barang_jadi : 0;
        $produk->save();

        return response(null, 204);
    }

    public function exportTemplate($id_produk)
    {
        $produk = Produk::findOrFail($id_produk);
        $fileName = 'template_data_training_'.$produk->nama_produk.'.xlsx';

        return Excel::download(new DataTrainingTemplateExport($produk), $fileName);
    }

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

        // validasi header template (baris 0 biasanya "PRODUCT: ... ID: ...")
        if (!isset($rows[0][0]) || stripos($rows[0][0], 'PRODUCT:') === false) {
            return back()->with('error', 'Template tidak valid atau sudah diubah.');
        }

        preg_match('/ID:\s*(\d+)/', $rows[0][0], $match);
        $templateProductId = $match[1] ?? null;

        if ($templateProductId != $produk->id_produk) {
            return back()->with('error', 'Template ini bukan untuk produk: '.$produk->nama_produk);
        }

        $inserted = 0;
        $updated  = 0;
        $skipped  = 0;

        // untuk info tanggal di alert (ambil tanggal terakhir yang berhasil diproses)
        $tanggalInfo = null;

        foreach ($rows as $index => $row) {
            if ($index < 2) continue; // skip 2 baris awal (judul+kolom)

            // skip baris kosong
            $allEmpty =
                (!isset($row[0]) || $row[0] === null || $row[0] === '') &&
                (!isset($row[1]) || $row[1] === null || $row[1] === '') &&
                (!isset($row[2]) || $row[2] === null || $row[2] === '') &&
                (!isset($row[3]) || $row[3] === null || $row[3] === '');

            if ($allEmpty) continue;

            $rawTanggal     = $row[0] ?? null;
            $penjualan      = (int)($row[1] ?? 0);
            $stokBarangJadi = (int)($row[2] ?? 0);
            $hasilProduksi  = (int)($row[3] ?? 0);

            $tanggal = $this->normalizeExcelDate($rawTanggal);
            if (!$tanggal) {
                $skipped++;
                continue;
            }

            $tanggalInfo = $tanggal;

            // cek existing
            $exists = DataTraining::where('id_produk', $produk->id_produk)
                ->whereDate('tanggal', $tanggal)
                ->exists();

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

            if ($exists) $updated++;
            else $inserted++;
        }

        // update stok produk ke stok terakhir dari data training
        $latest = DataTraining::where('id_produk', $produk->id_produk)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id_data_training', 'desc')
            ->first();

        $produk->stok = $latest ? $latest->stok_barang_jadi : 0;
        $produk->save();

        if (($inserted + $updated) === 0) {
            return back()->with('info', 'Tidak ada data yang diimport (semua baris kosong / format tanggal tidak terbaca).');
        }

        return back()->with([
            'import_success' => true,
            'import_tanggal' => $tanggalInfo, // tanggal terakhir yang diproses
            'import_inserted' => $inserted,
            'import_updated'  => $updated,
            'import_skipped'  => $skipped,
        ]);
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
