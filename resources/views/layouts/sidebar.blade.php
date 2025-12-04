<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <section class="sidebar">

        <ul class="sidebar-menu" data-widget="tree">

            <!-- DASHBOARD -->
            <li>
                <a href="{{ route('dashboard') }}">
                    <i class="fa fa-dashboard"></i>
                    <span> Dashboard</span>
                </a>
            </li>

            <li class="header">DATA</li>

            <!-- Kategori (hanya Owner / Level 0) -->
            @if (auth()->user()->level == 0)
                <li>
                    <a href="{{ route('kategori.index') }}">
                        <i class="fa fa-list"></i>
                        <span> Kategori</span>
                    </a>
                </li>
            @endif

            <!-- Produk (semua level) -->
            <li>
                <a href="{{ route('produk.index') }}">
                    <i class="fa fa-shopping-bag"></i>
                    <span> Produk</span>
                </a>
            </li>

            <!-- DATA TRAINING -->
            <li>
                <a href="{{ route('training.index') }}">
                    <i class="fa fa-database"></i>
                    <span> Data Training</span>
                </a>
            </li>

            <li class="header">FUZZY TSUKAMOTO</li>

            <!-- Prediksi -->
            <li>
                <a href="{{ route('prediksi.index') }}">
                    <i class="fa fa-line-chart"></i>
                    <span> Prediksi</span>
                </a>
            </li>

            <!-- Hasil -->
            <li>
                <a href="#">
                    <i class="fa fa-check-circle"></i>
                    <span> Hasil</span>
                </a>
            </li>

            <li>
                <a href="#">
                    <i class="fa fa-money"></i>
                    <span> Penjualan</span>
                </a>
            </li>

            <!-- Pengguna (hanya Owner) -->
            @if (auth()->user()->level == 0)
                <li class="header">USER</li>
                <li>
                    <a href="{{ route('user.index') }}">
                        <i class="fa fa-user"></i>
                        <span> Pengguna</span>
                    </a>
                </li>
            @endif

        </ul>

    </section>
</aside>
