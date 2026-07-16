@extends('layouts.article-canvas', ['title' => ($article->admin_title ?: 'Artikel baru').' · Canvas'])

@section('content')
  <div
    class="article-canvas-app"
    data-article-canvas
    data-autosave-url="{{ route('admin.artikel.canvas.autosave', $article) }}"
    data-upload-url="{{ route('admin.artikel.canvas.image', $article) }}"
    data-unsplash-url="{{ route('admin.artikel.canvas.unsplash') }}"
    data-publish-url="{{ route('admin.artikel.canvas.publish', $article) }}"
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

    <div class="canvas-format-bar" data-format-bar role="toolbar" aria-label="Format artikel">
      <button type="button" data-format="paragraph" title="Paragraf">¶</button>
      <button type="button" data-format="h2" title="Heading besar">T</button>
      <button type="button" data-format="h3" title="Heading kecil">t</button>
      <span aria-hidden="true"></span>
      <button type="button" data-format="bold" title="Bold"><strong>B</strong></button>
      <button type="button" data-format="italic" title="Italic"><em>i</em></button>
      <button type="button" data-format="strike" title="Coret"><s>S</s></button>
      <button type="button" data-format="highlight" title="Highlight">▣</button>
      <button type="button" data-format="link" title="Link">↗</button>
      <span aria-hidden="true"></span>
      <button type="button" data-format="small" title="Teks kecil">A−</button>
      <button type="button" data-format="large" title="Teks besar">A+</button>
      <button type="button" data-format="align-left" title="Rata kiri">⇤</button>
      <button type="button" data-format="align-center" title="Tengah">≡</button>
      <button type="button" data-format="align-right" title="Rata kanan">⇥</button>
      <button type="button" data-format="quote" title="Quote / Pull quote">“</button>
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
          <button type="button" data-insert="image" title="Upload gambar">▧</button>
          <button type="button" data-insert="unsplash" title="Cari Unsplash">⌕</button>
          <button type="button" data-insert="video" title="Sematkan video">▶</button>
          <button type="button" data-insert="embed" title="Sematkan media">&lt;&gt;</button>
          <button type="button" data-insert="code" title="Blok kode">{ }</button>
          <button type="button" data-insert="divider" title="Pemisah">•••</button>
          <button type="button" data-insert="dropcap" title="Drop cap">D</button>
        </div>
      </div>
    </main>

    <div class="canvas-inline-toolbar" data-inline-toolbar role="toolbar" aria-label="Format teks" hidden>
      <button type="button" data-format="paragraph" aria-label="Paragraph">¶</button>
      <button type="button" data-format="bold" aria-label="Bold"><strong>B</strong></button>
      <button type="button" data-format="italic" aria-label="Italic"><em>i</em></button>
      <button type="button" data-format="strike" aria-label="Coret"><s>S</s></button>
      <button type="button" data-format="highlight" aria-label="Highlight">▣</button>
      <button type="button" data-format="link" aria-label="Link">↗</button>
      <span aria-hidden="true"></span>
      <button type="button" data-format="h2" aria-label="Title">T</button>
      <button type="button" data-format="h3" aria-label="Subtitle">t</button>
      <button type="button" data-format="small" aria-label="Teks kecil">A−</button>
      <button type="button" data-format="large" aria-label="Teks besar">A+</button>
      <button type="button" data-format="quote" aria-label="Quote">“</button>
    </div>

    <div class="canvas-link-input" data-link-input hidden>
      <input type="url" placeholder="Paste or type a link…" aria-label="URL tautan">
    </div>

    <div class="canvas-image-toolbar" data-image-toolbar hidden>
      <button type="button" data-image-layout="inline">In-line</button>
      <button type="button" data-image-layout="outset">Out-set</button>
      <button type="button" data-image-layout="screen">Screen-width</button>
      <button type="button" data-image-alt>Alt text</button>
    </div>

    <input type="file" accept="image/jpeg,image/png,image/webp" data-image-file hidden>

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
          </div>
          <strong data-preview-title>{{ $article->admin_title }}</strong>
          <span data-preview-subtitle>{{ $article->subtitle_id }}</span>
        </article>

        <label class="canvas-publish-field">
          <span>Tags <small>maksimal 5, pisahkan dengan koma</small></span>
          <input type="text" data-publish-tags value="{{ implode(', ', $article->tags ?? []) }}" placeholder="Sekolah, Pendidikan">
        </label>

        <fieldset class="canvas-publish-schedule">
          <legend>Waktu terbit</legend>
          <label><input type="radio" name="publish_mode" value="now" checked> Terbitkan sekarang</label>
          <label><input type="radio" name="publish_mode" value="schedule"> Jadwalkan</label>
          <input type="datetime-local" data-scheduled-at hidden>
        </fieldset>

        <p class="canvas-publish-error" data-publish-error hidden></p>
        <button type="button" class="canvas-publish-submit" data-publish-submit>Publish now</button>
      </aside>
    </div>
  </div>
@endsection
