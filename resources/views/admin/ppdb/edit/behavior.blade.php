  <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
    (() => {
      const form = document.querySelector('[data-ppdb-showcase-form]');
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

      const typeSelect = form.querySelector('[data-ppdb-media-type]');
      const photoBox = form.querySelector('[data-ppdb-media-photo]');
      const urlBox = form.querySelector('[data-ppdb-media-url]');
      const photoInput = form.querySelector('input[name="media_file"]');
      const urlInput = form.querySelector('input[name="media_url"]');

      const syncMediaInputs = () => {
        const isUrl = typeSelect.value === 'video';

        photoBox.hidden = isUrl;
        urlBox.hidden = !isUrl;
        photoInput.disabled = isUrl;
        urlInput.disabled = !isUrl;
      };

      typeSelect.addEventListener('change', syncMediaInputs);
      syncMediaInputs();
    })();
  </script>
