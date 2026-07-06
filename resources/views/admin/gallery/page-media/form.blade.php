{{-- GALLERY_PAGE_MEDIA_CREATE_FINAL --}}
@extends('layouts.admin', [
  'title' => 'Tambah Media Galeri',
  'activeAdminPage' => 'galeri',
])

@section('content')
  <header class="admin-topbar admin-topbar--compact">
    <div>
      <p class="admin-topbar__eyebrow">Media Bagian Galeri</p>
      <h1>Tambah Media</h1>
      <p>Bagian: {{ $section->admin_title }}</p>
    </div>

    <div class="admin-inline-actions">
      <a href="{{ route('admin.galeri.sections.show', $section) }}" class="admin-primary-action admin-primary-action--ghost">Kembali</a>
    </div>
  </header>

  @if(isset($errors) && $errors->any())
    <div class="admin-error-box" role="alert">
      @foreach($errors->all() as $error)
        <p>{{ $error }}</p>
      @endforeach
    </div>
  @endif

  @include('admin.gallery.page-media._form')
@endsection
