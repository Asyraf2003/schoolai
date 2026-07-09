@extends('layouts.admin', [
  'title' => 'Admin Artikel',
  'activeAdminPage' => 'artikel',
])

@section('content')
  <header class="admin-topbar admin-topbar--compact">
    <div>
      <h1>Artikel</h1>
      <p>Kelola link artikel dari Medium atau platform lain. Konten asli tetap di sumber artikel, website hanya menampilkan ringkasannya.</p>
    </div>

    <div class="admin-inline-actions">
      <span class="admin-counter">{{ $articles->total() }} artikel</span>
      <a href="{{ route('admin.artikel.create') }}" class="admin-primary-action">Tambah Artikel</a>
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

  <section class="admin-gallery-block" aria-label="Daftar artikel">
    @if($articles->isNotEmpty())
      <div class="gallery-lite-list">
        @foreach($articles as $article)
          <article class="gallery-lite-row">
            <span class="gallery-lite-row__order">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>

            <span class="gallery-lite-row__body">
              <strong>{{ $article->admin_title }}</strong>
              <small>
                {{ $article->authorForDisplay() }}
                · {{ optional($article->published_date)->format('d M Y') ?? '-' }}
                · {{ parse_url($article->link_id, PHP_URL_HOST) ?: 'link' }}
              </small>
            </span>

            <span class="gallery-lite-status is-active">Aktif</span>

            <span class="gallery-lite-actions">
              <a href="{{ route('admin.artikel.show', $article) }}" class="admin-small-action admin-small-action--ghost">Detail</a>
              <a href="{{ route('admin.artikel.edit', $article) }}" class="admin-small-action">Edit</a>

              <form method="POST" action="{{ route('admin.artikel.destroy', $article) }}" data-admin-delete-form data-admin-delete-message="Hapus artikel ini dari website? Link sumber tidak ikut terhapus.">
                @csrf
                @method('DELETE')
                <button type="button" data-admin-delete-trigger class="admin-small-action admin-small-action--danger">Hapus</button>
              </form>
            </span>
          </article>
        @endforeach
      </div>

      @if($articles->hasPages())
        <div style="padding: 16px;">
          {{ $articles->links() }}
        </div>
      @endif
    @else
      <div class="gallery-lite-empty">
        <h2>Belum ada artikel.</h2>
        <p>Tambahkan artikel pertama dari Medium atau sumber lain.</p>
      </div>
    @endif
  </section>
@endsection
