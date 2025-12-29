<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <section class="sidebar">

        <ul class="sidebar-menu" data-widget="tree">

            <!-- DASHBOARD (SEMUA ROLE) -->
            <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}">
                    <i class="fa fa-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>


            {{-- ================= OWNER (LEVEL 0) ================= --}}
            @if(auth()->user()->level == 0)

                <li class="header">DATA MASTER</li>

                <li class="{{ request()->routeIs('kategori.*') ? 'active' : '' }}">
                    <a href="{{ route('kategori.index') }}">
                        <i class="fa fa-list"></i>
                        <span>Kategori</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('produk.*') ? 'active' : '' }}">
                    <a href="{{ route('produk.index') }}">
                        <i class="fa fa-shopping-bag"></i>
                        <span>Produk</span>
                    </a>
                </li>

                {{-- DATA TRAINING (dropdown) --}}
                <li class="treeview {{ request()->routeIs('training.*') || request()->routeIs('training.harian.*') ? 'active menu-open' : '' }}">
                    <a href="#">
                        <i class="fa fa-database"></i>
                        <span>Data Training</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>

                    <ul class="treeview-menu">

                        <li class="{{ request()->routeIs('training.index','training.data','training.destroy','training.template','training.import') ? 'active' : '' }}">
                            <a href="{{ route('training.index') }}">
                                <i class="fa fa-circle-o"></i> Data Training
                            </a>
                        </li>

                        <li class="{{ request()->routeIs('training.harian.*') ? 'active' : '' }}">
                            <a href="{{ route('training.harian.index') }}">
                                <i class="fa fa-circle-o"></i> Training Harian
                            </a>
                        </li>

                    </ul>
                </li>


                <li class="header">FUZZY TSUKAMOTO</li>

                <li class="{{ request()->routeIs('prediksi.index','prediksi.hitung') ? 'active' : '' }}">
                    <a href="{{ route('prediksi.index') }}">
                        <i class="fa fa-line-chart"></i>
                        <span>Saran Produksi</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('prediksi.riwayat*','prediksi.detail*') ? 'active' : '' }}">
                    <a href="{{ route('prediksi.riwayat') }}">
                        <i class="fa fa-check-circle"></i>
                        <span>Riwayat</span>
                    </a>
                </li>


                <li class="header">USER</li>

                <li class="{{ request()->routeIs('user.*') ? 'active' : '' }}">
                    <a href="{{ route('user.index') }}">
                        <i class="fa fa-user"></i>
                        <span>Pengguna</span>
                    </a>
                </li>



            {{-- ================= KEPALA PRODUKSI (LEVEL 1) ================= --}}
            @elseif(auth()->user()->level == 1)

                <li class="header">FUZZY TSUKAMOTO</li>

                <li class="{{ request()->routeIs('prediksi.riwayat*','prediksi.detail*','prediksi.hasil') ? 'active' : '' }}">
                    <a href="{{ route('prediksi.riwayat') }}">
                        <i class="fa fa-check-circle"></i>
                        <span>Riwayat Perhitungan</span>
                    </a>
                </li>



            {{-- ================= ADMIN (LEVEL 2) ================= --}}
            @elseif(auth()->user()->level == 2)

                <li class="header">DATA TRAINING</li>

                <li class="{{ request()->routeIs('training.harian.*') ? 'active' : '' }}">
                    <a href="{{ route('training.harian.index') }}">
                        <i class="fa fa-database"></i>
                        <span>Training Harian</span>
                    </a>
                </li>

            @endif

        </ul>

    </section>
</aside>
