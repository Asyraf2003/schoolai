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
  <form method="POST" action="{{ $action }}" class="gallery-lite-form" enctype="multipart/form-data" novalidate data-article-form>
    @csrf
    @if($isEdit)
      @method('PUT')
    @endif

    <header class="admin-topbar admin-topbar--compact">
      <div>
        <h1>{{ $isEdit ? 'Edit Artikel' : 'Tambah Artikel' }}</h1>
        <p>Thumbnail wajib upload file lokal. Link Indonesia wajib. Link English opsional. Author default Admin kalau dikosongkan.</p>
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
          <label for="description_id">Deskripsi Indonesia</label>
          <textarea id="description_id" name="description_id" rows="3" maxlength="600">{{ old('description_id', $article->description_id) }}</textarea>
          <em>Opsional, tapi disarankan. Dipakai di homepage dan halaman artikel.</em>
          @error('description_id') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field admin-field--wide">
          <label for="description_en">Deskripsi English</label>
          <textarea id="description_en" name="description_en" rows="3" maxlength="600">{{ old('description_en', $article->description_en) }}</textarea>
          <em>Opsional. Jika kosong, versi English fallback ke deskripsi Indonesia.</em>
          @error('description_en') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field admin-field--wide">
          <label for="thumbnail_file">Upload Thumbnail</label>
          <input
            id="thumbnail_file"
            name="thumbnail_file"
            type="file"
            accept="image/jpeg,image/png,image/webp"
            data-article-thumbnail-input
            @required(! $isEdit || ! $article->thumbnail_url)
          >
          <em>
            Wajib. JPG, PNG, atau WebP. Maksimal 10MB.
            @if($isEdit && $article->thumbnail_url)
              Thumbnail saat ini: {{ basename(parse_url($article->thumbnail_url, PHP_URL_PATH) ?: $article->thumbnail_url) }}
            @endif
          </em>
          @error('thumbnail_file') <small>{{ $message }}</small> @enderror
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

      <div class="gallery-media-review__stage" data-article-thumbnail-stage>
        @if($isEdit && $article->thumbnail_url)
          <img src="{{ $article->thumbnail_url }}" alt="{{ $article->admin_title }}" data-article-current-thumbnail>
        @else
          <span data-article-thumbnail-empty>Thumbnail baru akan tampil setelah dipilih.</span>
        @endif
      </div>
    </section>
  </form>

  <script>
    (() => {
      const form = document.querySelector('[data-article-form]');
      if (!form) return;

      const input = form.querySelector('[data-article-thumbnail-input]');
      const stage = form.querySelector('[data-article-thumbnail-stage]');
      const emptyText = 'Thumbnail baru akan tampil setelah dipilih.';
      let previewUrl = null;

      if (!input || !stage) return;

      function revokePreviewUrl() {
        if (previewUrl) {
          URL.revokeObjectURL(previewUrl);
          previewUrl = null;
        }
      }

      function setEmpty(message = emptyText) {
        revokePreviewUrl();

        const span = document.createElement('span');
        span.textContent = message;
        span.setAttribute('data-article-thumbnail-empty', '');

        stage.replaceChildren(span);
      }

      function setImage(file) {
        revokePreviewUrl();

        previewUrl = URL.createObjectURL(file);

        const image = document.createElement('img');
        image.src = previewUrl;
        image.alt = file.name || 'Preview thumbnail artikel';
        image.loading = 'eager';

        stage.replaceChildren(image);
      }

      input.addEventListener('change', () => {
        const file = input.files && input.files[0] ? input.files[0] : null;

        if (!file) {
          setEmpty();
          return;
        }

        if (!file.type || !file.type.startsWith('image/')) {
          input.value = '';
          setEmpty('File harus berupa gambar.');
          return;
        }

        setImage(file);
      });

      window.addEventListener('beforeunload', revokePreviewUrl);
    })();
  </script>
@endsection
