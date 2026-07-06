{{-- GALLERY_PAGE_MEDIA_SHOW_FINAL --}}
@extends('layouts.admin', [
  'title' => 'Detail Media Galeri',
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
      <h1>Detail Media</h1>
      <p>Bagian: {{ $section->admin_title }}. Media ini hanya menyimpan foto/embed.</p>
    </div>

    <div class="admin-inline-actions">
      <a href="{{ route('admin.galeri.sections.show', $section) }}" class="admin-primary-action admin-primary-action--ghost">Kembali</a>
      <a href="{{ route('admin.galeri.section-media.edit', $item) }}" class="admin-primary-action">Edit Media</a>

      <form method="POST" action="{{ route('admin.galeri.section-media.toggle', $item) }}">
        @csrf
        @method('PATCH')
        <button type="submit" class="admin-primary-action admin-primary-action--ghost">
          {{ $item->is_published ? 'Matikan' : 'Aktifkan' }}
        </button>
      </form>

      <form method="POST" action="{{ route('admin.galeri.section-media.destroy', $item) }}" onsubmit="return confirm('Hapus media ini?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="admin-primary-action admin-primary-action--danger">Hapus</button>
      </form>
    </div>
  </header>

  <section class="gallery-detail-panel gallery-detail-panel--simple">
    <div class="gallery-detail-preview gallery-detail-preview--large">
      @if($item->is_photo && $item->media_url)
        <img src="{{ $item->media_url }}" alt="{{ $section->admin_title }}">
      @elseif($item->is_video && $item->media_url)
        <iframe src="{{ $item->media_url }}" title="{{ $section->admin_title }}" loading="lazy" allow="fullscreen; picture-in-picture" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>
      @else
        <span>{{ $item->type_label }}</span>
      @endif
    </div>

    <div class="gallery-detail-summary">
      <div class="gallery-detail-summary__head">
        <div>
          <h1>{{ $item->type_label }}</h1>
          <p>{{ $item->media_label }}</p>
        </div>

        <span class="gallery-lite-status {{ $item->is_published ? 'is-active' : 'is-inactive' }}">
          {{ $item->is_published ? 'Aktif' : 'Nonaktif' }}
        </span>
      </div>

      <dl class="gallery-detail-list gallery-detail-list--simple">
        <div><dt>Bagian</dt><dd>{{ $section->admin_title }}</dd></div>
        <div><dt>Status</dt><dd>{{ $item->is_published ? 'Aktif' : 'Nonaktif' }}</dd></div>
        <div><dt>Tipe</dt><dd>{{ $item->type_label }}</dd></div>
        <div><dt>Tanggal</dt><dd>{{ optional($item->published_at)->format('d M Y H:i') ?? '-' }}</dd></div>
        <div class="gallery-detail-list__wide"><dt>Media</dt><dd>@if($item->media_url)<a href="{{ $item->media_url }}" target="_blank" rel="noopener">{{ $item->media_url }}</a>@else - @endif</dd></div>
      </dl>
    </div>
  </section>
@endsection
