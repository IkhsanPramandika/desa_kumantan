@if ($errors->any())
    <div class="alert alert-danger">
        <p><strong>Harap perbaiki error berikut:</strong></p>
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif



<div class="form-group">
    <label for="judul" class="font-weight-bold">Judul Pengumuman <span class="text-danger">*</span></label>
    <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul', $pengumuman->judul) }}" required placeholder="Masukkan judul pengumuman">
    @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="isi_pengumuman" class="font-weight-bold">Isi Pengumuman <span class="text-danger">*</span></label>
    <textarea class="form-control @error('isi') is-invalid @enderror" id="wysiwyg" name="isi" rows="15">{{ old('isi', $pengumuman->isi) }}</textarea>
    @error('isi') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
</div>

