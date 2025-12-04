<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;

class DashboardController extends Controller
{
    public function index()
    {
        $kategori = Kategori::count();
        $produk   = Produk::count();

        return view('admin.dashboard', compact('kategori', 'produk'));
    }
}
