<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index()
    {
        $kategori = Kategori::all();
        return view('produk.index', compact('kategori'));
    }

    public function data()
    {
        $produk = Produk::leftJoin('kategori', 'kategori.id_kategori', 'produk.id_kategori')
            ->select('produk.*', 'nama_kategori')
            ->get();

        return datatables()
            ->of($produk)
            ->addIndexColumn()
            ->addColumn('kode_produk', function ($produk) {
                return '<span class="label label-success">'. $produk->kode_produk .'</span>';
            })
            ->addColumn('aksi', function ($produk) {
                return '
                    <div class="btn-group">
                        <button onclick="editForm(`'. route('produk.update', $produk->id_produk) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                        <button onclick="deleteData(`'. route('produk.destroy', $produk->id_produk) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                    </div>
                ';
            })
            ->rawColumns(['aksi', 'kode_produk'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required',
            'id_kategori' => 'required',
            'stok'        => 'nullable|integer|min:0',
        ]);

        $exists = Produk::where('nama_produk', $request->nama_produk)
                        ->where('id_kategori', $request->id_kategori)
                        ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Nama Produk dengan kategori ini sudah digunakan'
            ], 422);
        }

        // Generate kode produk
        $produkTerakhir = Produk::latest('id_produk')->first();
        $request['kode_produk'] = 'K' . tambah_nol_didepan(($produkTerakhir->id_produk ?? 0) + 1, 6);

        $request['stok'] = $request->stok ?? 0;

        Produk::create($request->all());

        return response()->json('Data berhasil disimpan', 200);
    }

    public function show($id)
    {
        return Produk::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_produk' => 'required',
            'id_kategori' => 'required',
            'stok'        => 'nullable|integer|min:0',
        ]);

        $exists = Produk::where('nama_produk', $request->nama_produk)
                        ->where('id_kategori', $request->id_kategori)
                        ->where('id_produk', '!=', $id)
                        ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Nama Produk dengan kategori ini sudah digunakan'
            ], 422);
        }

        $produk = Produk::findOrFail($id);
        $produk->update($request->all());

        return response()->json('Data berhasil disimpan', 200);
    }

    public function destroy($id)
    {
        Produk::findOrFail($id)->delete();
        return response(null, 204);
    }

}
