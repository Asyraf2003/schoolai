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
      <h1>{{ $section->admin_title }}</h1>
      <p>{{ $section->admin_description ?: 'Belum ada deskripsi.' }}</p>
    </div>

    <div class="admin-inline-actions">
      <a href="{{ route('admin.galeri') }}" class="admin-primary-action admin-primary-action--ghost">Kembali</a>
      <a href="{{ route('admin.galeri.sections.edit', $section) }}" class="admin-primary-action admin-primary-action--ghost">Edit Bagian</a>
      <a href="{{ route('admin.galeri.section-media.create', $section) }}" class="admin-primary-action">Tambah Media</a>
    </div>
  </header>

  <section class="admin-gallery-block">
    <div class="admin-gallery-block__head">
      <div>
        <h2>Detail Bagian</h2>
        <p>Judul dan deskripsi section. Media di bawah hanya foto atau embed.</p>
      </div>

      <span class="gallery-lite-status {{ $section->is_published ? 'is-active' : 'is-inactive' }}">
        {{ $section->is_published ? 'Aktif' : 'Nonaktif' }}
      </span>
    </div>

    <dl class="gallery-detail-list gallery-detail-list--simple">
      <div>
        <dt>Judul Indonesia</dt>
        <dd>{{ $section->title_id ?: '-' }}</dd>
      </div>

      <div>
        <dt>Judul English</dt>
        <dd>{{ $section->title_en ?: '-' }}</dd>
      </div>

      <div class="gallery-detail-list__wide">
        <dt>Deskripsi Indonesia</dt>
        <dd>{{ $section->description_id ?: '-' }}</dd>
      </div>

      <div class="gallery-detail-list__wide">
        <dt>Deskripsi English</dt>
        <dd>{{ $section->description_en ?: '-' }}</dd>
      </div>
    </dl>
  </section>

  <section class="admin-gallery-block">
    <div class="admin-gallery-block__head">
      <div>
        <h2>Media</h2>
        <p>Upload beberapa foto sekaligus atau tempel banyak URL embed dari tombol Tambah Media.</p>
      </div>

      <a href="{{ route('admin.galeri.section-media.create', $section) }}" class="admin-primary-action">Tambah Media</a>
    </div>

    @if($mediaItems->isNotEmpty())
      <div class="admin-media-grid">
        @foreach($mediaItems as $item)
          <article class="admin-media-card">
            <div class="admin-media-card__preview">
              @if($item->is_photo && $item->media_url)
                <img src="{{ $item->media_url }}" alt="{{ $section->admin_title }}">
              @elseif($item->is_video && $item->media_url)
                <iframe src="{{ $item->media_url }}" title="{{ $section->admin_title }}" loading="lazy" allow="fullscreen; picture-in-picture" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>
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
              <a href="{{ route('admin.galeri.section-media.show', $item) }}" class="admin-small-action admin-small-action--ghost">Detail</a>

              <form method="POST" action="{{ route('admin.galeri.section-media.toggle', $item) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="admin-small-action">
                  {{ $item->is_published ? 'Matikan' : 'Aktifkan' }}
                </button>
              </form>

              <form method="POST" action="{{ route('admin.galeri.section-media.destroy', $item) }}" data-admin-delete-form data-admin-delete-message="Hapus media ini?">
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
