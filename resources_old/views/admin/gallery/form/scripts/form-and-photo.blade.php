    (() => {
      const form = document.querySelector('[data-gallery-video-form]');
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

      const typeInput = form.querySelector('[data-gallery-type]');
      const photoField = form.querySelector('[data-gallery-photo-field]');
      const videoField = form.querySelector('[data-gallery-video-field]');
      const photoInput = form.querySelector('[data-gallery-photo-input]');
      const videoInput = form.querySelector('[data-gallery-video-url]');
      const stage = form.querySelector('[data-gallery-preview-stage]');
      const emptyText = @json($form['review_empty']);
      const layoutClasses = ['is-landscape', 'is-portrait', 'is-square', 'is-image', 'is-empty'];
      let previewUrl = null;

      function clearPreviewUrl() {
        if (previewUrl) {
          URL.revokeObjectURL(previewUrl);
          previewUrl = null;
        }
      }

      function setEmpty() {
        if (!stage) return;

        const placeholder = document.createElement('span');
        placeholder.textContent = emptyText;
        setStageLayout('is-empty');
        stage.replaceChildren(placeholder);
      }

      function setStageLayout(layoutClass) {
        stage.classList.remove(...layoutClasses);
        stage.classList.add(layoutClass);
      }

      function facebookVideoId(url) {
        try {
          const parsed = new URL(url);
          const host = parsed.hostname.toLowerCase();
          const hostMatches = host === 'facebook.com' || host.endsWith('.facebook.com');

          if (parsed.protocol !== 'https:' || !hostMatches) return '';

          const reelMatch = parsed.pathname.match(/^\/reel\/(\d+)\/?$/);
          if (reelMatch) return reelMatch[1];

          const watchId = parsed.searchParams.get('v') || '';
          return /^\/watch\/?$/.test(parsed.pathname) && /^\d+$/.test(watchId)
            ? watchId
            : '';
        } catch {
          return '';
        }
      }

      function facebookEmbedUrl(videoId) {
        const reelUrl = `https://www.facebook.com/reel/${videoId}/`;
        const params = new URLSearchParams({
          height: '476',
          href: reelUrl,
          show_text: 'false',
          width: '267',
          t: '0',
        });

        return `https://www.facebook.com/plugins/video.php?${params.toString()}`;
      }

      function updateFields({ clear = false } = {}) {
        const isVideo = typeInput.value === 'video';

        photoField.hidden = isVideo;
        videoField.hidden = !isVideo;

        photoInput.disabled = isVideo;
        videoInput.disabled = !isVideo;

        if (clear) {
          if (isVideo) photoInput.value = '';
          if (!isVideo) videoInput.value = '';
          clearPreviewUrl();
          setEmpty();
          return;
        }

        if (isVideo && videoInput.value.trim()) {
          const embedUrl = toPreviewUrl(videoInput.value);
          setStageLayout(embedUrl ? mediaLayoutClass(embedUrl) : 'is-empty');
        } else if (!isVideo && stage.querySelector('img')) {
          setStageLayout('is-image');
        } else {
          setStageLayout('is-empty');
        }
      }

      function previewPhoto(file) {
        clearPreviewUrl();

        if (!file) {
          setEmpty();
          return;
        }

        if (!file.type.startsWith('image/')) {
          setEmpty();
          return;
        }

        previewUrl = URL.createObjectURL(file);

        const img = document.createElement('img');
        img.alt = file.name;
        img.src = previewUrl;

        setStageLayout('is-image');
        stage.replaceChildren(img);
      }
