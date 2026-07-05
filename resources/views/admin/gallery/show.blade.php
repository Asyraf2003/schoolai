{{-- REAL_GALLERY_CRUD_SHOW_FINAL --}}
@php($page = __('admin.gallery'))

@extends('layouts.admin', [
  'title' => $page['detail_title'],
  'activeAdminPage' => 'galeri',
])

@section('content')
  <header class="admin-topbar admin-topbar--compact">
    <div>
      <p class="admin-topbar__eyebrow">{{ $page['detail_title'] }}</p>
      <h1>{{ $item->title }}</h1>
      <p>{{ $item->type_label }} · {{ $item->category }}</p>
    </div>

    <div class="admin-inline-actions">
      <a href="{{ route('admin.galeri') }}" class="admin-primary-action admin-primary-action--ghost">{{ $page['back_button'] }}</a>
      <a href="{{ route('admin.galeri.edit', $item) }}" class="admin-primary-action">{{ $page['edit_button'] }}</a>
    </div>
  </header>

  @if(session('success'))
    <p class="flash-message">{{ session('success') }}</p>
  @endif

  @if(isset($errors) && $errors->any())
    <div class="admin-error-box">
      @foreach($errors->all() as $error)
        <p>{{ $error }}</p>
      @endforeach
    </div>
  @endif

  <section class="gallery-detail-panel">
    <div class="gallery-detail-preview">
      @if($item->media_url && $item->type === 'photo')
        <img src="{{ $item->media_url }}" alt="{{ $item->title }}">
      @elseif($item->media_url && $item->is_video)
        <video src="{{ $item->media_url }}" controls preload="metadata"></video>
      @else
        <span>{{ $item->fallback_icon }}</span>
      @endif
    </div>

    <dl class="gallery-detail-list">
      <div><dt>{{ $page['status'] }}</dt><dd>{{ $item->is_published ? $page['published'] : $page['draft'] }}</dd></div>
      <div><dt>{{ $page['sort_order'] }}</dt><dd>{{ $item->sort_order }}</dd></div>
      <div><dt>{{ $page['type'] }}</dt><dd>{{ $item->type_label }}</dd></div>
      <div><dt>{{ $page['category'] }}</dt><dd>{{ $item->category }}</dd></div>
      <div><dt>{{ $page['duration'] }}</dt><dd>{{ $item->duration_label }}</dd></div>
      <div><dt>{{ $page['date'] }}</dt><dd>{{ optional($item->published_at)->format('d M Y H:i') ?? '-' }}</dd></div>
      <div><dt>{{ $page['thumbnail'] }}</dt><dd>{{ $item->thumbnail_url ?: '-' }}</dd></div>
      <div><dt>{{ $page['media'] }}</dt><dd>{{ $item->media_url ?: '-' }}</dd></div>
      <div class="gallery-detail-list__wide"><dt>{{ $page['caption'] }}</dt><dd>{{ $item->caption ?: '-' }}</dd></div>
    </dl>
  </section>

  <section class="gallery-detail-actions">
    <form method="POST" action="{{ route('admin.galeri.toggle', $item) }}">
      @csrf
      @method('PATCH')
      <button type="submit" class="admin-small-action">
        {{ $item->is_published ? $page['toggle_off'] : $page['toggle_on'] }}
      </button>
    </form>

    <form method="POST" action="{{ route('admin.galeri.move-up', $item) }}">
      @csrf
      @method('PATCH')
      <button type="submit" class="admin-small-action">{{ $page['move_up'] }}</button>
    </form>

    <form method="POST" action="{{ route('admin.galeri.move-down', $item) }}">
      @csrf
      @method('PATCH')
      <button type="submit" class="admin-small-action">{{ $page['move_down'] }}</button>
    </form>

    <form method="POST" action="{{ route('admin.galeri.destroy', $item) }}" onsubmit="return confirm('Hapus item galeri ini?')">
      @csrf
      @method('DELETE')
      <button type="submit" class="admin-small-action admin-small-action--danger">{{ $page['delete_button'] }}</button>
    </form>
  </section>
@endsection
