@extends('layouts.app')

@section('title', 'Ajukan Surat')

@section('content')
    <div class="card">
        <div class="card-header">Ajukan Surat</div>
        <div class="card-body">
            <form action="{{ route('pengajuan.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Jenis Layanan</label>
                    <select name="jenis_layanan" class="form-select" required>
                        @foreach($layanan as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Data Tambahan (JSON)</label>
                    <textarea name="data_tambahan" class="form-control" rows="5" 
                        placeholder='{"nama": "John Doe", "alamat": "Jl. Desa No.1"}' required></textarea>
                    <small class="text-muted">Masukkan data sesuai kebutuhan layanan</small>
                </div>
                
                <button type="submit" class="btn btn-primary">Ajukan</button>
            </form>
        </div>
    </div>
@endsection
