  (() => {
    const form = document.querySelector('[data-gallery-page-media-form]');
    if (!form) return;

    const isBulk = form.hasAttribute('data-gallery-page-media-bulk');
    const typeInput = form.querySelector('[data-gallery-page-media-type]');
    const photoField = form.querySelector('[data-gallery-page-media-photo-field]');
    const videoField = form.querySelector('[data-gallery-page-media-video-field]');
    const photoInput = form.querySelector('[data-gallery-page-media-photo-input]');
    const videoInput = form.querySelector('[data-gallery-page-media-video-url]');
    const stage = form.querySelector('[data-gallery-page-media-preview-stage]');
    const countText = form.querySelector('[data-gallery-page-media-count]');
    const layoutClasses = ['is-landscape', 'is-portrait', 'is-square', 'is-image', 'is-video-list', 'is-empty'];
    let previewUrls = [];

    function clearPreviewUrls() {
      previewUrls.forEach((url) => URL.revokeObjectURL(url));
      previewUrls = [];
    }

    function setEmpty(message = 'Pilih foto atau tempel URL video untuk preview.') {
      if (!stage) return;

      const placeholder = document.createElement('span');
      placeholder.textContent = message;
      setStageLayout('is-empty');
      stage.replaceChildren(placeholder);

      if (countText) countText.textContent = '';
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
        photoInput.value = '';
        videoInput.value = '';
        clearPreviewUrls();
        setEmpty(isBulk ? 'Pilih banyak foto atau tempel banyak URL embed.' : undefined);
        return;
      }

      if (isVideo && videoInput.value.trim()) {
        const embedUrl = toPreviewUrl(videoInput.value);
        const currentIframe = stage.querySelector('iframe');

        if (embedUrl && currentIframe) {
          currentIframe.classList.add('gallery-media-review__video', mediaLayoutClass(embedUrl));
          setStageLayout('is-video-list');
        } else {
          setStageLayout(embedUrl ? 'is-video-list' : 'is-empty');
        }
      } else if (!isVideo && stage.querySelector('img')) {
        setStageLayout('is-image');
      } else {
        setStageLayout('is-empty');
      }
    }

    function previewPhotos(files) {
      clearPreviewUrls();

      const validFiles = Array.from(files || []).filter((file) => file.type.startsWith('image/'));

      if (!validFiles.length) {
        setEmpty(isBulk ? 'Belum ada foto dipilih.' : undefined);
        return;
      }

      const wrap = document.createElement('div');
      wrap.className = 'gallery-media-review__multi';

      validFiles.slice(0, 12).forEach((file) => {
        const url = URL.createObjectURL(file);
        previewUrls.push(url);

        const img = document.createElement('img');
        img.alt = file.name;
        img.src = url;
        wrap.appendChild(img);
      });

      setStageLayout('is-image');
      stage.replaceChildren(wrap);

      if (countText) {
        countText.textContent = `${validFiles.length} foto dipilih${validFiles.length > 12 ? ' · preview 12 pertama' : ''}`;
      }
    }
