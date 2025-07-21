@extends('layouts.app')

@section('title', 'Buat Pengumuman Baru')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Buat Pengumuman / Berita Desa Baru</h1>

    <div class="card shadow mb-4">
        <div class="card-body p-4">
            <form action="{{ route('petugas.pengumuman.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('layouts.partials.form-fields', ['pengumuman' => new \App\Models\Pengumuman()])
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @include('layouts.partials.form-scripts')
@endpush
