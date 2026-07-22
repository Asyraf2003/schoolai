@extends('layouts.admin', [
  'title' => 'Admin Hero Section',
  'activeAdminPage' => 'hero',
])

@section('content')
  <header class="admin-topbar admin-topbar--compact">
    <div>
      <h1>Hero Section</h1>
      <p>Kelola slide fullscreen homepage tanpa mengubah renderer hero yang sudah ada. Urutan di bawah sama dengan urutan tampil di website.</p>
    </div>

    <div class="admin-inline-actions">
      <span class="admin-counter">{{ $slides->count() }} slide</span>
      <a href="{{ route('admin.hero.create') }}" class="admin-primary-action">Tambah Slide</a>
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

  <section class="admin-gallery-block" aria-label="Daftar hero slide">
    @if($slides->isNotEmpty())
      <div class="gallery-lite-list">
        @foreach($slides as $slide)
          <article class="gallery-lite-row">
            <span class="gallery-lite-row__order">{{ str_pad((string) $slide->sort_order, 2, '0', STR_PAD_LEFT) }}</span>

            <span class="gallery-lite-row__body">
              <strong>{{ $slide->admin_title }}</strong>
              <small>
                {{ strtoupper($slide->type) }}
                · {{ $slide->focal_position }}
                · overlay {{ number_format((float) $slide->overlay_strength, 2) }}
              </small>
            </span>

            <span class="gallery-lite-status {{ $slide->is_active ? 'is-active' : 'is-deleted' }}">
              {{ $slide->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>

            <span class="gallery-lite-actions">
              <form method="POST" action="{{ route('admin.hero.move-up', $slide) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="admin-small-action admin-small-action--ghost" @disabled($loop->first)>Naik</button>
              </form>

              <form method="POST" action="{{ route('admin.hero.move-down', $slide) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="admin-small-action admin-small-action--ghost" @disabled($loop->last)>Turun</button>
              </form>

              <form method="POST" action="{{ route('admin.hero.toggle', $slide) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="admin-small-action admin-small-action--ghost">{{ $slide->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
              </form>

              <a href="{{ route('admin.hero.edit', $slide) }}" class="admin-small-action">Edit</a>

              <form method="POST" action="{{ route('admin.hero.destroy', $slide) }}" data-admin-delete-form data-admin-delete-message="Hapus hero slide ini? Jika semua slide dihapus atau dinonaktifkan, homepage otomatis kembali ke fallback locale existing.">
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
        <h2>Belum ada hero slide di database.</h2>
        <p>Homepage tetap memakai fallback locale existing sampai slide database dibuat dan diaktifkan.</p>
      </div>
    @endif
  </section>
@endsection
