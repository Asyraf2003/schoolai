@extends('layouts.admin', [
  'title' => 'Admin Testimoni',
  'activeAdminPage' => 'testimoni',
])

@section('content')
  <header class="admin-topbar admin-topbar--compact">
    <div>
      <h1>Testimoni</h1>
      <p>Kelola media untuk section “Apa Kata Mereka Tentang Al Mustaqbal?”. Urutan video menentukan video utama pertama yang tampil di tengah.</p>
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

  <section class="admin-gallery-block" aria-label="Media testimoni aktif">
    <div class="admin-gallery-block__head">
      <div>
        <h2>Media Aktif</h2>
        <p>Foto menjadi media pendamping. Video aktif dengan urutan paling atas menjadi video utama section testimoni.</p>
      </div>
    </div>

    @if($activeItems->isNotEmpty())
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
      </div>
    @else
      <div class="gallery-lite-empty">
        <h2>Belum ada media testimoni.</h2>
        <p>Homepage tetap memakai media dummy sampai media testimoni aktif ditambahkan.</p>
      </div>
    @endif
  </section>

  @if($archivedItems->isNotEmpty())
    <section class="admin-gallery-block" aria-label="Arsip media testimoni" style="margin-top: 22px;">
      <div class="admin-gallery-block__head">
        <div>
          <h2>Arsip</h2>
          <p>Media yang dihapus dari daftar aktif tetap dapat dipulihkan.</p>
        </div>
      </div>

      <div class="gallery-lite-list">
        @foreach($archivedItems as $item)
          <article class="gallery-lite-row is-deleted">
            <span class="gallery-lite-row__order">A{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
            <span class="gallery-lite-row__body">
              <strong>{{ $item->type_label }} · {{ $item->source_label }}</strong>
              <small>{{ $item->media_label }}</small>
            </span>
            <span class="gallery-lite-status is-deleted">Dihapus</span>
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
      </div>
    </section>
  @endif
@endsection
