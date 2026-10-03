    <div class="canvas-publish-drawer" data-publish-drawer hidden>
      <button type="button" class="canvas-publish-drawer__backdrop" data-publish-close aria-label="Tutup publish"></button>
      <aside class="canvas-publish-drawer__panel" role="dialog" aria-modal="true" aria-labelledby="publish-title">
        <button type="button" class="canvas-publish-drawer__close" data-publish-close aria-label="Tutup">×</button>
        <span class="canvas-dialog__eyebrow">Siap diterbitkan?</span>
        <h2 id="publish-title">Preview artikel</h2>

        <article class="canvas-preview-card">
          <div class="canvas-preview-card__image" data-preview-image>
            <img src="{{ $article->thumbnail_url ?: \App\Models\Article::PLACEHOLDER_THUMBNAIL }}" alt="">
            <button type="button" data-thumbnail-change>Ganti thumbnail</button>
          </div>
          <strong data-preview-title>{{ $article->admin_title }}</strong>
          <span data-preview-subtitle>{{ $article->subtitle_id }}</span>
          <div class="canvas-preview-card__meta">
            <span data-preview-author>{{ $article->authorForDisplay() }}</span>
            <time data-preview-date datetime="{{ $article->published_at?->toIso8601String() }}">{{ $article->published_at?->translatedFormat('j M Y') }}</time>
            <span data-preview-reading>{{ max(1, (int) ceil(max(1, $article->word_count) / 220)) }} menit baca</span>
          </div>
        </article>

        <label class="canvas-publish-field">
          <span>Author</span>
          <input type="text" data-publish-author maxlength="120" value="{{ $article->authorForDisplay() }}" placeholder="Nama penulis">
        </label>

        <div class="canvas-publish-field">
          <span>Kategori <small>maksimal 5; pilihan lama akan disarankan otomatis</small></span>
          <div class="canvas-category-editor" data-category-editor>
            <div class="canvas-category-chips" data-category-chips></div>
            <input type="text" data-category-input maxlength="40" autocomplete="off" placeholder="Ketik kategori lalu Enter">
            <div class="canvas-category-suggestions" data-category-suggestions hidden></div>
          </div>
        </div>

        <script type="application/json" data-category-data>{!! json_encode([
          'selected' => array_values($article->tags ?? []),
          'suggestions' => array_values($categorySuggestions ?? []),
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>

        <fieldset class="canvas-publish-schedule">
          <legend>Waktu terbit</legend>
          <label><input type="radio" name="publish_mode" value="now" @checked($article->article_status !== \App\Models\Article::STATUS_SCHEDULED)> Terbitkan sekarang</label>
          <label><input type="radio" name="publish_mode" value="schedule" @checked($article->article_status === \App\Models\Article::STATUS_SCHEDULED)> Jadwalkan</label>
          <label class="canvas-publish-date">
            <span data-publish-date-label>{{ $article->article_status === \App\Models\Article::STATUS_SCHEDULED ? 'Jadwal publikasi' : 'Tanggal publikasi' }}</span>
            <input type="datetime-local" data-publish-at value="{{ ($article->published_at ?: now())->format('Y-m-d\TH:i') }}">
          </label>
        </fieldset>

        <p class="canvas-publish-error" data-publish-error hidden></p>
        <button type="button" class="canvas-publish-submit" data-publish-submit>{{ $article->article_status === \App\Models\Article::STATUS_SCHEDULED ? 'Schedule to publish' : 'Publish now' }}</button>
      </aside>
    </div>
