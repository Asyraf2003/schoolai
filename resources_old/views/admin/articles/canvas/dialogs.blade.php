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
