{{-- GALLERY_PAGE_MEDIA_EDIT_FINAL --}}
@extends('layouts.admin', [
  'title' => 'Edit Media Galeri',
  'activeAdminPage' => 'galeri',
])

@section('content')
  <header class="admin-topbar admin-topbar--compact">
    <div>
      <h1>Edit Media</h1>
      <p>Bagian: {{ $section->admin_title }}. Edit satu media saja, bukan bulk.</p>
    </div>

    <div class="admin-inline-actions">
      <a href="{{ route('admin.galeri.section-media.show', $item) }}" class="admin-primary-action admin-primary-action--ghost">Kembali</a>
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
