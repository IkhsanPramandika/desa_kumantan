{{-- Lokasi: resources/views/petugas/profile/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Profil Akun</h1>

{{-- Notifikasi Sukses --}}
@if (session('status') === 'profile-updated')
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Sukses!</strong> Informasi profil Anda telah berhasil diperbarui.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
@elseif (session('status') === 'password-updated')
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Sukses!</strong> Password Anda telah berhasil diubah.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
@endif

<div class="row">
    {{-- KOLOM KIRI: FOTO PROFIL & INFO DASAR --}}
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                <img class="img-profile rounded-circle mb-3" src="{{ asset('sbadmin/img/undraw_profile.svg') }}" style="max-width: 150px;">
                <h5 class="font-weight-bold">{{ $user->name }}</h5>
                <p class="text-muted">{{ $user->email }}</p>
                <span class="badge badge-primary">{{ ucfirst($user->role) }}</span>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: FORM EDIT DENGAN TAB --}}
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true">Edit Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="password-tab" data-toggle="tab" href="#password" role="tab" aria-controls="password" aria-selected="false">Ubah Password</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="myTabContent">
                    
                    {{-- TAB 1: EDIT PROFIL --}}
                    <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <h6 class="font-weight-bold text-primary mb-3">Informasi Akun</h6>
                        <form method="POST" action="{{ route('petugas.profile.update') }}">
                            @csrf
                            @method('PATCH')

                            <div class="form-group">
                                <label for="name">Nama Lengkap</label>
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name">
                                @error('name')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="email">Alamat Email</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email">
                                @error('email')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </form>
                    </div>

                    {{-- TAB 2: UBAH PASSWORD --}}
                    <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                        <h6 class="font-weight-bold text-primary mb-3">Ubah Password</h6>
                        <form method="POST" action="{{ route('petugas.profile.password.update') }}">
                            @csrf
                            @method('PUT')

                            {{-- Password Saat Ini --}}
                            <div class="form-group">
                                <label for="current_password">Password Saat Ini</label>
                                <div class="input-group">
                                    <input id="current_password" type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" name="current_password" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary toggle-password" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('current_password', 'updatePassword')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            {{-- Password Baru --}}
                            <div class="form-group">
                                <label for="password">Password Baru</label>
                                <div class="input-group">
                                    <input id="password" type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" name="password" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary toggle-password" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('password', 'updatePassword')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            {{-- Konfirmasi Password Baru --}}
                            <div class="form-group">
                                <label for="password_confirmation">Konfirmasi Password Baru</label>
                                <div class="input-group">
                                    <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary toggle-password" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Ubah Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Ketika tombol dengan class .toggle-password di-klik
    $(".toggle-password").click(function() {
        // Cari elemen ikon di dalam tombol yang di-klik
        var icon = $(this).find('i');
        // Cari elemen input di dalam grup yang sama
        var input = $(this).closest('.input-group').find('input');

        // Toggle (ubah) tipe input
        if (input.attr("type") == "password") {
            // Jika tipenya password, ubah ke text
            input.attr("type", "text");
            // Ubah ikon mata menjadi mata-coret
            icon.removeClass("fa-eye").addClass("fa-eye-slash");
        } else {
            // Jika tipenya text, ubah kembali ke password
            input.attr("type", "password");
            // Ubah ikon kembali menjadi mata
            icon.removeClass("fa-eye-slash").addClass("fa-eye");
        }
    });
});
</script>
@endpush