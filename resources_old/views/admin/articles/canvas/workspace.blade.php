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
