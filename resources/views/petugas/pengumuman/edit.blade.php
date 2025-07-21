@extends('layouts.app')

@section('title', 'Edit Pengumuman: ' . $pengumuman->judul)

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Edit Pengumuman: {{ Str::limit($pengumuman->judul, 40) }}</h1>

    <div class="card shadow mb-4">
        <div class="card-body p-4">
            <form action="{{ route('petugas.pengumuman.update', $pengumuman->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('layouts.partials.form-fields', ['pengumuman' => $pengumuman])
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @include('layouts.partials.form-scripts')
@endpush