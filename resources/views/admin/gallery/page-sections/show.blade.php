{{-- GALLERY_PAGE_SECTION_SHOW_FINAL --}}
@extends('layouts.admin', [
  'title' => 'Detail Bagian Galeri',
  'activeAdminPage' => 'galeri',
])

@section('content')
  @if(session('success'))
    <p class="flash-message" role="status">{{ session('success') }}</p>
  @endif

  @if(isset($errors) && $errors->any())
    <div class="admin-error-box" role="alert">
      @foreach($errors->all() as $error)
        <p>{{ $error }}</p>
      @endforeach
    </div>
  @endif

  <header class="admin-topbar admin-topbar--compact">
    <div>
      <p class="admin-topbar__eyebrow">Detail/Edit Bagian</p>
      <h1>{{ $section->admin_title }}</h1>
      <p>{{ $section->admin_description ?: 'Belum ada deskripsi.' }}</p>
    </div>

    <div class="admin-inline-actions">
      <a href="{{ route('admin.galeri') }}" class="admin-primary-action admin-primary-action--ghost">Kembali</a>
      <a href="{{ route('admin.galeri.section-media.create', $section) }}" class="admin-primary-action">Tambah Media</a>
    </div>
  </header>

  <section class="admin-gallery-block">
    <div class="admin-gallery-block__head">
      <div>
        <h2>Edit Bagian</h2>
        <p>Judul dan deskripsi boleh panjang. Dunia belum runtuh karena textarea, sejauh ini.</p>
      </div>

      <span class="gallery-lite-status {{ $section->is_published ? 'is-active' : 'is-inactive' }}">
        {{ $section->is_published ? 'Aktif' : 'Nonaktif' }}
      </span>
    </div>

    <form method="POST" action="{{ route('admin.galeri.sections.update', $section) }}" class="gallery-lite-form">
      @csrf
      @method('PUT')

      <div class="gallery-lite-form__grid">
        <div class="admin-field">
          <label for="title_id">Judul Indonesia</label>
          <input id="title_id" name="title_id" value="{{ old('title_id', $section->title_id) }}" required>
          @error('title_id') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field">
          <label for="title_en">Judul English</label>
          <input id="title_en" name="title_en" value="{{ old('title_en', $section->title_en) }}">
          @error('title_en') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field admin-field--wide">
          <label for="description_id">Deskripsi Indonesia</label>
          <textarea id="description_id" name="description_id" rows="4">{{ old('description_id', $section->description_id) }}</textarea>
          @error('description_id') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field admin-field--wide">
          <label for="description_en">Deskripsi English</label>
          <textarea id="description_en" name="description_en" rows="4">{{ old('description_en', $section->description_en) }}</textarea>
          @error('description_en') <small>{{ $message }}</small> @enderror
        </div>

        <label class="admin-check-field">
          <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $section->is_published))>
          <span>Aktif</span>
        </label>
      </div>

      <div class="gallery-detail-actions gallery-detail-actions--simple">
        <button type="submit" class="admin-primary-action">Update Bagian</button>
      </div>
    </form>
  </section>

  <section class="admin-gallery-block">
    <div class="admin-gallery-block__head">
      <div>
        <h2>Media di Bagian Ini</h2>
        <p>Tanpa posisi dan tanpa judul per media. Section yang mengatur judul dan deskripsi.</p>
      </div>

      <a href="{{ route('admin.galeri.section-media.create', $section) }}" class="admin-primary-action">Tambah Media</a>
    </div>

    @if($mediaItems->isNotEmpty())
      <div class="admin-media-grid">
        @foreach($mediaItems as $item)
          <article class="admin-media-card">
            <div class="admin-media-card__preview">
              @if($item->is_photo && $item->media_url)
                <img src="{{ $item->media_url }}" alt="{{ $item->admin_title }}">
              @elseif($item->is_video && $item->media_url)
                <iframe src="{{ $item->media_url }}" title="{{ $item->admin_title }}" loading="lazy" allow="fullscreen; picture-in-picture" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>
              @else
                <span>{{ $item->type_label }}</span>
              @endif
            </div>

            <div class="admin-media-card__body">
              <span class="gallery-lite-status {{ $item->is_published ? 'is-active' : 'is-inactive' }}">
                {{ $item->is_published ? 'Aktif' : 'Nonaktif' }}
              </span>
              <h3>{{ $item->type_label }}</h3>
              <p>{{ $item->media_label }}</p>
            </div>

            <div class="gallery-lite-actions">
              <a href="{{ route('admin.galeri.section-media.show', $item) }}" class="admin-small-action admin-small-action--ghost">Detail/Edit</a>

              <form method="POST" action="{{ route('admin.galeri.section-media.toggle', $item) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="admin-small-action">
                  {{ $item->is_published ? 'Matikan' : 'Aktifkan' }}
                </button>
              </form>

              <form method="POST" action="{{ route('admin.galeri.section-media.destroy', $item) }}" onsubmit="return confirm('Hapus media ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="admin-small-action admin-small-action--danger">Hapus</button>
              </form>
            </div>
          </article>
        @endforeach
      </div>
    @else
      <div class="gallery-lite-empty">
        <h2>Belum ada media.</h2>
        <p>Tambahkan foto atau embed untuk menampilkan bagian ini di halaman publik.</p>
      </div>
    @endif
  </section>
@endsection
