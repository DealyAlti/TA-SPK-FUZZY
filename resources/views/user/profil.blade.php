@extends('layouts.master')

@section('title')
    Profile Pengguna
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Profile Pengguna</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-body">
                <div class="form-group row">
                    <label class="col-lg-2 control-label">Nama</label>
                    <div class="col-lg-6">
                        <p class="form-control-static">{{ $user->name }}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 control-label">Email</label>
                    <div class="col-lg-6">
                        <p class="form-control-static">{{ $user->email }}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 control-label">Hak Akses</label>
                    <div class="col-lg-6">
                        <p class="form-control-static">{{ $user->role_name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
