{{-- REAL_GALLERY_CRUD_INDEX_FINAL --}}
@php
  $page = __('admin.gallery');
@endphp

@extends('layouts.admin', [
  'title' => $page['title'],
  'activeAdminPage' => 'galeri',
])

@section('content')
  <header class="admin-topbar">
    <div>
      <p class="admin-topbar__eyebrow">{{ $page['eyebrow'] }}</p>
      <h1>{{ $page['heading'] }}</h1>
      <p>{{ $page['description'] }}</p>
    </div>

    @if($canCreate)
      <a href="{{ route('admin.galeri.create') }}" class="admin-primary-action">{{ $page['create_button'] }}</a>
    @else
      <span class="admin-status-pill">{{ str_replace(':max', $limits['max_items'], $page['limit_badge']) }}</span>
    @endif
  </header>

  @if(session('success'))
    <p class="flash-message">{{ session('success') }}</p>
  @endif

  @if($errors->any())
    <div class="admin-error-box">
      @foreach($errors->all() as $error)
        <p>{{ $error }}</p>
      @endforeach
    </div>
  @endif

  <section class="gallery-admin-summary" aria-label="{{ $page['source_title'] }}">
    <article>
      <span>{{ str_replace(':max', $limits['max_items'], $page['limit_badge']) }}</span>
      <strong>{{ $page['source_title'] }}</strong>
      <p>{{ $page['source_description'] }}</p>
    </article>

    <article>
      <span>{{ str_replace(':min', $limits['min_published_items'], $page['minimum_badge']) }}</span>
      <strong>{{ $page['published'] }}</strong>
      <p>{{ $page['empty_description'] }}</p>
    </article>

    <article>
      <span>{{ str_replace(':minutes', $limits['max_video_minutes'], $page['video_badge']) }}</span>
      <strong>{{ $page['video_rule'] }}</strong>
      <p>{{ $page['video_rule_text'] }}</p>
    </article>
  </section>

  <section class="gallery-admin-panel" aria-labelledby="gallery-admin-source-title">
    <div class="gallery-admin-panel__head">
      <div>
        <h2 id="gallery-admin-source-title">{{ $page['source_title'] }}</h2>
        <p>{{ $page['source_description'] }}</p>
      </div>

      <span>{{ $items->count() }}/{{ $limits['max_items'] }}</span>
    </div>

    @if($items->isNotEmpty())
      <div class="gallery-admin-grid">
        @foreach($items as $item)
          <article class="gallery-admin-card" style="--admin-gallery-accent: {{ $item->accent }};">
            <div class="gallery-admin-card__visual">
              <span aria-hidden="true">{{ $item->fallback_icon }}</span>

              <small>
                {{ $item->type_label }}
                @if($item->is_video)
                  · {{ $item->duration_label }}
                @endif
              </small>
            </div>

            <div class="gallery-admin-card__body">
              <div class="gallery-admin-card__title-row">
                <h3>{{ $item->title }}</h3>
                <span class="gallery-status {{ $item->is_published ? 'is-published' : 'is-draft' }}">
                  {{ $item->is_published ? $page['published'] : $page['draft'] }}
                </span>
              </div>

              <dl>
                <div>
                  <dt>{{ $page['type'] }}</dt>
                  <dd>{{ $item->type_label }}</dd>
                </div>

                <div>
                  <dt>{{ $page['category'] }}</dt>
                  <dd>{{ $item->category }}</dd>
                </div>

                <div>
                  <dt>{{ $page['duration'] }}</dt>
                  <dd>{{ $item->duration_label }}</dd>
                </div>

                <div>
                  <dt>{{ $page['date'] }}</dt>
                  <dd>{{ optional($item->published_at)->format('d M Y') ?? '-' }}</dd>
                </div>
              </dl>

              @if($item->caption)
                <p><strong>{{ $page['caption'] }}:</strong> {{ $item->caption }}</p>
              @endif

              <div class="gallery-card-actions">
                <a href="{{ route('admin.galeri.edit', $item) }}" class="admin-small-action">{{ $page['edit_button'] }}</a>

                <form method="POST" action="{{ route('admin.galeri.destroy', $item) }}" onsubmit="return confirm('Hapus item galeri ini?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="admin-small-action admin-small-action--danger">{{ $page['delete_button'] }}</button>
                </form>
              </div>
            </div>
          </article>
        @endforeach
      </div>
    @else
      <div class="gallery-admin-empty">
        <h3>{{ $page['empty_title'] }}</h3>
        <p>{{ $page['empty_description'] }}</p>
      </div>
    @endif
  </section>

  <section class="gallery-admin-panel gallery-admin-panel--map" aria-labelledby="gallery-db-map-title">
    <div class="gallery-admin-panel__head">
      <div>
        <h2 id="gallery-db-map-title">{{ $page['db_title'] }}</h2>
        <p>{{ $page['db_description'] }}</p>
      </div>
    </div>

    <div class="gallery-db-map">
      <table>
        <thead>
          <tr>
            <th>{{ $page['field'] }}</th>
            <th>{{ $page['data_type'] }}</th>
            <th>{{ $page['note'] }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($dbMap as $row)
            <tr>
              <td><code>{{ $row['field'] }}</code></td>
              <td>{{ $row['type'] }}</td>
              <td>{{ $row['note'] }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </section>
@endsection
