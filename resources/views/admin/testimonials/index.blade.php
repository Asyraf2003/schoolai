@extends('layouts.admin', [
  'title' => 'Admin Testimoni',
  'activeAdminPage' => 'testimoni',
])

@section('content')
  <header class="admin-topbar admin-topbar--compact">
    <div>
      <h1>Testimoni</h1>
    </div>

    <div class="admin-inline-actions">
      <span class="admin-counter">{{ $activeItems->count() }}/{{ $maxItems }} aktif · {{ $archivedItems->count() }} arsip</span>

      @if($canCreate)
        <a href="{{ route('admin.testimoni.create') }}" class="admin-primary-action">Tambah Media</a>
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

  <section class="admin-gallery-block" aria-label="Daftar media testimoni">
    <div class="admin-gallery-block__head">
      <div>
        <h2>Daftar Media</h2>
      </div>
      <span class="admin-counter">{{ $activeItems->count() + $archivedItems->count() }} data</span>
    </div>

    @if($activeItems->isNotEmpty() || $archivedItems->isNotEmpty())
      <div class="gallery-lite-list">
        @foreach($activeItems as $item)
          <article class="gallery-lite-row">
            <span class="gallery-lite-row__order">{{ str_pad((string) $item->sort_order, 2, '0', STR_PAD_LEFT) }}</span>

            <span class="gallery-lite-row__body">
              <strong>{{ $item->type_label }} · {{ $item->source_label }}</strong>
              <small>{{ $item->media_label }}</small>
            </span>

            <span class="gallery-lite-status {{ $item->is_published ? 'is-active' : 'is-inactive' }}">
              {{ $item->is_published ? 'Aktif' : 'Nonaktif' }}
            </span>

            <span class="gallery-lite-actions">
              <form method="POST" action="{{ route('admin.testimoni.move-up', $item) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="admin-small-action" @disabled($loop->first)>Naik</button>
              </form>

              <form method="POST" action="{{ route('admin.testimoni.move-down', $item) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="admin-small-action" @disabled($loop->last)>Turun</button>
              </form>

              <a href="{{ route('admin.testimoni.edit', $item) }}" class="admin-small-action admin-small-action--ghost">Edit</a>

              <form method="POST" action="{{ route('admin.testimoni.toggle', $item) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="admin-small-action admin-small-action--ghost">
                  {{ $item->is_published ? 'Matikan' : 'Aktifkan' }}
                </button>
              </form>

              <form method="POST" action="{{ route('admin.testimoni.destroy', $item) }}" data-admin-delete-form data-admin-delete-message="Arsipkan media testimoni ini? File tetap tersimpan dan dapat dipulihkan.">
                @csrf
                @method('DELETE')
                <button type="button" data-admin-delete-trigger class="admin-small-action admin-small-action--danger">Hapus</button>
              </form>
            </span>
          </article>
        @endforeach

        @if($archivedItems->isNotEmpty())
          <div class="admin-stat-archive-title">
            <strong>Arsip</strong>
            <small>{{ $archivedItems->count() }} data</small>
          </div>

          @foreach($archivedItems as $item)
            <article class="gallery-lite-row is-deleted">
              <span class="gallery-lite-row__order">A{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>

              <span class="gallery-lite-row__body">
                <strong>{{ $item->type_label }} · {{ $item->source_label }}</strong>
                <small>{{ $item->media_label }}</small>
              </span>

              <span class="gallery-lite-status is-deleted">Arsip</span>

              <span class="gallery-lite-actions">
                @if($canCreate)
                  <form method="POST" action="{{ route('admin.testimoni.restore', $item->getKey()) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="admin-small-action admin-small-action--restore">Pulihkan</button>
                  </form>
                @else
                  <span class="admin-archive-note">Slot aktif penuh.</span>
                @endif
              </span>
            </article>
          @endforeach
        @endif
      </div>
    @else
      <div class="gallery-lite-empty">
        <h2>Belum ada media testimoni.</h2>
      </div>
    @endif
  </section>
@endsection
