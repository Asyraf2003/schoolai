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
      <h1>{{ $item->admin_title }}</h1>
      <p>{{ $item->type_label }} · {{ $item->admin_category }}</p>
    </div>

    <div class="admin-inline-actions">
      <a href="{{ route('admin.galeri') }}" class="admin-primary-action admin-primary-action--ghost">{{ $page['back_button'] }}</a>
      <a href="{{ route('admin.galeri.edit', $item) }}" class="admin-primary-action">{{ $page['edit_button'] }}</a>
    </div>
  </header>

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

  <section class="gallery-detail-panel">
    <div class="gallery-detail-preview">
      @if($item->is_photo && $item->media_url)
        <img src="{{ $item->media_url }}" alt="{{ $item->admin_title }}">
      @elseif($item->is_video && $item->media_url)
        <iframe src="{{ $item->media_url }}" title="{{ $item->admin_title }}" loading="lazy" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>
      @else
        <span>{{ $item->type_label }}</span>
      @endif
    </div>

    <dl class="gallery-detail-list">
      <div><dt>{{ $page['status'] }}</dt><dd>{{ $item->is_published ? $page['published'] : $page['draft'] }}</dd></div>
      <div><dt>{{ $page['sort_order'] }}</dt><dd>{{ $item->sort_order }}</dd></div>
      <div><dt>{{ $page['type'] }}</dt><dd>{{ $item->type_label }}</dd></div>
      <div><dt>{{ $page['category'] }}</dt><dd>{{ $item->admin_category }}</dd></div>
      <div><dt>{{ $page['date'] }}</dt><dd>{{ optional($item->published_at)->format('d M Y H:i') ?? '-' }}</dd></div>
      <div><dt>{{ $page['media'] }}</dt><dd>@if($item->media_url)<a href="{{ $item->media_url }}" target="_blank" rel="noopener">{{ $item->media_label }}</a>@else - @endif</dd></div>
      <div class="gallery-detail-list__wide"><dt>{{ $page['caption'] }}</dt><dd>{{ $item->admin_caption ?: '-' }}</dd></div>
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

    <form method="POST" action="{{ route('admin.galeri.destroy', $item) }}" onsubmit="return confirm('Hapus item galeri ini?')">
      @csrf
      @method('DELETE')
      <button type="submit" class="admin-small-action admin-small-action--danger">{{ $page['delete_button'] }}</button>
    </form>
  </section>
@endsection
