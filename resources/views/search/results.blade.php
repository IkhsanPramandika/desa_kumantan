{{-- Lokasi: resources/views/petugas/search/results.blade.php --}}
@extends('layouts.app')

@section('title', 'Hasil Pencarian')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Hasil Pencarian untuk: "{{ $query }}"</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Ditemukan {{ $results->count() }} hasil</h6>
        </div>
        <div class="card-body">
            @if($results->isEmpty())
                <div class="text-center my-5">
                    <i class="fas fa-search fa-3x text-gray-400"></i>
                    <p class="mt-3 text-gray-600">Tidak ada permohonan yang cocok dengan kata kunci Anda.</p>
                    <p class="small">Coba gunakan kata kunci lain.</p>
                </div>
            @else
                <div class="list-group list-group-flush">
                    @foreach ($results as $item)
                        <a href="{{ $item->getRouteTujuan() }}" class="list-group-item list-group-item-action flex-column align-items-start">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1 font-weight-bold text-primary">{{ $item->getJudulNotifikasi() }}</h5>
                                <small>{{ $item->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">
                                Diajukan oleh: <strong>{{ $item->masyarakat->nama_lengkap ?? 'N/A' }}</strong>
                                (NIK: {{ $item->masyarakat->nik ?? 'N/A' }})
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">ID Permohonan: #{{ $item->id }}</small>
                                @include('layouts.partials.status_badge', ['status' => $item->status])
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
