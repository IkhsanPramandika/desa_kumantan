{{-- This file is a partial and should be included from input_data.blade.php --}}
<div class="card bg-light p-3 mb-3 ahli-waris-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="m-0 text-gray-800">Ahli Waris #<span class="ahli-waris-number">{{ $index + 1 }}</span></h6>
        <button type="button" class="btn btn-danger btn-sm remove-ahli-waris" style="{{ $index === 0 ? 'display:none;' : '' }}">Hapus</button>
    </div>

    <div class="form-group">
        <label for="ahli_waris_{{ $index }}_nama">Nama Ahli Waris</label>
        <input type="text" class="form-control @error('ahli_waris.' . $index . '.nama') is-invalid @enderror" id="ahli_waris_{{ $index }}_nama" name="ahli_waris[{{ $index }}][nama]" value="{{ old('ahli_waris.' . $index . '.nama', $ahliWaris['nama'] ?? '') }}" required>
        @error('ahli_waris.' . $index . '.nama')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="ahli_waris_{{ $index }}_nik">NIK Ahli Waris (Opsional)</label>
        <input type="text" class="form-control @error('ahli_waris.' . $index . '.nik') is-invalid @enderror" id="ahli_waris_{{ $index }}_nik" name="ahli_waris[{{ $index }}][nik]" value="{{ old('ahli_waris.' . $index . '.nik', $ahliWaris['nik'] ?? '') }}">
        @error('ahli_waris.' . $index . '.nik')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="ahli_waris_{{ $index }}_hubungan">Hubungan dengan Pewaris</label>
        <input type="text" class="form-control @error('ahli_waris.' . $index . '.hubungan') is-invalid @enderror" id="ahli_waris_{{ $index }}_hubungan" name="ahli_waris[{{ $index }}][hubungan]" value="{{ old('ahli_waris.' . $index . '.hubungan', $ahliWaris['hubungan'] ?? '') }}" required>
        @error('ahli_waris.' . $index . '.hubungan')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="ahli_waris_{{ $index }}_alamat">Alamat Ahli Waris</label>
        <textarea class="form-control @error('ahli_waris.' . $index . '.alamat') is-invalid @enderror" id="ahli_waris_{{ $index }}_alamat" name="ahli_waris[{{ $index }}][alamat]" rows="2" required>{{ old('ahli_waris.' . $index . '.alamat', $ahliWaris['alamat'] ?? '') }}</textarea>
        @error('ahli_waris.' . $index . '.alamat')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
