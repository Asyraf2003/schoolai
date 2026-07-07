{{-- ADMIN_ARTICLES_FORM_FINAL --}}
@php
  $isEdit = $mode === 'edit';
  $action = $isEdit ? route('admin.artikel.update', $article) : route('admin.artikel.store');
  $publishedDateValue = old('published_date', optional($article->published_date)->format('Y-m-d') ?? now()->toDateString());
@endphp

@extends('layouts.admin', [
  'title' => $isEdit ? 'Edit Artikel' : 'Tambah Artikel',
  'activeAdminPage' => 'artikel',
])

@section('content')
  <form method="POST" action="{{ $action }}" class="gallery-lite-form" novalidate>
    @csrf
    @if($isEdit)
      @method('PUT')
    @endif

    <header class="admin-topbar admin-topbar--compact">
      <div>
        <h1>{{ $isEdit ? 'Edit Artikel' : 'Tambah Artikel' }}</h1>
        <p>Link Indonesia wajib. Link English opsional. Author default Admin kalau dikosongkan.</p>
      </div>

      <div class="admin-inline-actions">
        <a href="{{ $isEdit ? route('admin.artikel.show', $article) : route('admin.artikel') }}" class="admin-primary-action admin-primary-action--ghost">Kembali</a>
        <button type="submit" class="admin-primary-action">{{ $isEdit ? 'Update' : 'Simpan' }}</button>
      </div>
    </header>

    @if(isset($errors) && $errors->any())
      <div class="admin-error-box" role="alert">
        @foreach($errors->all() as $error)
          <p>{{ $error }}</p>
        @endforeach
      </div>
    @endif

    <section class="gallery-lite-form__panel">
      <div class="gallery-lite-form__grid">
        <div class="admin-field">
          <label for="title_id">Judul Indonesia</label>
          <input id="title_id" name="title_id" value="{{ old('title_id', $article->title_id) }}" maxlength="200" required>
          @error('title_id') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field">
          <label for="title_en">Judul English</label>
          <input id="title_en" name="title_en" value="{{ old('title_en', $article->title_en) }}" maxlength="200">
          @error('title_en') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field admin-field--wide">
          <label for="thumbnail_url">URL Thumbnail</label>
          <input id="thumbnail_url" name="thumbnail_url" type="url" value="{{ old('thumbnail_url', $article->thumbnail_url) }}" maxlength="2048" placeholder="https://..." required>
          <em>Wajib. Gunakan URL gambar dari Medium atau gambar publik lain.</em>
          @error('thumbnail_url') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field admin-field--wide">
          <label for="link_id">Link Artikel Indonesia</label>
          <input id="link_id" name="link_id" type="url" value="{{ old('link_id', $article->link_id) }}" maxlength="2048" placeholder="https://medium.com/..." required>
          @error('link_id') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field admin-field--wide">
          <label for="link_en">Link Artikel English</label>
          <input id="link_en" name="link_en" type="url" value="{{ old('link_en', $article->link_en) }}" maxlength="2048" placeholder="https://medium.com/...">
          <em>Opsional. Jika kosong, versi English akan fallback ke link Indonesia.</em>
          @error('link_en') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field">
          <label for="author">Author</label>
          <input id="author" name="author" value="{{ old('author', $article->author ?: 'Admin') }}" maxlength="120">
          @error('author') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field">
          <label for="published_date">Tanggal Publikasi</label>
          <input id="published_date" name="published_date" type="date" value="{{ $publishedDateValue }}">
          <em>Opsional. Kalau kosong, otomatis hari ini.</em>
          @error('published_date') <small>{{ $message }}</small> @enderror
        </div>
      </div>
    </section>

    <section class="gallery-media-review">
      <div class="gallery-media-review__head">
        <strong>Preview Thumbnail</strong>
      </div>

      <div class="gallery-media-review__stage">
        @if(old('thumbnail_url', $article->thumbnail_url))
          <img src="{{ old('thumbnail_url', $article->thumbnail_url) }}" alt="{{ old('title_id', $article->admin_title) }}">
        @else
          <span>Masukkan URL thumbnail untuk preview.</span>
        @endif
      </div>
    </section>
  </form>
@endsection
