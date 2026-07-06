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
      <p class="admin-topbar__eyebrow">Detail/Edit Media</p>
      <h1>{{ $item->admin_title }}</h1>
      <p>Bagian: {{ $section->admin_title }}</p>
    </div>

    <div class="admin-inline-actions">
      <a href="{{ route('admin.galeri.sections.show', $section) }}" class="admin-primary-action admin-primary-action--ghost">Kembali</a>

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

  @include('admin.gallery.page-media._form')
@endsection
