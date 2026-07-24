    function toPreviewUrl(url) {
      try {
        const parsed = new URL(url.trim());
        const host = parsed.hostname.toLowerCase();
        const hostMatches = (domain) => host === domain || host.endsWith(`.${domain}`);

        if (hostMatches('youtu.be')) {
          const id = parsed.pathname.split('/').filter(Boolean)[0];
          return id ? `https://www.youtube.com/embed/${encodeURIComponent(id)}` : '';
        }

        if (hostMatches('youtube.com')) {
          const id = parsed.searchParams.get('v');
          if (id) return `https://www.youtube.com/embed/${encodeURIComponent(id)}`;

          const parts = parsed.pathname.split('/').filter(Boolean);
          const marker = parts.findIndex((part) => part === 'shorts' || part === 'embed');
          if (marker >= 0 && parts[marker + 1]) {
            return `https://www.youtube.com/embed/${encodeURIComponent(parts[marker + 1])}`;
          }
        }

        if (hostMatches('tiktok.com')) {
          const match = parsed.pathname.match(/(?:\/video\/|\/(?:player\/v1|embed\/v2)\/)(\d+)/);
          return match ? `https://www.tiktok.com/player/v1/${match[1]}` : '';
        }

        if (hostMatches('instagram.com')) {
          const match = parsed.pathname.match(/^\/(p|reel|tv)\/([^/]+)/);
          return match ? `https://www.instagram.com/${match[1]}/${encodeURIComponent(match[2])}/embed` : '';
        }

        if (hostMatches('facebook.com') && parsed.protocol === 'https:') {
          const sourceUrl = parsed.pathname === '/plugins/video.php'
            ? parsed.searchParams.get('href') || ''
            : parsed.toString();
          const videoId = facebookVideoId(sourceUrl);

          return videoId ? facebookEmbedUrl(videoId) : '';
        }

        if (hostMatches('vimeo.com')) {
          const match = parsed.pathname.match(/(?:\/video)?\/(\d+)/);
          return match ? `https://player.vimeo.com/video/${match[1]}` : '';
        }

        return '';
      } catch {
        return '';
      }
    }

    function mediaLayoutClass(url) {
      try {
        const parsed = new URL(url);
        const host = parsed.hostname.toLowerCase().replace(/^www\./, '');

        if (host === 'facebook.com' || host === 'tiktok.com') return 'is-portrait';

        if (host === 'instagram.com') {
          return /^\/p\//.test(parsed.pathname) ? 'is-square' : 'is-portrait';
        }

        return 'is-landscape';
      } catch {
        return 'is-landscape';
      }
    }

    function previewVideos(value) {
      clearPreviewUrls();

      const urls = isBulk
        ? value.split(/\n+/).map((url) => url.trim()).filter(Boolean)
        : [value.trim()].filter(Boolean);

      const embedUrls = urls.map(toPreviewUrl).filter(Boolean);

      if (!embedUrls.length) {
        setEmpty(isBulk ? 'Belum ada URL valid untuk preview.' : undefined);
        return;
      }

      const wrap = document.createElement('div');
      wrap.className = 'gallery-media-review__multi';

      embedUrls.slice(0, 6).forEach((url) => {
        const iframe = document.createElement('iframe');
        iframe.classList.add('gallery-media-review__video', mediaLayoutClass(url));
        iframe.src = url;
        iframe.title = 'Preview video';
        iframe.loading = 'lazy';
        iframe.allow = 'autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture; web-share';
        iframe.allowFullscreen = true;
        iframe.referrerPolicy = 'strict-origin-when-cross-origin';
        wrap.appendChild(iframe);
      });

      setStageLayout('is-video-list');
      stage.replaceChildren(wrap);

      if (countText) {
        countText.textContent = `${embedUrls.length} URL valid${embedUrls.length > 6 ? ' · preview 6 pertama' : ''}`;
      }
    }

    typeInput.addEventListener('change', () => updateFields({ clear: true }));
    photoInput.addEventListener('change', () => previewPhotos(photoInput.files));
    videoInput.addEventListener('input', () => previewVideos(videoInput.value));

    updateFields();
  })();
