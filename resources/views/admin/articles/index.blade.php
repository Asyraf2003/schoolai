@extends('layouts.admin', [
  'title' => 'Admin Artikel',
  'activeAdminPage' => 'artikel',
])

@section('content')
  <header class="admin-topbar admin-topbar--compact">
    <div>
      <h1>Artikel</h1>
      <p>Kelola link artikel dari Medium atau platform lain. Artikel yang dihapus tetap disimpan sebagai arsip dan dapat dipulihkan.</p>
    </div>

    <div class="admin-inline-actions">
      <span class="admin-counter">{{ $articles->total() }} artikel termasuk arsip</span>
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
          @php
            $isDeleted = $article->trashed();
            $replacementCandidates = $replacementCandidatesByArticle->get($article->getKey(), collect());
          @endphp

          <article class="gallery-lite-row {{ $isDeleted ? 'is-deleted' : '' }}">
            <span class="gallery-lite-row__order">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>

            <span class="gallery-lite-row__body">
              <strong>{{ $article->admin_title }}</strong>
              <small>
                {{ $article->authorForDisplay() }}
                · {{ $article->published_at ? $article->published_at->translatedFormat('d M Y, H:i').' WIB' : '-' }}
                · {{ parse_url($article->link_id, PHP_URL_HOST) ?: 'link' }}
                @if($isDeleted && $article->deleted_at)
                  · dihapus {{ $article->deleted_at->translatedFormat('d M Y, H:i') }} WIB
                @endif
              </small>
            </span>

            <span class="gallery-lite-status {{ $isDeleted ? 'is-deleted' : 'is-active' }}">
              {{ $isDeleted ? 'Dihapus' : 'Aktif' }}
            </span>

            <span class="gallery-lite-actions">
              @if($isDeleted)
                <form method="POST" action="{{ route('admin.artikel.restore', $article->getKey()) }}">
                  @csrf
                  @method('PATCH')
                  <button type="submit" class="admin-small-action admin-small-action--restore">Pulihkan</button>
                </form>

                @if($replacementCandidates->isNotEmpty())
                  <form
                    method="POST"
                    action="{{ route('admin.artikel.restore', $article->getKey()) }}"
                    class="admin-replacement-form"
                    data-admin-delete-form
                    data-admin-delete-message="Pulihkan artikel arsip ini dan pindahkan artikel aktif yang dipilih ke arsip? Tidak ada data atau file yang dihapus permanen."
                  >
                    @csrf
                    @method('PATCH')

                    <label class="sr-only" for="replacement-article-{{ $article->getKey() }}">Artikel aktif yang digantikan</label>
                    <select
                      id="replacement-article-{{ $article->getKey() }}"
                      name="replacement_article_id"
                      class="admin-replacement-select"
                      required
                    >
                      @foreach($replacementCandidates as $replacementCandidate)
                        <option value="{{ $replacementCandidate->getKey() }}">
                          Gantikan ID {{ $replacementCandidate->getKey() }} · {{ $replacementCandidate->admin_title }}
                        </option>
                      @endforeach
                    </select>

                    <button type="button" data-admin-delete-trigger class="admin-small-action admin-small-action--restore-swap">
                      Pulihkan &amp; Gantikan
                    </button>
                  </form>
                @endif
              @else
                <a href="{{ route('admin.artikel.show', $article) }}" class="admin-small-action admin-small-action--ghost">Detail</a>
                <a href="{{ route('admin.artikel.edit', $article) }}" class="admin-small-action">Edit</a>

                <form method="POST" action="{{ route('admin.artikel.destroy', $article) }}" data-admin-delete-form data-admin-delete-message="Hapus artikel ini dari website? Artikel tetap tersimpan dan dapat dipulihkan.">
                  @csrf
                  @method('DELETE')
                  <button type="button" data-admin-delete-trigger class="admin-small-action admin-small-action--danger">Hapus</button>
                </form>
              @endif
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
