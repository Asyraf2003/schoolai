{{-- GALLERY_PAGE_MEDIA_FORM_PARTIAL_FINAL --}}
@php
  $isEdit = $mode === 'edit';
  $action = $isEdit ? route('admin.galeri.section-media.update', $item) : route('admin.galeri.section-media.store', $section);
  $publishedAtValue = old('published_at', optional($item->published_at)->format('Y-m-d\TH:i'));
  $currentType = old('type', $item->type ?: 'photo');
  $isVideo = $currentType === 'video';
@endphp

<form method="POST" action="{{ $action }}" class="gallery-lite-form" enctype="multipart/form-data" data-gallery-page-media-form novalidate>
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
        <label for="media_file">Upload Foto</label>
        <input
          id="media_file"
          name="media_file"
          type="file"
          accept="image/jpeg,image/png,image/webp"
          data-gallery-page-media-photo-input
          @disabled($isVideo)
        >
        <em>
          JPG, PNG, atau WebP. Maksimal 10MB.
          @if($isEdit && $item->is_photo && $item->media_url)
            Media saat ini: {{ $item->media_label }}
          @endif
        </em>
        @error('media_file') <small>{{ $message }}</small> @enderror
      </div>

      <div class="admin-field admin-field--wide" data-gallery-page-media-video-field @if(! $isVideo) hidden @endif>
        <label for="media_url">URL Video / Embed</label>
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
        <em>Tempel URL YouTube, TikTok, Instagram, atau Vimeo. Server akan ubah ke embed yang aman.</em>
        @error('media_url') <small>{{ $message }}</small> @enderror
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
    </div>

    <div class="gallery-media-review__stage" data-gallery-page-media-preview-stage>
      @if($isEdit && $item->is_photo && $item->media_url)
        <img src="{{ $item->media_url }}" alt="{{ $item->admin_title }}">
      @elseif($isEdit && $item->is_video && $item->media_url)
        <iframe src="{{ $item->media_url }}" title="{{ $item->admin_title }}" loading="lazy" allow="fullscreen; picture-in-picture" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>
      @else
        <span>Pilih foto atau tempel URL video untuk preview.</span>
      @endif
    </div>
  </section>

  <div class="gallery-detail-actions gallery-detail-actions--simple">
    <button type="submit" class="admin-primary-action">{{ $isEdit ? 'Update Media' : 'Simpan Media' }}</button>
  </div>
</form>

<script>
  (() => {
    const form = document.querySelector('[data-gallery-page-media-form]');
    if (!form) return;

    const typeInput = form.querySelector('[data-gallery-page-media-type]');
    const photoField = form.querySelector('[data-gallery-page-media-photo-field]');
    const videoField = form.querySelector('[data-gallery-page-media-video-field]');
    const photoInput = form.querySelector('[data-gallery-page-media-photo-input]');
    const videoInput = form.querySelector('[data-gallery-page-media-video-url]');
    const stage = form.querySelector('[data-gallery-page-media-preview-stage]');
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
      placeholder.textContent = 'Pilih foto atau tempel URL video untuk preview.';
      stage.replaceChildren(placeholder);
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
      }
    }

    function previewPhoto(file) {
      clearPreviewUrl();

      if (!file || !file.type.startsWith('image/')) {
        setEmpty();
        return;
      }

      previewUrl = URL.createObjectURL(file);

      const img = document.createElement('img');
      img.alt = file.name;
      img.src = previewUrl;

      stage.replaceChildren(img);
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
          const match = parsed.pathname.match(/\/video\/(\d+)/);
          return match ? `https://www.tiktok.com/embed/v2/${match[1]}` : '';
        }

        if (hostMatches('instagram.com')) {
          const match = parsed.pathname.match(/^\/(p|reel|tv)\/([^/]+)/);
          return match ? `https://www.instagram.com/${match[1]}/${encodeURIComponent(match[2])}/embed` : '';
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

    function previewVideo(url) {
      clearPreviewUrl();

      const embedUrl = toPreviewUrl(url);

      if (!embedUrl) {
        setEmpty();
        return;
      }

      const iframe = document.createElement('iframe');
      iframe.src = embedUrl;
      iframe.title = 'Preview video';
      iframe.loading = 'lazy';
      iframe.allow = 'fullscreen; picture-in-picture';
      iframe.allowFullscreen = true;
      iframe.referrerPolicy = 'strict-origin-when-cross-origin';

      stage.replaceChildren(iframe);
    }

    typeInput.addEventListener('change', () => updateFields({ clear: true }));
    photoInput.addEventListener('change', () => previewPhoto(photoInput.files?.[0] || null));
    videoInput.addEventListener('input', () => previewVideo(videoInput.value));

    updateFields();
  })();
</script>
