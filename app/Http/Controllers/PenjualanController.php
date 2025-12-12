<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Penjualan;
use App\Models\StokHarian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    /**
     * FORM INPUT (tambah penjualan)
     */
    public function index()
    {
        $produk = Produk::orderBy('nama_produk')->get();

        return view('penjualan.index', compact('produk'));
    }

    /**
     * SIMPAN PENJUALAN (bisa banyak produk)
     * - simpan ke tabel penjualan
     * - kurangi stok produk
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'produk'  => 'required|array',
            'produk.*.id_produk' => 'required|exists:produk,id_produk',
            'produk.*.jumlah'    => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {

            foreach ($request->produk as $row) {

                $jumlah = (float) ($row['jumlah'] ?? 0);

                // kalau kosong / 0, skip
                if ($jumlah <= 0) continue;

                $produk = Produk::lockForUpdate()
                    ->where('id_produk', $row['id_produk'])
                    ->firstOrFail();

                // simpan riwayat penjualan
                Penjualan::create([
                    'tanggal'   => $request->tanggal,
                    'id_produk' => $produk->id_produk,
                    'jumlah'    => $jumlah,
                ]);

                // kurangi stok produk (stok tidak boleh minus)
                $produk->stok = max(0, (float)$produk->stok - $jumlah);
                $produk->save();
                
                StokHarian::updateOrCreate(
                [
                    'id_produk' => $produk->id_produk,
                    'tanggal'   => $request->tanggal,
                ],
                [
                    // stok_awal cuma diisi sekali kalau record baru
                    'stok_awal'  => DB::raw('stok_awal'), // biar gak ke-reset kalau sudah ada
                    'stok_akhir' => $produk->stok,        // snapshot stok setelah transaksi
                ]
            );

            }
        });

        return redirect()->route('penjualan.riwayat')
            ->with('success', 'Penjualan berhasil disimpan & stok produk otomatis berkurang.');
    }

    /**
     * RIWAYAT PENJUALAN
        */
    public function riwayat(Request $request)
    {
        $query = Penjualan::query()
            ->with('produk')
            ->leftJoin('stok_harian as sh', function ($join) {
                $join->on('sh.id_produk', '=', 'penjualan.id_produk')
                    ->on('sh.tanggal',  '=', 'penjualan.tanggal');
            })
            ->select('penjualan.*', 'sh.stok_akhir as stok_saat_itu')
            ->orderBy('penjualan.tanggal', 'desc')
            ->orderBy('penjualan.id_penjualan', 'desc');

        if ($request->filled('id_produk')) {
            $query->where('penjualan.id_produk', $request->id_produk);
        }

        if ($request->filled('from')) {
            $query->whereDate('penjualan.tanggal', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('penjualan.tanggal', '<=', $request->to);
        }

        $riwayat = $query->paginate(15)->appends($request->query());
        $produk  = Produk::orderBy('nama_produk')->get();

        return view('penjualan.riwayat', compact('riwayat', 'produk'));
    }

}
