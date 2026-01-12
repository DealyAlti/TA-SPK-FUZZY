@extends('layouts.master')

@section('title','Keputusan Produksi')

@section('breadcrumb')
@parent
<li class="active">Keputusan Produksi</li>
@endsection

@push('css')
<style>
    .bg-soft { background:#f3f4f6 !important; }
    .is-invalid { border:1px solid #dc2626 !important; box-shadow:none !important; }
    .hint { font-size:12px; color:#6b7280; }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fa fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fa fa-warning"></i> {{ $errors->first() }}
            </div>
        @endif

        <div class="box">
            <div class="box-header with-border">
                <div class="row" style="margin-bottom:10px;">
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('keputusan.index') }}" class="form-inline">
                            <div class="form-group">
                                <label for="tanggal">Tanggal</label>
                                <input type="date" name="tanggal" id="tanggal" class="form-control"
                                       value="{{ $tanggal }}">
                            </div>
                            <button class="btn btn-default btn-flat" type="submit">
                                <i class="fa fa-search"></i> Tampilkan
                            </button>
                        </form>
                    </div>
                    <div class="col-md-6 text-right">
                        <span class="label label-info" style="font-size:12px;">
                            Server: {{ $now->format('H:i:s') }} WIB
                        </span>
                        <span class="hint" style="margin-left:10px;">
                            Batas update/kirim: {{ $limit->format('d-m-Y H:i') }} WIB
                        </span>
                        @if($locked)
                            <span class="label label-danger" style="margin-left:10px;">Terkunci</span>
                        @else
                            <span class="label label-success" style="margin-left:10px;">Masih bisa update</span>
                        @endif
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('keputusan.kirim') }}" id="formKeputusan">
                @csrf
                <input type="hidden" name="tanggal" value="{{ $tanggal }}">

                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead style="background:#b91c1c;color:#fff;">
                            <tr>
                                <th width="5%">No</th>
                                <th>Produk</th>
                                <th width="16%" class="text-center">Hasil Saran</th>
                                <th width="10%" class="text-center">Pakai</th>
                                <th width="24%" class="text-center">Keputusan</th>
                                <th width="20%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($saran as $i => $hp)
                            @php
                                $k = $keputusanMap[$hp->id_produk] ?? null;

                                $saranVal = (float) $hp->jumlah_produksi;

                                // default: kalau belum ada keputusan -> pakai saran
                                $pakai = $k ? (bool) $k->pakai_saran : true;

                                // nilai keputusan: kalau ada keputusan manual -> tampilkan nilai manualnya
                                if ($k) {
                                    $keputusanVal = $pakai ? (int)$saranVal : (int)$k->jumlah_keputusan;
                                } else {
                                    $keputusanVal = $pakai ? (int)$saranVal : '';
                                }

                                // flag untuk JS: apakah awalnya ini manual?
                                $initialManual = ($k && !$k->pakai_saran) ? 1 : 0;
                            @endphp

                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $hp->produk->nama_produk ?? '-' }}</td>

                                <td class="text-center">
                                    <span class="label label-primary" style="font-size:12px;">
                                        {{ number_format($saranVal, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <input type="checkbox"
                                           class="js-pakai-saran"
                                           name="rows[{{ $hp->id_produk }}][pakai_saran]"
                                           value="1"
                                           {{ $pakai ? 'checked' : '' }}
                                           data-saran="{{ (int)$saranVal }}"
                                           data-target="keputusan-{{ $hp->id_produk }}"
                                           {{ $locked ? 'disabled' : '' }}>
                                </td>

                                <td>
                                    <input type="number" min="0" step="1"
                                           class="form-control js-keputusan"
                                           id="keputusan-{{ $hp->id_produk }}"
                                           name="rows[{{ $hp->id_produk }}][jumlah_keputusan]"
                                           value="{{ $keputusanVal }}"
                                           placeholder="Isi keputusan..."
                                           data-initial-manual="{{ $initialManual }}"
                                           {{ $locked ? 'readonly' : '' }}>
                                    <small class="hint">Jika checkbox tidak dicentang, keputusan wajib diisi.</small>
                                </td>

                                <td>
                                    @if($k)
                                        <span class="label label-success">Sudah dikirim</span>
                                        <span class="text-muted">
                                            (update terakhir: {{ $k->diputuskan_pada ? $k->diputuskan_pada->format('d-m H:i') : '-' }})
                                        </span>
                                    @else
                                        <span class="label label-default">Belum</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    Tidak ada hasil saran pada tanggal {{ $tanggal }}.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="box-footer">
                    <button type="submit"
                            class="btn btn-danger btn-flat"
                            {{ ($saran->count() && !$locked) ? '' : 'disabled' }}>
                        <i class="fa fa-send"></i>
                        {{ $locked ? 'Terkunci' : 'Simpan / Update' }}
                    </button>
                </div>
            </form>

        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    const locked = {{ $locked ? 'true' : 'false' }};

    // apply state tanpa merusak nilai manual yang sudah ada saat load awal
    function applyState(cb, isInit=false){
        const targetId = cb.getAttribute('data-target');
        const saran    = cb.getAttribute('data-saran');
        const input    = document.getElementById(targetId);
        if(!input) return;

        if(cb.checked){
            // centang -> isi saran + readonly
            input.value = parseInt(saran || 0);
            input.setAttribute('readonly', 'readonly');
            input.classList.add('bg-soft');
            input.classList.remove('is-invalid');
        } else {
            // tidak centang:
            // - saat INIT: kalau sudah ada manual tersimpan, JANGAN dikosongkan
            // - saat CHANGE (toggle): kosongkan biar user isi manual baru
            if(!isInit){
                input.value = '';
                input.classList.remove('is-invalid');
                input.focus();
            }

            input.removeAttribute('readonly');
            input.classList.remove('bg-soft');
        }

        if(locked){
            input.setAttribute('readonly', 'readonly');
        }
    }

    // init
    document.querySelectorAll('.js-pakai-saran').forEach(cb => {
        applyState(cb, true);
        cb.addEventListener('change', function(){
            if(locked) return;
            applyState(this, false);
        });
    });

    // validasi sebelum submit: unchecked wajib isi
    document.getElementById('formKeputusan').addEventListener('submit', function(e){
        if(locked){
            e.preventDefault();
            alert('Form terkunci karena sudah lewat batas jam 09:00.');
            return;
        }

        let ok = true;
        document.querySelectorAll('.js-pakai-saran').forEach(cb => {
            const input = document.getElementById(cb.getAttribute('data-target'));
            if(!input) return;

            if(!cb.checked){
                const val = (input.value || '').trim();
                if(val === ''){
                    ok = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            } else {
                input.classList.remove('is-invalid');
            }
        });

        if(!ok){
            e.preventDefault();
            alert('Ada keputusan yang belum diisi (checkbox tidak dicentang).');
        }
    });

})();
</script>
@endpush
