  <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
    (() => {
      document.querySelectorAll('[data-language-tabs]').forEach((tabsRoot) => {
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
      });

      const createDetails = document.getElementById('stats-create');
      const createTrigger = document.querySelector('[data-open-stat-create]');

      if (createDetails && createTrigger) {
        createTrigger.addEventListener('click', () => {
          createDetails.open = true;
          createDetails.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
      }
    })();
  </script>
