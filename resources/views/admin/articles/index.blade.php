@extends('layouts.admin', [
  'title' => 'Admin Artikel',
  'activeAdminPage' => 'artikel',
])

@section('content')
  <header class="admin-topbar admin-topbar--compact">
    <div>
      <h1>Artikel</h1>
      <p>Kelola konten, Homepage Article, dan Hero Spotlight dari satu halaman. Pin hanya mengatur penempatan; isi artikel tetap satu sumber data.</p>
    </div>

    <div class="admin-inline-actions">
      <span class="admin-counter">{{ $articles->total() }} artikel termasuk arsip</span>
      <a href="{{ route('admin.artikel.create') }}" class="admin-primary-action admin-primary-action--ghost">Tambah Artikel Eksternal</a>
      <form method="POST" action="{{ route('admin.artikel.canvas.start') }}" style="margin:0">
        @csrf
        <button type="submit" class="admin-primary-action">Buat via Canvas</button>
      </form>
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

  <section class="article-placement-grid" aria-label="Penempatan artikel homepage">
    <article class="article-placement-panel">
      <header class="article-placement-panel__head">
        <div>
          <span class="article-placement-panel__eyebrow">Homepage Article</span>
          <h2>1 Head + 3 Rail</h2>
          <p>Pin maksimal {{ $homepageLimit }} artikel. Urutan pin mendapat prioritas; slot kosong otomatis diisi artikel terbaru.</p>
        </div>
        <strong class="article-placement-panel__count">{{ $homepagePinnedArticles->count() }}/{{ $homepageLimit }}</strong>
      </header>

      <div class="article-placement-list" data-placement-sortable="homepage">
        @foreach($homepagePinnedArticles as $article)
          <div class="article-placement-item" draggable="true" data-placement-item data-article-id="{{ $article->getKey() }}">
            <button type="button" class="article-placement-handle" data-placement-handle aria-label="Seret {{ $article->admin_title }}">⋮⋮</button>
            <span class="article-placement-position">{{ $loop->iteration }}</span>
            <span class="article-placement-copy">
              <strong>{{ $article->admin_title }}</strong>
              <small>{{ $loop->first ? 'HEAD · PIN' : 'RAIL · PIN' }}</small>
            </span>
            <span class="article-placement-move">
              <button type="button" data-placement-move="up" aria-label="Naikkan urutan" @disabled($loop->first)>↑</button>
              <button type="button" data-placement-move="down" aria-label="Turunkan urutan" @disabled($loop->last)>↓</button>
            </span>
            <form method="POST" action="{{ route('admin.artikel.homepage.unpin', $article) }}">
              @csrf
              @method('DELETE')
              <button type="submit" class="admin-small-action admin-small-action--ghost">Lepas</button>
            </form>
          </div>
        @endforeach
      </div>

      @if($homepageAutoArticles->isNotEmpty())
        <div class="article-placement-auto" aria-label="Artikel pengisi otomatis">
          @foreach($homepageAutoArticles as $article)
            <div class="article-placement-item article-placement-item--auto">
              <span class="article-placement-handle" aria-hidden="true">·</span>
              <span class="article-placement-position">{{ $homepagePinnedArticles->count() + $loop->iteration }}</span>
              <span class="article-placement-copy">
                <strong>{{ $article->admin_title }}</strong>
                <small>{{ ($homepagePinnedArticles->count() + $loop->iteration) === 1 ? 'HEAD · AUTO' : 'RAIL · AUTO' }}</small>
              </span>
              <span class="article-placement-auto__badge">AUTO</span>
            </div>
          @endforeach
        </div>
      @endif

      @if($homepagePinnedArticles->isEmpty() && $homepageAutoArticles->isEmpty())
        <p class="article-placement-empty">Belum ada artikel publik. Homepage Article akan terisi otomatis setelah artikel pertama diterbitkan.</p>
      @endif

      <form method="POST" action="{{ route('admin.artikel.homepage.order') }}" data-placement-order-form="homepage" class="article-placement-order-form">
        @csrf
        @method('PATCH')
        <span data-placement-order-inputs>
          @foreach($homepagePinnedArticles as $article)
            <input type="hidden" name="article_ids[]" value="{{ $article->getKey() }}">
          @endforeach
        </span>
        <button type="submit" class="admin-small-action" data-placement-save disabled>Simpan urutan pin</button>
      </form>
    </article>

    <article class="article-placement-panel">
      <header class="article-placement-panel__head">
        <div>
          <span class="article-placement-panel__eyebrow">Hero Spotlight</span>
          <h2>Berita penting saja</h2>
          <p>Opening video selalu pertama. Tambahkan maksimal {{ $heroLimit }} artikel untuk prestasi, pengumuman besar, atau berita yang sedang panas.</p>
        </div>
        <strong class="article-placement-panel__count">{{ $heroPinnedArticles->count() }}/{{ $heroLimit }}</strong>
      </header>

      <div class="article-placement-item article-placement-item--fixed">
        <span class="article-placement-handle" aria-hidden="true">●</span>
        <span class="article-placement-position">0</span>
        <span class="article-placement-copy">
          <strong>Opening Video</strong>
          <small>FIXED · selalu pertama</small>
        </span>
        <a href="{{ route('admin.hero') }}" class="admin-small-action admin-small-action--ghost">Atur Opening</a>
      </div>

      <div class="article-placement-list" data-placement-sortable="hero">
        @foreach($heroPinnedArticles as $article)
          <div class="article-placement-item" draggable="true" data-placement-item data-article-id="{{ $article->getKey() }}">
            <button type="button" class="article-placement-handle" data-placement-handle aria-label="Seret {{ $article->admin_title }}">⋮⋮</button>
            <span class="article-placement-position">{{ $loop->iteration }}</span>
            <span class="article-placement-copy">
              <strong>{{ $article->admin_title }}</strong>
              <small>SPOTLIGHT · PIN</small>
            </span>
            <span class="article-placement-move">
              <button type="button" data-placement-move="up" aria-label="Naikkan urutan" @disabled($loop->first)>↑</button>
              <button type="button" data-placement-move="down" aria-label="Turunkan urutan" @disabled($loop->last)>↓</button>
            </span>
            <form method="POST" action="{{ route('admin.hero.articles.unpromote', $article) }}">
              @csrf
              @method('DELETE')
              <button type="submit" class="admin-small-action admin-small-action--ghost">Lepas</button>
            </form>
          </div>
        @endforeach
      </div>

      @if($heroPinnedArticles->isEmpty())
        <p class="article-placement-empty">Tidak ada Spotlight. Hero publik hanya menampilkan Opening video, dan itu sah-sah saja.</p>
      @endif

      <form method="POST" action="{{ route('admin.hero.articles.order') }}" data-placement-order-form="hero" class="article-placement-order-form">
        @csrf
        @method('PATCH')
        <span data-placement-order-inputs>
          @foreach($heroPinnedArticles as $article)
            <input type="hidden" name="article_ids[]" value="{{ $article->getKey() }}">
          @endforeach
        </span>
        <button type="submit" class="admin-small-action" data-placement-save disabled>Simpan urutan Spotlight</button>
      </form>
    </article>
  </section>

  <section class="admin-gallery-block" aria-label="Daftar artikel">
    @if($articles->isNotEmpty())
      <div class="gallery-lite-list">
        @foreach($articleRows as ['article' => $article, 'isDeleted' => $isDeleted, 'replacementCandidates' => $replacementCandidates, 'statusLabel' => $statusLabel, 'statusClass' => $statusClass])
          @php($canPlace = ! $isDeleted && $article->isPubliclyVisibleNow())
          <article class="gallery-lite-row article-admin-row {{ $isDeleted ? 'is-deleted' : '' }}">
            <span class="gallery-lite-row__order">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>

            <span class="gallery-lite-row__body">
              <strong>{{ $article->admin_title }}</strong>
              <small>
                {{ $article->authorForDisplay() }}
                · {{ $article->published_at ? $article->published_at->translatedFormat('d M Y, H:i').' WIB' : '-' }}
                · {{ $article->isNative() ? 'Canvas internal' : (parse_url($article->link_id, PHP_URL_HOST) ?: 'eksternal') }}
                @if($isDeleted && $article->deleted_at)
                  · dihapus {{ $article->deleted_at->translatedFormat('d M Y, H:i') }} WIB
                @endif
              </small>
            </span>

            <span class="gallery-lite-status {{ $statusClass }}">
              {{ $statusLabel }}
            </span>

            <span class="article-admin-placement-actions">
              @if(! $isDeleted)
                @if($article->homepage_position !== null)
                  <form method="POST" action="{{ route('admin.artikel.homepage.unpin', $article) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="admin-small-action admin-small-action--pinned">Homepage #{{ $article->homepage_position }}</button>
                  </form>
                @else
                  <form method="POST" action="{{ route('admin.artikel.homepage.pin', $article) }}">
                    @csrf
                    <button type="submit" class="admin-small-action admin-small-action--ghost" @disabled(! $canPlace || $homepagePinnedArticles->count() >= $homepageLimit)>+ Homepage</button>
                  </form>
                @endif

                @if($article->hero_position !== null)
                  <form method="POST" action="{{ route('admin.hero.articles.unpromote', $article) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="admin-small-action admin-small-action--spotlight">Spotlight #{{ $article->hero_position }}</button>
                  </form>
                @else
                  <form method="POST" action="{{ route('admin.hero.articles.promote') }}">
                    @csrf
                    <input type="hidden" name="article_id" value="{{ $article->getKey() }}">
                    <button type="submit" class="admin-small-action admin-small-action--ghost" @disabled(! $canPlace || $heroPinnedArticles->count() >= $heroLimit)>+ Spotlight</button>
                  </form>
                @endif

                @if(! $canPlace)
                  <small class="article-admin-placement-note">Terbitkan dulu untuk placement publik.</small>
                @endif
              @endif
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
                <a href="{{ $article->linkForLocale('id') }}" target="_blank" rel="noopener" class="admin-small-action admin-small-action--ghost">Preview</a>
                <a href="{{ $article->isNative() ? route('admin.artikel.canvas.edit', $article) : route('admin.artikel.edit', $article) }}" class="admin-small-action">{{ $article->isNative() ? 'Canvas' : 'Edit' }}</a>

                <form method="POST" action="{{ route('admin.artikel.destroy', $article) }}" data-admin-delete-form data-admin-delete-message="Arsipkan artikel ini? Artikel akan dilepas dari Homepage dan Hero, tetapi data tetap dapat dipulihkan.">
                  @csrf
                  @method('DELETE')
                  <button type="button" data-admin-delete-trigger class="admin-small-action admin-small-action--danger">Arsipkan</button>
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
        <p>Buat artikel melalui Canvas atau tambahkan artikel eksternal.</p>
      </div>
    @endif
  </section>

  @include('admin.articles.index-placement-behavior')
@endsection
