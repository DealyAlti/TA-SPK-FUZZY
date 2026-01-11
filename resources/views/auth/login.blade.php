@extends('layouts.auth')

@section('login')
<div class="login-container-flex">

    {{-- BAGIAN MERAH KIRI – FULL HEIGHT --}}
    <div class="left-red-side">
        <div class="left-content-box">
            <h4 class="pt-short">PT APN</h4>
            <h2 class="pt-name">PT Anugerah Pelangi Nusantara</h2>
            <p class="pt-sub">Sistem Pendukung Keputusan Jumlah Produksi</p>
        </div>
    </div>

    {{-- BAGIAN LOGIN PUTIH --}}
    <div class="right-login-box">
        <div class="login-inner">

            <h3 class="login-title">Sign In</h3>
            <p class="login-subtitle">Masuk menggunakan akun yang telah terdaftar.</p>

            {{-- ERROR UMUM (misal email/password salah) --}}
            @if ($errors->has('email') && !old('email'))
                {{-- biarkan saja, biasanya dari validasi required --}}
            @endif

            @if(session('error'))
                <div class="alert alert-danger modern-alert" style="margin-bottom:16px;">
                    <i class="fa fa-times-circle"></i> {{ session('error') }}
                </div>
            @endif

            {{-- Error login bawaan (misal credentials salah) kadang masuknya ke errors->first('email') --}}
            @if ($errors->any() && !$errors->has('email') && !$errors->has('password'))
                <div class="alert alert-danger modern-alert" style="margin-bottom:16px;">
                    <i class="fa fa-times-circle"></i> {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="post" novalidate>
                @csrf

                {{-- EMAIL --}}
                <div class="form-group @error('email') has-error @enderror">
                    <label>Email</label>
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="Masukkan email"
                        value="{{ old('email') }}"
                        required
                        oninvalid="this.setCustomValidity('Email tidak boleh kosong')"
                        oninput="this.setCustomValidity('')"
                    >
                    @error('email')
                        <span class="help-block text-danger">{{ $message }}</span>
                    @enderror
                </div>

                {{-- PASSWORD --}}
                <div class="form-group @error('password') has-error @enderror">
                    <label>Password</label>
                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="Masukkan password"
                        required
                        oninvalid="this.setCustomValidity('Password tidak boleh kosong')"
                        oninput="this.setCustomValidity('')"
                    >
                    @error('password')
                        <span class="help-block text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-login-red">Login</button>
            </form>

        </div>
    </div>

</div>

<style>
    :root {
        --apn-red: #d82323;
        --apn-red-dark: #b81818;
    }

    /* LAYOUT UTAMA */
    body {
        background: #ffffff !important;
    }

    .login-container-flex {
        display: flex;
        height: 100vh;
        width: 100%;
        overflow: hidden;
    }

    /* BAGIAN KIRI (MERAH FULL) */
    .left-red-side {
        flex: 1.1;
        background:
            linear-gradient(rgba(200, 0, 0, 0.55), rgba(200, 0, 0, 0.55)),
            url('{{ asset('images/login.png') }}');
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;

        display: flex;
        align-items: center;
        padding: 50px;
        color: white;
    }

    .left-content-box {
        max-width: 450px;
    }

    .pt-short {
        font-size: 14px;
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-bottom: 8px;
        opacity: 0.85;
    }

    .pt-name {
        font-size: 32px;
        font-weight: 800;
        margin: 0 0 10px;
        line-height: 1.3;
    }

    .pt-sub {
        font-size: 15px;
        opacity: 0.9;
        margin-top: 10px;
    }

    /* BAGIAN KANAN (KOTAK LOGIN) */
    .right-login-box {
        flex: 1;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
    }

    .login-inner {
        width: 100%;
        max-width: 360px;
    }

    .login-title {
        font-size: 28px;
        font-weight: 700;
        color: #0f6b37;
        margin-bottom: 10px;
    }

    .login-subtitle {
        font-size: 14px;
        color: #666;
        margin-bottom: 25px;
    }

    /* Alert style */
    .modern-alert {
        border-radius: 10px;
        padding: 12px 14px;
        font-size: 14px;
    }

    /* Input */
    .form-group label {
        font-size: 13px;
        font-weight: 600;
        color: #444;
        margin-bottom: 6px;
    }

    .form-control {
        height: 42px;
        border-radius: 8px;
        box-shadow: none;
        border: 1px solid #ddd;
    }

    .has-error .form-control {
        border-color: #d82323 !important;
    }

    .help-block {
        margin-top: 6px;
        font-size: 12px;
    }

    /* Tombol Login */
    .btn-login-red {
        width: 100%;
        height: 46px;
        border-radius: 999px;
        background: var(--apn-red);
        border: none;
        color: #fff;
        font-size: 16px;
        font-weight: 600;
        margin-top: 18px;
        box-shadow: 0 10px 25px rgba(216,35,35,0.5);
        transition: 0.2s ease;
    }

    .btn-login-red:hover {
        background: var(--apn-red-dark);
        box-shadow: 0 14px 32px rgba(184,24,24,0.6);
    }

    /* Responsif */
    @media (max-width: 850px) {
        .login-container-flex {
            flex-direction: column;
        }
        .left-red-side {
            flex: none;
            height: 220px;
            padding: 28px;
        }
        .pt-name {
            font-size: 24px;
        }
    }
</style>
@endsection
