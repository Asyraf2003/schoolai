@extends('admin.layout')
@section('title', $article->exists ? 'Edit artikel' : 'Tambah artikel')
@section('content')
    <p>Isi minimal satu bahasa. Judul, deskripsi, tanggal, dan cover opsional. Isi artikel hanya teks terformat dan hyperlink.</p>
    <form class="fields" method="POST" enctype="multipart/form-data" action="{{ $article->exists ? route('admin.artikel.update', $article) : route('admin.artikel.store') }}">
        @csrf @if($article->exists) @method('PUT') @endif
        @foreach(['id' => 'Indonesia', 'en' => 'English', 'ar' => 'العربية'] as $locale => $label)
            <fieldset lang="{{ $locale }}" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">
                <legend>{{ $label }}</legend>
                <div class="fields">
                    <label>Judul (opsional)<input type="text" name="title_{{ $locale }}" value="{{ old('title_'.$locale, $article->{'title_'.$locale}) }}" maxlength="200"></label>
                    <label>Deskripsi (opsional)<textarea name="description_{{ $locale }}" maxlength="2000">{{ old('description_'.$locale, $article->{'description_'.$locale}) }}</textarea></label>
                    <div data-editor>
                        <label for="content-{{ $locale }}" id="label-{{ $locale }}">Isi artikel</label>
                        <div class="editor-toolbar" data-editor-toolbar hidden role="group" aria-label="Format teks {{ $label }}">
                            @foreach(['bold' => 'Tebal', 'italic' => 'Miring', 'underline' => 'Garis bawah', 'insertUnorderedList' => 'Daftar', 'createLink' => 'Tautan', 'unlink' => 'Hapus tautan'] as $command => $name)
                                <button class="secondary" type="button" data-command="{{ $command }}">{{ $name }}</button>
                            @endforeach
                        </div>
                        <textarea id="content-{{ $locale }}" name="content_{{ $locale }}" rows="12">{{ app(\App\Support\ArticleContentSanitizer::class)->sanitize(old('content_'.$locale, $article->{'content_'.$locale})) }}</textarea>
                        <div data-editor-surface hidden contenteditable="true" role="textbox" aria-multiline="true" aria-labelledby="label-{{ $locale }}"></div>
                    </div>
                </div>
            </fieldset>
        @endforeach
        <label>Tanggal tampilan (opsional)<input type="date" name="published_date" value="{{ old('published_date', $article->published_date?->format('Y-m-d')) }}"></label>
        <label>Cover foto/video (opsional)<input type="file" name="cover_file" accept="image/jpeg,image/png,image/webp,video/mp4,video/webm"></label>
        <label>Poster cover video (opsional)<input type="file" name="cover_thumbnail_file" accept="image/jpeg,image/png,image/webp"></label>
        <p>Cover maksimal 100 MB. Tanggal hanya metadata tampilan, bukan jadwal penerbitan.</p>
        @if($article->cover_url)<label class="check"><input type="checkbox" name="remove_cover" value="1">Hapus cover saat ini</label>@endif
        @if($article->cover_thumbnail_url)<label class="check"><input type="checkbox" name="remove_cover_thumbnail" value="1">Hapus poster saat ini</label>@endif
        <div class="actions"><button type="submit">Simpan artikel</button><a href="{{ route('admin.artikel') }}">Batal</a></div>
    </form>
@endsection
