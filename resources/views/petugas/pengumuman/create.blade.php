<form action="{{ route('pengumuman.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label>Judul</label>
        <input type="text" name="judul" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Isi Pengumuman</label>
        <textarea name="isi" class="form-control" rows="5" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Buat Pengumuman</button>
</form>

