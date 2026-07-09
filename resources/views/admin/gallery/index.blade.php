@php($page = __('admin.gallery'))

@extends('layouts.admin', [
  'title' => $page['title'],
  'activeAdminPage' => 'galeri',
])

@section('content')
  <header class="admin-topbar admin-topbar--compact">
    <div>
      <h1>{{ $page['heading'] }}</h1>
    </div>

    <div class="admin-inline-actions">
      @if($canCreate)
        <a href="{{ route('admin.galeri.create') }}" class="admin-primary-action">{{ $page['create_button'] }} Utama</a>
      @endif

      <a href="{{ route('admin.galeri.sections.create') }}" class="admin-primary-action admin-primary-action--ghost">Tambah Bagian</a>
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

  <section class="admin-gallery-block" aria-label="Galeri utama homepage">
    <div class="admin-gallery-block__head">
      <div>
        <h2>Galeri Utama Homepage</h2>
      </div>

      <span class="admin-counter">{{ $items->count() }}/{{ $limits['max_items'] ?? 6 }} item</span>
    </div>

    @if($items->isNotEmpty())
      <div class="gallery-lite-list">
        @foreach($items as $item)
          <article class="gallery-lite-row">
            <span class="gallery-lite-row__order">{{ str_pad((string) $item->sort_order, 2, '0', STR_PAD_LEFT) }}</span>

            <span class="gallery-lite-row__body">
              <strong>{{ $item->admin_title }}</strong>
              <small>{{ $item->type_label }} · {{ $item->admin_category }} · {{ $item->media_label }}</small>
            </span>

            <span class="gallery-lite-status {{ $item->is_published ? 'is-active' : 'is-inactive' }}">
              {{ $item->is_published ? $page['published'] : $page['draft'] }}
            </span>

            <span class="gallery-lite-actions">
              <form method="POST" action="{{ route('admin.galeri.move-up', $item) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="admin-small-action" @disabled($loop->first)>{{ $page['move_up'] }}</button>
              </form>

              <form method="POST" action="{{ route('admin.galeri.move-down', $item) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="admin-small-action" @disabled($loop->last)>{{ $page['move_down'] }}</button>
              </form>

              <a href="{{ route('admin.galeri.show', $item) }}" class="admin-small-action admin-small-action--ghost">Detail</a>

              <form method="POST" action="{{ route('admin.galeri.toggle', $item) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="admin-small-action admin-small-action--ghost">
                  {{ $item->is_published ? $page['toggle_off'] : $page['toggle_on'] }}
                </button>
              </form>

              <form method="POST" action="{{ route('admin.galeri.destroy', $item) }}" data-admin-delete-form data-admin-delete-message="Hapus item galeri utama ini?">
                @csrf
                @method('DELETE')
                <button type="button" data-admin-delete-trigger class="admin-small-action admin-small-action--danger">{{ $page['delete_button'] }}</button>
              </form>
            </span>
          </article>
        @endforeach
      </div>
    @else
      <div class="gallery-lite-empty">
        <h2>{{ $page['empty_title'] }}</h2>
        <p>{{ $page['empty_description'] }}</p>
      </div>
    @endif
  </section>

  <section class="admin-gallery-block" aria-label="Bagian halaman galeri">
    <div class="admin-gallery-block__head">
      <div>
        <h2>Bagian Halaman Galeri</h2>
        <p>Tanpa posisi. Tiap bagian bisa punya banyak foto atau embed.</p>
      </div>

      <a href="{{ route('admin.galeri.sections.create') }}" class="admin-primary-action">Tambah Bagian</a>
    </div>

    @if(($pageSections ?? collect())->isNotEmpty())
      <div class="admin-section-grid">
        @foreach($pageSections as $section)
          <article class="admin-section-card">
            <div class="admin-section-card__body">
              <span class="gallery-lite-status {{ $section->is_published ? 'is-active' : 'is-inactive' }}">
                {{ $section->is_published ? $page['published'] : $page['draft'] }}
              </span>

              <h3>{{ $section->admin_title }}</h3>
              <p>{{ $section->admin_description ?: 'Belum ada deskripsi.' }}</p>

              <small>{{ $section->media_items_count }} media</small>
            </div>

            <div class="gallery-lite-actions admin-section-card__actions">
              <a href="{{ route('admin.galeri.sections.show', $section) }}" class="admin-small-action admin-small-action--ghost">Detail</a>

              <form method="POST" action="{{ route('admin.galeri.sections.toggle', $section) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="admin-small-action">
                  {{ $section->is_published ? $page['toggle_off'] : $page['toggle_on'] }}
                </button>
              </form>

              <form method="POST" action="{{ route('admin.galeri.sections.destroy', $section) }}" data-admin-delete-form data-admin-delete-message="Hapus bagian galeri ini beserta semua medianya?">
                @csrf
                @method('DELETE')
                <button type="button" data-admin-delete-trigger class="admin-small-action admin-small-action--danger">{{ $page['delete_button'] }}</button>
              </form>
            </div>
          </article>
        @endforeach
      </div>
    @else
      <div class="gallery-lite-empty">
        <h2>Belum ada bagian galeri.</h2>
        <p>Tambahkan bagian seperti Prestasi, Fasilitas, Testimoni, Laporan, atau Media.</p>
      </div>
    @endif
  </section>
@endsection
