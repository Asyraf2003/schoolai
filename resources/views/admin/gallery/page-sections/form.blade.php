{{-- GALLERY_PAGE_SECTION_FORM_FINAL --}}
@php
  $isEdit = $mode === 'edit';
  $action = $isEdit ? route('admin.galeri.sections.update', $section) : route('admin.galeri.sections.store');
@endphp

@extends('layouts.admin', [
  'title' => $isEdit ? 'Edit Bagian Galeri' : 'Tambah Bagian Galeri',
  'activeAdminPage' => 'galeri',
])

@section('content')
  <form method="POST" action="{{ $action }}" class="gallery-lite-form">
    @csrf
    @if($isEdit)
      @method('PUT')
    @endif

    <header class="admin-topbar admin-topbar--compact">
      <div>
        <p class="admin-topbar__eyebrow">Bagian Halaman Galeri</p>
        <h1>{{ $isEdit ? 'Edit Bagian Galeri' : 'Tambah Bagian Galeri' }}</h1>
      </div>

      <div class="admin-inline-actions">
        <a href="{{ $isEdit ? route('admin.galeri.sections.show', $section) : route('admin.galeri') }}" class="admin-primary-action admin-primary-action--ghost">Kembali</a>
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
          <textarea id="description_id" name="description_id" rows="5">{{ old('description_id', $section->description_id) }}</textarea>
          @error('description_id') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field admin-field--wide">
          <label for="description_en">Deskripsi English</label>
          <textarea id="description_en" name="description_en" rows="5">{{ old('description_en', $section->description_en) }}</textarea>
          @error('description_en') <small>{{ $message }}</small> @enderror
        </div>

        <label class="admin-check-field">
          <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $section->is_published))>
          <span>Aktif</span>
        </label>
      </div>
    </section>
  </form>
@endsection
