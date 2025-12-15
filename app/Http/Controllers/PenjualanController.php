<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Penjualan;
use App\Models\StokHarian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

        // ✅ CEK DUPLIKAT DULU (sebelum transaksi)
        $idsInput = collect($request->produk)
            ->filter(fn($r) => (float)($r['jumlah'] ?? 0) > 0)
            ->pluck('id_produk')
            ->unique()
            ->values();

        if ($idsInput->isNotEmpty()) {
            $dupes = Penjualan::whereDate('tanggal', $request->tanggal)
                ->whereIn('id_produk', $idsInput)
                ->pluck('id_produk')
                ->toArray();

            if (!empty($dupes)) {
                $namaDupes = Produk::whereIn('id_produk', $dupes)
                    ->orderBy('nama_produk')
                    ->pluck('nama_produk')
                    ->implode(', ');

                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'duplicate' => "Penjualan tanggal {$request->tanggal} sudah ada untuk produk: {$namaDupes}."
                    ]);
            }
        }

        // ✅ kalau aman, baru transaksi
        DB::transaction(function () use ($request) {

            foreach ($request->produk as $row) {

                $jumlah = (float) ($row['jumlah'] ?? 0);
                if ($jumlah <= 0) continue;

                $produk = Produk::lockForUpdate()
                    ->where('id_produk', $row['id_produk'])
                    ->firstOrFail();

                $stokSebelum = (float)$produk->stok;

                Penjualan::create([
                    'tanggal'   => $request->tanggal,
                    'id_produk' => $produk->id_produk,
                    'jumlah'    => $jumlah,
                ]);

                $produk->stok = max(0, $stokSebelum - $jumlah);
                $produk->save();

                StokHarian::updateOrCreate(
                    ['id_produk' => $produk->id_produk, 'tanggal' => $request->tanggal],
                    [
                        'stok_awal'  => StokHarian::where('id_produk',$produk->id_produk)
                                        ->whereDate('tanggal',$request->tanggal)
                                        ->value('stok_awal') ?? $stokSebelum,
                        'stok_akhir' => $produk->stok,
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
        // summary per tanggal
        $query = DB::table('penjualan')
            ->selectRaw('DATE(tanggal) as tanggal, SUM(jumlah) as total_terjual, COUNT(*) as total_item')
            ->groupBy(DB::raw('DATE(tanggal)'))
            ->orderBy(DB::raw('DATE(tanggal)'), 'desc');

        // filter tanggal (opsional)
        if ($request->filled('from')) {
            $query->whereDate('tanggal', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('tanggal', '<=', $request->to);
        }

        $riwayat = $query->paginate(15)->appends($request->query());

        return view('penjualan.riwayat', compact('riwayat'));
    }

    public function detail($tanggal)
    {
        // amankan format tanggal (YYYY-MM-DD)
        try {
            $tanggal = Carbon::parse($tanggal)->format('Y-m-d');
        } catch (\Exception $e) {
            abort(404);
        }

        $list = Penjualan::query()
            ->with('produk')
            ->leftJoin('stok_harian as sh', function ($join) {
                $join->on('sh.id_produk', '=', 'penjualan.id_produk')
                    ->on('sh.tanggal',  '=', 'penjualan.tanggal');
            })
            ->select('penjualan.*', 'sh.stok_akhir as stok_saat_itu')
            ->whereDate('penjualan.tanggal', $tanggal)
            ->orderBy('penjualan.id_penjualan', 'asc')
            ->get();

        return view('penjualan.detail', [
            'tanggal' => $tanggal,
            'list'    => $list,
        ]);
    }

}
