@php
  $isEdit = $mode === 'edit';
  $action = $isEdit ? route('admin.galeri.section-media.update', $item) : route('admin.galeri.section-media.store', $section);
  $publishedAtValue = old('published_at', optional($item->published_at)->format('Y-m-d\TH:i'));
  $currentType = old('type', $item->type ?: 'photo');
  $isVideo = $currentType === 'video';
@endphp

<form
  method="POST"
  action="{{ $action }}"
  class="gallery-lite-form"
  enctype="multipart/form-data"
  data-gallery-page-media-form
  @if(! $isEdit) data-gallery-page-media-bulk @endif
  novalidate
>
  @csrf
  @if($isEdit)
    @method('PUT')
  @endif

  <section class="gallery-lite-form__panel">
    <div class="gallery-lite-form__grid">
      <div class="admin-field">
        <label for="type">Tipe</label>
        <select id="type" name="type" required data-gallery-page-media-type>
          @foreach($typeOptions as $value => $label)
            <option value="{{ $value }}" @selected($currentType === $value)>{{ $label }}</option>
          @endforeach
        </select>
        @error('type') <small>{{ $message }}</small> @enderror
      </div>

      <div class="admin-field">
        <label for="published_at">Tanggal</label>
        <input id="published_at" name="published_at" type="datetime-local" value="{{ $publishedAtValue }}">
        @error('published_at') <small>{{ $message }}</small> @enderror
      </div>

      <div class="admin-field admin-field--wide" data-gallery-page-media-photo-field @if($isVideo) hidden @endif>
        <label for="{{ $isEdit ? 'media_file' : 'media_files' }}">{{ $isEdit ? 'Ganti Foto' : 'Upload Banyak Foto' }}</label>
        <input
          id="{{ $isEdit ? 'media_file' : 'media_files' }}"
          name="{{ $isEdit ? 'media_file' : 'media_files[]' }}"
          type="file"
          accept="image/jpeg,image/png,image/webp"
          data-gallery-page-media-photo-input
          @if(! $isEdit) multiple @endif
          @disabled($isVideo)
        >
        <em>
          JPG, PNG, atau WebP. Maksimal 10MB per foto.
          @if(! $isEdit)
            Bisa pilih banyak gambar sekaligus.
          @endif
          @if($isEdit && $item->is_photo && $item->media_url)
            Media saat ini: {{ $item->media_label }}
          @endif
        </em>
        @error('media_file') <small>{{ $message }}</small> @enderror
        @error('media_files') <small>{{ $message }}</small> @enderror
        @error('media_files.*') <small>{{ $message }}</small> @enderror
      </div>

      <div class="admin-field admin-field--wide" data-gallery-page-media-video-field @if(! $isVideo) hidden @endif>
        <label for="{{ $isEdit ? 'media_url' : 'media_urls' }}">{{ $isEdit ? 'URL Video / Embed' : 'Banyak URL Video / Embed' }}</label>

        @if($isEdit)
          <input
            id="media_url"
            name="media_url"
            type="url"
            value="{{ old('media_url', $item->is_video ? $item->media_url : '') }}"
            maxlength="2048"
            placeholder="https://www.instagram.com/reel/..."
            data-gallery-page-media-video-url
            @disabled(! $isVideo)
          >
        @else
          <textarea
            id="media_urls"
            name="media_urls"
            rows="7"
            maxlength="20000"
            placeholder="https://www.instagram.com/reel/...
https://www.youtube.com/watch?v=..."
            data-gallery-page-media-video-url
            @disabled(! $isVideo)
          >{{ old('media_urls') }}</textarea>
        @endif

        <em>{{ $isEdit ? 'Tempel URL YouTube, TikTok, Instagram, Vimeo, atau Facebook Reel/Watch/Embed. Link Facebook share/r belum didukung.' : 'Tempel banyak URL, satu per baris. Mendukung YouTube, TikTok, Instagram, Vimeo, dan Facebook Reel/Watch/Embed; link share/r belum didukung.' }}</em>
        @error('media_url') <small>{{ $message }}</small> @enderror
        @error('media_urls') <small>{{ $message }}</small> @enderror
      </div>

      <label class="admin-check-field">
        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $item->is_published))>
        <span>Aktif</span>
      </label>
    </div>
  </section>

  <section class="gallery-media-review" data-gallery-page-media-review>
    <div class="gallery-media-review__head">
      <strong>Review Media</strong>
      @if(! $isEdit)
        <small data-gallery-page-media-count></small>
      @endif
    </div>

    <div class="gallery-media-review__stage" data-gallery-page-media-preview-stage>
      @if($isEdit && $item->is_photo && $item->media_url)
        <img src="{{ $item->media_url }}" alt="{{ $section->admin_title }}">
      @elseif($isEdit && $item->is_video && $item->media_url)
        <iframe src="{{ $item->media_url }}" title="{{ $section->admin_title }}" loading="lazy" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture; web-share" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>
      @else
        <span>{{ $isEdit ? 'Pilih foto atau tempel URL video untuk preview.' : 'Pilih banyak foto atau tempel banyak URL embed.' }}</span>
      @endif
    </div>
  </section>

  <div class="gallery-detail-actions gallery-detail-actions--simple">
    <button type="submit" class="admin-primary-action">{{ $isEdit ? 'Update Media' : 'Simpan Semua Media' }}</button>
  </div>
</form>

<script>
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
</script>
