@extends('layouts.article-canvas', ['title' => ($article->admin_title ?: 'Artikel baru').' · Canvas'])

@section('content')
  <div
    class="article-canvas-app"
    data-article-canvas
    data-autosave-url="{{ route('admin.artikel.canvas.autosave', $article) }}"
    data-upload-url="{{ route('admin.artikel.canvas.image', $article) }}"
    data-unsplash-url="{{ route('admin.artikel.canvas.unsplash') }}"
    data-publish-url="{{ route('admin.artikel.canvas.publish', $article) }}"
    data-thumbnail-url="{{ $article->thumbnail_url ?: \App\Models\Article::PLACEHOLDER_THUMBNAIL }}"
  >
    <header class="canvas-topbar">
      <div class="canvas-topbar__left">
        <a href="{{ route('admin.artikel') }}" class="canvas-brand" aria-label="Kembali ke admin artikel">AM</a>
        <span class="canvas-divider" aria-hidden="true"></span>
        <span class="canvas-save-state" data-save-state>Draft · Tersimpan</span>
      </div>

      <div class="canvas-topbar__right">
        <div class="canvas-language-switch" role="group" aria-label="Bahasa artikel">
          <button type="button" class="is-active" data-language="id" aria-pressed="true">ID</button>
          <button type="button" data-language="en" aria-pressed="false">EN</button>
        </div>
        <button type="button" class="canvas-count" data-count-toggle aria-expanded="false">0 kata</button>
        <button type="button" class="canvas-publish-button" data-publish-open>Publish</button>
      </div>
    </header>

    <div class="canvas-count-popover" data-count-popover hidden>
      <strong data-word-count>0 kata</strong>
      <span data-character-count>0 karakter</span>
    </div>

    <main class="canvas-workspace">
      <section class="canvas-document is-active" data-document-language="id" aria-label="Canvas artikel Indonesia">
        <textarea
          class="canvas-title"
          data-title
          rows="1"
          maxlength="200"
          placeholder="Title"
          aria-label="Judul artikel Indonesia"
        >{{ $article->title_id }}</textarea>
        <textarea
          class="canvas-subtitle"
          data-subtitle
          rows="1"
          maxlength="300"
          placeholder="Subtitle (optional)"
          aria-label="Subjudul artikel Indonesia"
        >{{ $article->subtitle_id }}</textarea>
        <div
          class="canvas-body"
          data-editor
          contenteditable="true"
          role="textbox"
          aria-multiline="true"
          data-placeholder="Tell your story..."
          spellcheck="true"
        >{!! $article->content_id !!}</div>
      </section>

      <section class="canvas-document" data-document-language="en" aria-label="English article canvas" hidden>
        <textarea
          class="canvas-title"
          data-title
          rows="1"
          maxlength="200"
          placeholder="Title"
          aria-label="English article title"
        >{{ $article->title_en }}</textarea>
        <textarea
          class="canvas-subtitle"
          data-subtitle
          rows="1"
          maxlength="300"
          placeholder="Subtitle (optional)"
          aria-label="English article subtitle"
        >{{ $article->subtitle_en }}</textarea>
        <div
          class="canvas-body"
          data-editor
          contenteditable="true"
          role="textbox"
          aria-multiline="true"
          data-placeholder="Tell your story..."
          spellcheck="true"
        >{!! $article->content_en !!}</div>
      </section>

      <div class="canvas-block-menu" data-block-menu hidden>
        <button type="button" class="canvas-plus" data-block-toggle aria-label="Tambahkan blok" aria-expanded="false">+</button>
        <div class="canvas-block-actions" data-block-actions hidden>
          <div class="canvas-block-actions__group">
            <span>Jenis blok</span>
            <div>
              <button type="button" data-format="paragraph" title="Paragraf">P</button>
              <button type="button" data-format="h2" title="Heading besar">H2</button>
              <button type="button" data-format="h3" title="Heading kecil">H3</button>
              <button type="button" data-format="quote" title="Quote / pull quote">“</button>
              <button type="button" data-insert="dropcap" title="Drop cap">D</button>
            </div>
          </div>

          <div class="canvas-block-actions__group">
            <span>Ukuran &amp; posisi</span>
            <div>
              <button type="button" data-format="small" title="Teks kecil">A−</button>
              <button type="button" data-format="large" title="Teks besar">A+</button>
              <button type="button" data-format="align-left" title="Rata kiri">⇤</button>
              <button type="button" data-format="align-center" title="Rata tengah">≡</button>
              <button type="button" data-format="align-right" title="Rata kanan">⇥</button>
              <button type="button" data-format="align-justify" title="Rata kanan-kiri">☰</button>
            </div>
          </div>

          <div class="canvas-block-actions__group canvas-block-colors">
            <span>Warna teks</span>
            <div>
              <button type="button" class="color-default" data-block-color="default" title="Warna default">A</button>
              <button type="button" class="color-muted" data-block-color="muted" title="Abu-abu"></button>
              <button type="button" class="color-green" data-block-color="green" title="Hijau"></button>
              <button type="button" class="color-blue" data-block-color="blue" title="Biru"></button>
              <button type="button" class="color-red" data-block-color="red" title="Merah"></button>
              <button type="button" class="color-amber" data-block-color="amber" title="Jingga"></button>
            </div>
          </div>

          <div class="canvas-block-actions__group canvas-block-colors">
            <span>Latar blok</span>
            <div>
              <button type="button" class="bg-default" data-block-background="default" title="Tanpa latar">×</button>
              <button type="button" class="bg-gray" data-block-background="gray" title="Abu-abu"></button>
              <button type="button" class="bg-yellow" data-block-background="yellow" title="Kuning"></button>
              <button type="button" class="bg-green" data-block-background="green" title="Hijau"></button>
              <button type="button" class="bg-blue" data-block-background="blue" title="Biru"></button>
              <button type="button" class="bg-rose" data-block-background="rose" title="Merah muda"></button>
            </div>
          </div>

          <div class="canvas-block-actions__group">
            <span>Sisipkan</span>
            <div>
              <button type="button" data-insert="image" title="Upload gambar">▧</button>
              <button type="button" data-insert="unsplash" title="Cari Unsplash">⌕</button>
              <button type="button" data-insert="video" title="Sematkan video">▶</button>
              <button type="button" data-insert="embed" title="Sematkan media">&lt;&gt;</button>
              <button type="button" data-insert="code" title="Blok kode">{ }</button>
              <button type="button" data-insert="divider" title="Pemisah">•••</button>
            </div>
          </div>
        </div>
      </div>
    </main>

    <div class="canvas-inline-toolbar" data-inline-toolbar role="toolbar" aria-label="Format teks" hidden>
      <button type="button" data-format="bold" aria-label="Bold"><strong>B</strong></button>
      <button type="button" data-format="italic" aria-label="Italic"><em>i</em></button>
      <button type="button" data-format="strike" aria-label="Coret"><s>S</s></button>
      <button type="button" data-format="highlight" aria-label="Highlight">▣</button>
      <button type="button" data-format="link" aria-label="Link">↗</button>
      <button type="button" class="canvas-inline-color-button color-default" data-text-color="default" title="Warna default">A</button>
      <button type="button" class="canvas-inline-color-button color-muted" data-text-color="muted" title="Abu-abu"></button>
      <button type="button" class="canvas-inline-color-button color-green" data-text-color="green" title="Hijau"></button>
      <button type="button" class="canvas-inline-color-button color-blue" data-text-color="blue" title="Biru"></button>
      <button type="button" class="canvas-inline-color-button color-red" data-text-color="red" title="Merah"></button>
      <button type="button" class="canvas-inline-color-button color-amber" data-text-color="amber" title="Jingga"></button>
      <span aria-hidden="true"></span>
      <button type="button" data-format="paragraph" aria-label="Paragraf">P</button>
      <button type="button" data-format="h2" aria-label="Heading besar">H2</button>
      <button type="button" data-format="h3" aria-label="Heading kecil">H3</button>
      <button type="button" data-format="align-left" aria-label="Rata kiri">⇤</button>
      <button type="button" data-format="align-center" aria-label="Rata tengah">≡</button>
      <button type="button" data-format="align-right" aria-label="Rata kanan">⇥</button>
      <button type="button" data-format="align-justify" aria-label="Rata kanan-kiri">☰</button>
    </div>

    <div class="canvas-link-input" data-link-input hidden>
      <input type="url" placeholder="Paste or type a link…" aria-label="URL tautan">
    </div>

    <div class="canvas-image-toolbar" data-image-toolbar hidden>
      <button type="button" data-image-layout="compact">Compact</button>
      <button type="button" data-image-layout="inline">In-line</button>
      <button type="button" data-image-layout="outset">Out-set</button>
      <button type="button" data-image-layout="screen">Screen-width</button>
      <span aria-hidden="true"></span>
      <button type="button" data-image-align="left" title="Gambar rata kiri">⇤</button>
      <button type="button" data-image-align="center" title="Gambar rata tengah">≡</button>
      <button type="button" data-image-align="right" title="Gambar rata kanan">⇥</button>
      <span aria-hidden="true"></span>
      <button type="button" data-image-alt>Alt text</button>
      <button type="button" data-image-thumbnail>Jadikan thumbnail</button>
      <button type="button" data-image-replace>Ganti</button>
      <button type="button" data-image-continue>Tulis di bawah</button>
      <button type="button" data-image-delete aria-label="Hapus gambar">×</button>
    </div>

    <div class="canvas-code-toolbar" data-code-toolbar hidden>
      <span>Code block</span>
      <button type="button" data-code-exit>+ Paragraf di bawah</button>
    </div>

    <input type="file" accept="image/jpeg,image/png,image/webp" data-image-file hidden>
    <input type="file" accept="image/jpeg,image/png,image/webp" data-thumbnail-file hidden>

    <div class="canvas-dialog" data-url-dialog hidden>
      <button type="button" class="canvas-dialog__backdrop" data-dialog-close aria-label="Tutup"></button>
      <section class="canvas-dialog__panel" role="dialog" aria-modal="true" aria-labelledby="canvas-url-title">
        <span class="canvas-dialog__eyebrow" data-url-eyebrow>Sematkan media</span>
        <h2 id="canvas-url-title" data-url-title>Tempel URL</h2>
        <p data-url-help>YouTube dan Vimeo akan menjadi player. Spotify dan CodePen didukung untuk embed.</p>
        <input type="url" data-url-value placeholder="https://…">
        <p class="canvas-dialog__error" data-url-error hidden></p>
        <div class="canvas-dialog__actions">
          <button type="button" data-dialog-close>Batal</button>
          <button type="button" class="is-primary" data-url-apply>Sematkan</button>
        </div>
      </section>
    </div>

    <div class="canvas-dialog" data-unsplash-dialog hidden>
      <button type="button" class="canvas-dialog__backdrop" data-unsplash-close aria-label="Tutup"></button>
      <section class="canvas-dialog__panel canvas-dialog__panel--wide" role="dialog" aria-modal="true" aria-labelledby="unsplash-title">
        <span class="canvas-dialog__eyebrow">Unsplash</span>
        <h2 id="unsplash-title">Cari gambar stock</h2>
        <form class="canvas-unsplash-search" data-unsplash-form>
          <input type="search" data-unsplash-query placeholder="Cari: ruang kelas, anak belajar…" required>
          <button type="submit">Cari</button>
        </form>
        <p class="canvas-dialog__error" data-unsplash-error hidden></p>
        <div class="canvas-unsplash-grid" data-unsplash-results></div>
        <div class="canvas-dialog__actions">
          <button type="button" data-unsplash-close>Tutup</button>
        </div>
      </section>
    </div>

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
  </div>
@endsection
