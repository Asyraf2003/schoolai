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
