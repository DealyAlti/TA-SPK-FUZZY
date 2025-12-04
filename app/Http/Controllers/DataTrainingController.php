<?php

namespace App\Http\Controllers;

use App\Models\DataTraining;
use App\Models\Produk;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class DataTrainingController extends Controller
{
    public function index()
    {
        $produk = Produk::orderBy('nama_produk')->get();

        return view('data_training.index', compact('produk'));
    }

    /**
     * DataTables untuk 1 produk tertentu
     */
public function data($id_produk)
    {
        // ambil semua data training untuk produk ini
        $training = DataTraining::where('id_produk', $id_produk)
            ->orderBy('tanggal', 'desc')
            ->get();

        return datatables()
            ->of($training)
            ->addIndexColumn()
            ->editColumn('tanggal', function ($row) {
                // kalau mau sederhana saja
                return date('d-m-Y', strtotime($row->tanggal));
            })
            ->addColumn('stok_akhir', function ($row) {
                return $row->stok_barang_jadi;
            })
            ->addColumn('aksi', function ($row) {
                $edit = route('training.update', $row->id_data_training);
                $del  = route('training.destroy', $row->id_data_training);

                return '
                    <div class="btn-group">
                        <button type="button" onclick="editForm(`'.$edit.'`)" 
                                class="btn btn-xs btn-info btn-flat">
                            <i class="fa fa-pencil"></i>
                        </button>
                        <button type="button" onclick="deleteData(`'.$del.'`)" 
                                class="btn btn-xs btn-danger btn-flat">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);   // <<< penting: sama seperti controller lain
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_produk'       => 'required|exists:produk,id_produk',
            'tanggal'         => 'required|date',
            'penjualan'       => 'required|integer|min:0',
            'hasil_produksi'  => 'required|integer|min:0',
        ]);

        // cari stok akhir sebelumnya
        $last = DataTraining::where('id_produk', $request->id_produk)
            ->where('tanggal', '<=', $request->tanggal)
            ->orderBy('tanggal', 'desc')
            ->first();
        $stokAkhir = (int) $request->stok_barang_jadi;
        $data = DataTraining::create([
            'id_produk'        => $request->id_produk,
            'tanggal'          => $request->tanggal,
            'penjualan'        => $request->penjualan,
            'hasil_produksi'   => $request->hasil_produksi,
            'stok_barang_jadi' => $stokAkhir,
        ]);
        return response()->json([
            'message' => 'Data training berhasil disimpan',
            'data'    => $data,
        ]);
    }

    public function show($id)
    {
        $data = DataTraining::findOrFail($id);

        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal'         => 'required|date',
            'penjualan'       => 'required|integer|min:0',
            'hasil_produksi'  => 'required|integer|min:0',
        ]);

        $data = DataTraining::findOrFail($id);

        // stok sebelumnya = stok akhir record sebelum tanggal ini
        $last = DataTraining::where('id_produk', $data->id_produk)
            ->where('tanggal', '<=', $request->tanggal)
            ->where('id_data_training', '!=', $id)
            ->orderBy('tanggal', 'desc')
            ->first();

        $stokSebelumnya = $last->stok_barang_jadi ?? 0;

        $stokAkhir = $stokSebelumnya - (int) $request->penjualan + (int) $request->hasil_produksi;
        if ($stokAkhir < 0) $stokAkhir = 0;

        $data->update([
            'tanggal'          => $request->tanggal,
            'penjualan'        => $request->penjualan,
            'hasil_produksi'   => $request->hasil_produksi,
            'stok_barang_jadi' => (int) $request->stok_barang_jadi,
        ]);


        return response()->json('Data training berhasil diupdate', 200);
    }

    public function destroy($id)
    {
        $data = DataTraining::findOrFail($id);
        $data->delete();

        return response(null, 204);
    }
}
