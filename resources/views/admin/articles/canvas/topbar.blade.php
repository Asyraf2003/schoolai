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
