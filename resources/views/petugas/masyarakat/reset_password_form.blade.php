@extends('layouts.app')

@section('title', 'Reset Password Akun Masyarakat')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Reset Password untuk: {{ $masyarakat->nama_lengkap }}</h1>
        <a href="{{ route('petugas.masyarakat.show', $masyarakat->id) }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Detail Akun
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-key mr-2"></i>Masukkan Password Baru</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Anda akan mengubah password untuk akun dengan NIK <strong>{{ $masyarakat->nik }}</strong>. Pengguna harus menggunakan password baru ini untuk login selanjutnya.</p>
                    <hr>
                    <form action="{{ route('petugas.masyarakat.resetPasswordByPetugas', $masyarakat->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="password">Password Baru <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Minimal 8 karakter.</small>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Reset Password</button>
                        <a href="{{ route('petugas.masyarakat.show', $masyarakat->id) }}" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
