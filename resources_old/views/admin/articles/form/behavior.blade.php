  <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
    (() => {
      const form = document.querySelector('[data-article-form]');
      if (!form) return;

      const tabsRoot = form.querySelector('[data-language-tabs]');
      if (tabsRoot) {
        const tabs = [...tabsRoot.querySelectorAll('[data-language-tab]')];
        const panels = [...tabsRoot.querySelectorAll('[data-language-panel]')];

        function activateLanguage(locale) {
          tabs.forEach((tab) => {
            const isActive = tab.dataset.languageTab === locale;
            tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
            tab.classList.toggle('admin-primary-action--ghost', !isActive);
          });

          panels.forEach((panel) => {
            panel.hidden = panel.dataset.languagePanel !== locale;
          });
        }

        tabs.forEach((tab) => {
          tab.addEventListener('click', () => activateLanguage(tab.dataset.languageTab));
        });
      }

      const input = form.querySelector('[data-article-thumbnail-input]');
      const stage = form.querySelector('[data-article-thumbnail-stage]');
      const emptyText = 'Thumbnail baru akan tampil setelah dipilih.';
      let previewUrl = null;

      if (!input || !stage) return;

      function revokePreviewUrl() {
        if (previewUrl) {
          URL.revokeObjectURL(previewUrl);
          previewUrl = null;
        }
      }

      function setEmpty(message = emptyText) {
        revokePreviewUrl();
        const span = document.createElement('span');
        span.textContent = message;
        span.setAttribute('data-article-thumbnail-empty', '');

        stage.replaceChildren(span);
      }

      function setImage(file) {
        revokePreviewUrl();

        previewUrl = URL.createObjectURL(file);

        const image = document.createElement('img');
        image.src = previewUrl;
        image.alt = file.name || 'Preview thumbnail artikel';
        image.loading = 'eager';

        stage.replaceChildren(image);
      }

      input.addEventListener('change', () => {
        const file = input.files && input.files[0] ? input.files[0] : null;

        if (!file) {
          setEmpty();
          return;
        }

        if (!file.type || !file.type.startsWith('image/')) {
          input.value = '';
          setEmpty('File harus berupa gambar.');
          return;
        }

        setImage(file);
      });

      window.addEventListener('beforeunload', revokePreviewUrl);
    })();
  </script>
