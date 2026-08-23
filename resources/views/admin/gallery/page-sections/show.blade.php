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
        <p>Judul dan deskripsi section. Media yang dihapus tetap disimpan sebagai arsip.</p>
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
        <p>Media arsip tidak dapat diedit atau dihapus permanen, tetapi dapat dipulihkan atau menggantikan media aktif identik.</p>
      </div>

      <div class="admin-inline-actions">
        <span class="admin-counter">
          {{ $mediaItems->reject->trashed()->count() }} aktif · {{ $mediaItems->filter->trashed()->count() }} arsip
        </span>
        <a href="{{ route('admin.galeri.section-media.create', $section) }}" class="admin-primary-action">Tambah Media</a>
      </div>
    </div>

    @if($mediaItems->isNotEmpty())
      <div class="admin-media-grid">
        @foreach($mediaRows as ['item' => $item, 'isDeleted' => $isDeleted, 'replacementCandidates' => $replacementCandidates])
          <article class="admin-media-card {{ $isDeleted ? 'is-deleted' : '' }}">
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
              <span class="gallery-lite-status {{ $isDeleted ? 'is-deleted' : ($item->is_published ? 'is-active' : 'is-inactive') }}">
                {{ $isDeleted ? 'Dihapus' : ($item->is_published ? 'Aktif' : 'Nonaktif') }}
              </span>
              <h3>{{ $item->type_label }}</h3>
              <p>{{ $item->media_label }}</p>
              @if($isDeleted && $item->deleted_at)
                <small>Dihapus {{ $item->deleted_at->translatedFormat('d M Y, H:i') }} WIB</small>
              @endif
            </div>

            <div class="gallery-lite-actions">
              @if($isDeleted)
                <form method="POST" action="{{ route('admin.galeri.section-media.restore', $item->getKey()) }}">
                  @csrf
                  @method('PATCH')
                  <button type="submit" class="admin-small-action admin-small-action--restore">Pulihkan</button>
                </form>

                @foreach($replacementCandidates as $candidate)
                  <form
                    method="POST"
                    action="{{ route('admin.galeri.section-media.restore', $item->getKey()) }}"
                    data-admin-delete-form
                    data-admin-delete-message="Pulihkan media lama dan pindahkan media aktif #{{ $candidate->getKey() }} ke arsip? File tetap tersimpan."
                  >
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="replacement_gallery_page_media_item_id" value="{{ $candidate->getKey() }}">
                    <button type="button" data-admin-delete-trigger class="admin-small-action admin-small-action--restore-swap">
                      Pulihkan &amp; Gantikan #{{ $candidate->getKey() }}
                    </button>
                  </form>
                @endforeach
              @else
                <a href="{{ route('admin.galeri.section-media.show', $item) }}" class="admin-small-action admin-small-action--ghost">Detail</a>

                <form method="POST" action="{{ route('admin.galeri.section-media.toggle', $item) }}">
                  @csrf
                  @method('PATCH')
                  <button type="submit" class="admin-small-action">
                    {{ $item->is_published ? 'Matikan' : 'Aktifkan' }}
                  </button>
                </form>

                <form method="POST" action="{{ route('admin.galeri.section-media.destroy', $item) }}" data-admin-delete-form data-admin-delete-message="Arsipkan media ini? File tetap disimpan dan media dapat dipulihkan.">
                  @csrf
                  @method('DELETE')
                  <button type="button" data-admin-delete-trigger class="admin-small-action admin-small-action--danger">Hapus</button>
                </form>
              @endif
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
