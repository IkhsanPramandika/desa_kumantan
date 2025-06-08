@extends('layouts.app')

@section('title', 'Input Data Surat Keterangan Ahli Waris')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Input Data Surat Keterangan Ahli Waris</h1>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Terjadi kesalahan!</strong> Mohon periksa kembali input Anda.
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Form Input Data Pewaris dan Ahli Waris</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('permohonan-sk-ahli-waris.store-data-and-generate-pdf', $permohonan->id) }}" method="POST">
            @csrf
            @method('PUT') {{-- Tambahkan baris ini --}}

            <div class="form-group">
                <label for="nama_pewaris">Nama Pewaris</label>
                <input type="text" class="form-control @error('nama_pewaris') is-invalid @enderror" id="nama_pewaris" name="nama_pewaris" value="{{ old('nama_pewaris', $permohonan->nama_pewaris) }}" required>
                @error('nama_pewaris')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="nik_pewaris">NIK Pewaris (Opsional)</label>
                <input type="text" class="form-control @error('nik_pewaris') is-invalid @enderror" id="nik_pewaris" name="nik_pewaris" value="{{ old('nik_pewaris', $permohonan->nik_pewaris) }}">
                @error('nik_pewaris')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="tempat_lahir_pewaris">Tempat Lahir Pewaris</label>
                <input type="text" class="form-control @error('tempat_lahir_pewaris') is-invalid @enderror" id="tempat_lahir_pewaris" name="tempat_lahir_pewaris" value="{{ old('tempat_lahir_pewaris', $permohonan->tempat_lahir_pewaris) }}" required>
                @error('tempat_lahir_pewaris')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="tanggal_lahir_pewaris">Tanggal Lahir Pewaris</label>
                <input type="date" class="form-control @error('tanggal_lahir_pewaris') is-invalid @enderror" id="tanggal_lahir_pewaris" name="tanggal_lahir_pewaris" value="{{ old('tanggal_lahir_pewaris', $permohonan->tanggal_lahir_pewaris ? $permohonan->tanggal_lahir_pewaris->format('Y-m-d') : '') }}" required>
                @error('tanggal_lahir_pewaris')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="tanggal_meninggal_pewaris">Tanggal Meninggal Pewaris</label>
                <input type="date" class="form-control @error('tanggal_meninggal_pewaris') is-invalid @enderror" id="tanggal_meninggal_pewaris" name="tanggal_meninggal_pewaris" value="{{ old('tanggal_meninggal_pewaris', $permohonan->tanggal_meninggal_pewaris ? $permohonan->tanggal_meninggal_pewaris->format('Y-m-d') : '') }}" required>
                @error('tanggal_meninggal_pewaris')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="alamat_pewaris">Alamat Pewaris</label>
                <textarea class="form-control @error('alamat_pewaris') is-invalid @enderror" id="alamat_pewaris" name="alamat_pewaris" rows="3" required>{{ old('alamat_pewaris', $permohonan->alamat_pewaris) }}</textarea>
                @error('alamat_pewaris')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <hr>
            <h5 class="mb-3">Daftar Ahli Waris</h5>
            <div id="ahli-waris-container">
                @if (old('ahli_waris'))
                    @foreach (old('ahli_waris') as $index => $ahliWaris)
                        @include('petugas.pengajuan.sk_ahli_waris._ahli_waris_template', ['index' => $index, 'ahliWaris' => $ahliWaris])
                    @endforeach
                @elseif ($permohonan->daftar_ahli_waris && count($permohonan->daftar_ahli_waris) > 0)
                    @foreach ($permohonan->daftar_ahli_waris as $index => $ahliWaris)
                        @include('petugas.pengajuan.sk_ahli_waris._ahli_waris_template', ['index' => $index, 'ahliWaris' => $ahliWaris])
                    @endforeach
                @else
                    {{-- Initial empty ahli waris field (will be handled by JS for dynamic addition) --}}
                @endif
            </div>

            {{-- Button actions section --}}
            <div class="d-flex justify-content-start align-items-center mt-4">
                <button type="button" class="btn btn-info btn-sm mr-2" id="add-ahli-waris">Tambah Ahli Waris</button>
                <button type="submit" class="btn btn-primary mr-2">Simpan Data & Buat PDF</button>
                <a href="{{ route('permohonan-sk-ahli-waris.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

{{-- Hidden template for new ahli waris fields --}}
<template id="ahli-waris-template">
    <div class="card bg-light p-3 mb-3 ahli-waris-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="m-0 text-gray-800">Ahli Waris #<span class="ahli-waris-number">__INDEX_PLUS_ONE__</span></h6>
            <button type="button" class="btn btn-danger btn-sm remove-ahli-waris" style="display:none;">Hapus</button>
        </div>

        <div class="form-group">
            <label for="ahli_waris___INDEX___nama">Nama Ahli Waris</label>
            <input type="text" class="form-control" id="ahli_waris___INDEX___nama" name="ahli_waris[__INDEX__][nama]" required>
        </div>

        <div class="form-group">
            <label for="ahli_waris___INDEX___nik">NIK Ahli Waris (Opsional)</label>
            <input type="text" class="form-control" id="ahli_waris___INDEX___nik" name="ahli_waris[__INDEX__][nik]">
        </div>

        <div class="form-group">
            <label for="ahli_waris___INDEX___hubungan">Hubungan dengan Pewaris</label>
            <input type="text" class="form-control" id="ahli_waris___INDEX___hubungan" name="ahli_waris[__INDEX__][hubungan]" required>
        </div>

        <div class="form-group">
            <label for="ahli_waris___INDEX___alamat">Alamat Ahli Waris</label>
            <textarea class="form-control" id="ahli_waris___INDEX___alamat" name="ahli_waris[__INDEX__][alamat]" rows="2" required></textarea>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ahliWarisContainer = document.getElementById('ahli-waris-container');
        const addAhliWarisButton = document.getElementById('add-ahli-waris');
        const ahliWarisTemplate = document.getElementById('ahli-waris-template');

        let ahliWarisCount = ahliWarisContainer.children.length;

        // Function to add a new ahli waris field
        function addAhliWarisField() {
            const templateContent = ahliWarisTemplate.content.cloneNode(true);
            const newAhliWarisCard = templateContent.querySelector('.ahli-waris-card');

            // Replace placeholders with actual index
            const currentAhliWarisIndex = ahliWarisCount;
            newAhliWarisCard.innerHTML = newAhliWarisCard.innerHTML
                .replace(/__INDEX_PLUS_ONE__/g, currentAhliWarisIndex + 1)
                .replace(/__INDEX__/g, currentAhliWarisIndex);

            ahliWarisContainer.appendChild(newAhliWarisCard);
            ahliWarisCount++;
            updateRemoveButtons();
        }

        // Function to update numbers and remove button visibility
        function updateRemoveButtons() {
            const items = ahliWarisContainer.querySelectorAll('.ahli-waris-card');
            items.forEach((item, index) => {
                // Update number display
                const numberSpan = item.querySelector('.ahli-waris-number');
                if (numberSpan) {
                    numberSpan.textContent = index + 1;
                }

                // Update input names and IDs
                item.querySelectorAll('input, textarea').forEach(input => {
                    const nameAttr = input.getAttribute('name');
                    if (nameAttr) {
                        input.setAttribute('name', nameAttr.replace(/ahli_waris\[\d+\]/, `ahli_waris[${index}]`));
                    }
                    const idAttr = input.getAttribute('id');
                    if (idAttr) {
                        input.setAttribute('id', idAttr.replace(/ahli_waris_\d+/, `ahli_waris_${index}`));
                    }
                });

                const removeButton = item.querySelector('.remove-ahli-waris');
                if (removeButton) {
                    // Remove existing event listener to prevent duplicates
                    removeButton.removeEventListener('click', handleRemoveAhliWaris);
                    // Add new event listener
                    removeButton.addEventListener('click', handleRemoveAhliWaris);

                    if (items.length === 1) {
                        removeButton.style.display = 'none'; // Hide if only one item
                    } else {
                        removeButton.style.display = 'inline-block'; // Show if more than one
                    }
                }
            });
            ahliWarisCount = items.length; // Update the global index
        }

        // Handle removing an ahli waris item
        function handleRemoveAhliWaris(event) {
            const itemToRemove = event.target.closest('.ahli-waris-card');
            if (ahliWarisContainer.children.length > 1) { // Prevent removing the last item
                itemToRemove.remove();
                updateRemoveButtons(); // Re-index and update button visibility
            }
        }

        // Add event listener for adding a new item
        addAhliWarisButton.addEventListener('click', addAhliWarisField);

        // Initial setup: add one field if no existing data, and update buttons
        if (ahliWarisCount === 0) {
            addAhliWarisField();
        } else {
            updateRemoveButtons(); // Just update if there's existing data
        }
    });
</script>
@endpush
