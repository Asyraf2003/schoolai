@extends('admin.layout')
@section('title', $item->exists ? 'Edit media' : 'Tambah media')
@section('content')
    <form class="fields" method="POST" enctype="multipart/form-data" action="{{ $item->exists ? route('admin.galeri.update', $item) : route('admin.galeri.store') }}">
        @csrf @if($item->exists) @method('PUT') @endif
        <label>Foto atau video<input type="file" name="media_file" accept="image/jpeg,image/png,image/webp,video/mp4,video/webm" @required(!$item->exists)></label>
        <p>JPG, PNG, WebP, MP4, atau WebM. Maksimal 100 MB. Saat edit, kosongkan untuk mempertahankan media.</p>
        <label>Thumbnail (opsional)<input type="file" name="thumbnail_file" accept="image/jpeg,image/png,image/webp"></label>
        <p>Thumbnail maksimal 10 MB. Foto menggunakan media asli jika thumbnail kosong.</p>
        @if($item->thumbnail_url)<label class="check"><input type="checkbox" name="remove_thumbnail" value="1">Hapus thumbnail saat ini</label>@endif
        <label>Alt text<input type="text" name="alt_text" value="{{ old('alt_text', $item->alt_text) }}" maxlength="300"></label>
        <div class="actions"><button type="submit">Simpan media</button><a href="{{ route('admin.galeri') }}">Batal</a></div>
    </form>
@endsection
