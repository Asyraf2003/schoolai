{{-- REAL_GALLERY_CRUD_INDEX_FINAL --}}
@php($page = __('admin.gallery'))

@extends('layouts.admin', [
  'title' => $page['title'],
  'activeAdminPage' => 'galeri',
])

@section('content')
  <header class="admin-topbar admin-topbar--compact">
    <div>
      <p class="admin-topbar__eyebrow">{{ $page['eyebrow'] }}</p>
      <h1>{{ $page['heading'] }}</h1>
      <p>{{ $page['description'] }}</p>
    </div>

    <div class="admin-inline-actions">
      <span class="admin-counter">{{ str_replace([':count', ':max'], [$items->count(), $limits['max_items']], $page['limit_badge']) }}</span>

      @if($canCreate)
        <a href="{{ route('admin.galeri.create') }}" class="admin-primary-action">{{ $page['create_button'] }}</a>
      @endif
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

  <section class="gallery-lite-panel" aria-label="{{ $page['heading'] }}">
    @if($items->isNotEmpty())
      <div class="gallery-lite-list">
        @foreach($items as $item)
          <a href="{{ route('admin.galeri.show', $item) }}" class="gallery-lite-row">
            <span class="gallery-lite-row__order">{{ $item->sort_order }}</span>

            <span class="gallery-lite-row__body">
              <strong>{{ $item->admin_title }}</strong>
              <small>{{ $item->type_label }} · {{ $item->admin_category }} · {{ $item->media_label }}</small>
            </span>

            <span class="gallery-lite-status {{ $item->is_published ? 'is-active' : 'is-inactive' }}">
              {{ $item->is_published ? $page['published'] : $page['draft'] }}
            </span>
          </a>
        @endforeach
      </div>
    @else
      <div class="gallery-lite-empty">
        <h2>{{ $page['empty_title'] }}</h2>
        <p>{{ $page['empty_description'] }}</p>
      </div>
    @endif
  </section>
@endsection
