@extends('layouts.app')

@section('title', 'Reset Password Akun Masyarakat')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Reset Password untuk: {{ $masyarakat->nama_lengkap }} (NIK: {{ $masyarakat->nik }})</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Masukkan Password Baru</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('petugas.masyarakat.resetPasswordByPetugas', $masyarakat->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="password">Password Baru <span class="text-danger">*</span></label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Minimal 8 karakter, mengandung huruf besar, huruf kecil, angka, dan simbol.</small>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                </div>

                <button type="submit" class="btn btn-primary">Reset Password</button>
                <a href="{{ route('petugas.masyarakat.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection
