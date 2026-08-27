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
    </div>
  </header>

  <section class="admin-gallery-block">
    <div class="admin-gallery-block__head">
      <div>
        <h2>Pilih Media Canonical</h2>
        <p>Satu media dapat dipakai pada beberapa bagian tanpa upload atau salinan object baru.</p>
      </div>

      <span class="admin-counter">{{ $section->items->count() }} dipilih</span>
    </div>

    <form method="POST" action="{{ route('admin.galeri.sections.media.update', $section) }}">
      @csrf
      @method('PUT')

      @if($galleryItems->isNotEmpty())
        <div class="admin-media-grid">
          @foreach($galleryItems as $item)
            <label class="admin-media-card">
              <span class="admin-media-card__preview">
                @if($item->is_photo && $item->media_url)
                  <img src="{{ $item->media_url }}" alt="{{ $item->admin_title }}">
                @else
                  <span>{{ $item->type_label }}</span>
                @endif
              </span>

              <span class="admin-media-card__body">
                <input
                  type="checkbox"
                  name="gallery_item_ids[]"
                  value="{{ $item->getKey() }}"
                  @checked(in_array($item->getKey(), old('gallery_item_ids', $section->items->modelKeys()), true))
                >
                <strong>{{ $item->admin_title }}</strong>
                <small>{{ $item->type_label }} · {{ $item->media_label }}</small>
              </span>
            </label>
          @endforeach
        </div>

        <div class="admin-inline-actions">
          <button type="submit" class="admin-primary-action">Simpan Pilihan Media</button>
          <a href="{{ route('admin.galeri.create') }}" class="admin-primary-action admin-primary-action--ghost">Upload Media Baru</a>
        </div>
      @else
        <div class="gallery-lite-empty">
          <h2>Belum ada media canonical.</h2>
          <p>Upload media sekali ke koleksi, lalu pilih media tersebut untuk bagian ini.</p>
          <a href="{{ route('admin.galeri.create') }}" class="admin-primary-action">Upload Media</a>
        </div>
      @endif
    </form>
  </section>
@endsection
