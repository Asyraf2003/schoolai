{{-- REAL_GALLERY_CRUD_FORM_FINAL --}}
@php
  $page = __('admin.gallery');
  $form = $page['form'];
  $isEdit = $mode === 'edit';
  $action = $isEdit ? route('admin.galeri.update', $item) : route('admin.galeri.store');
  $publishedAtValue = old('published_at', optional($item->published_at)->format('Y-m-d\TH:i'));
  $currentType = old('type', $item->type ?: 'photo');
  $isVideo = $currentType === 'video';
@endphp

@extends('layouts.admin', [
  'title' => $isEdit ? $page['edit_title'] : $page['create_title'],
  'activeAdminPage' => 'galeri',
])

@section('content')
  <form method="POST" action="{{ $action }}" class="gallery-lite-form" enctype="multipart/form-data" data-gallery-video-form novalidate>
    @csrf
    @if($isEdit)
      @method('PUT')
    @endif

    <header class="admin-topbar admin-topbar--compact">
      <div>
        <p class="admin-topbar__eyebrow">{{ $page['eyebrow'] }}</p>
        <h1>{{ $isEdit ? $page['edit_title'] : $page['create_title'] }}</h1>
      </div>

      <div class="admin-inline-actions">
        <a href="{{ $isEdit ? route('admin.galeri.show', $item) : route('admin.galeri') }}" class="admin-primary-action admin-primary-action--ghost">{{ $page['back_button'] }}</a>
        <button type="submit" class="admin-primary-action">{{ $isEdit ? $page['update_button'] : $page['save_button'] }}</button>
      </div>
    </header>

    @if(isset($errors) && $errors->any())
      <div class="admin-error-box" role="alert">
        @foreach($errors->all() as $error)
          <p>{{ $error }}</p>
        @endforeach
      </div>
    @endif

    <section class="gallery-lite-form__panel">
      <div class="gallery-lite-form__grid">
        <div class="admin-field admin-field--wide">
          <label for="title">{{ $form['title'] }}</label>
          <input id="title" name="title" value="{{ old('title', $item->title) }}" maxlength="160" required>
          @error('title') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field">
          <label for="type">{{ $form['type'] }}</label>
          <select id="type" name="type" required data-gallery-type>
            @foreach($typeOptions as $value => $label)
              <option value="{{ $value }}" @selected($currentType === $value)>{{ $label }}</option>
            @endforeach
          </select>
          @error('type') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field">
          <label for="category">{{ $form['category'] }}</label>
          <input id="category" name="category" value="{{ old('category', $item->category) }}" maxlength="80" required>
          @error('category') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field admin-field--wide">
          <label for="caption">{{ $form['caption'] }}</label>
          <textarea id="caption" name="caption" rows="3" maxlength="1000">{{ old('caption', $item->caption) }}</textarea>
          @error('caption') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field admin-field--wide" data-gallery-photo-field @if($isVideo) hidden @endif>
          <label for="media_file">{{ $form['photo_file'] }}</label>
          <input
            id="media_file"
            name="media_file"
            type="file"
            accept="image/jpeg,image/png,image/webp"
            data-gallery-photo-input
            @disabled($isVideo)
          >
          <em>
            {{ $form['photo_hint'] }}
            @if($isEdit && $item->is_photo && $item->media_url)
              {{ $form['current_media'] }}: {{ $item->media_label }}
            @endif
          </em>
          @error('media_file') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field admin-field--wide" data-gallery-video-field @if(! $isVideo) hidden @endif>
          <label for="media_url">{{ $form['video_url'] }}</label>
          <input
            id="media_url"
            name="media_url"
            type="url"
            value="{{ old('media_url', $item->is_video ? $item->media_url : '') }}"
            maxlength="2048"
            placeholder="https://www.youtube.com/watch?v=..."
            data-gallery-video-url
            @disabled(! $isVideo)
          >
          <em>{{ $form['video_hint'] }}</em>
          @error('media_url') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field">
          <label for="sort_order">{{ $form['sort_order'] }}</label>
          <input id="sort_order" name="sort_order" type="number" min="1" max="{{ $limits['max_items'] }}" value="{{ old('sort_order', $item->sort_order) }}" required>
          @error('sort_order') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field">
          <label for="published_at">{{ $form['published_at'] }}</label>
          <input id="published_at" name="published_at" type="datetime-local" value="{{ $publishedAtValue }}">
          @error('published_at') <small>{{ $message }}</small> @enderror
        </div>

        <label class="admin-check-field">
          <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $item->is_published))>
          <span>{{ $form['is_published'] }}</span>
        </label>
      </div>
    </section>

    <section class="gallery-media-review" data-gallery-review>
      <div class="gallery-media-review__head">
        <strong>{{ $form['review_title'] }}</strong>
      </div>

      <div class="gallery-media-review__stage" data-gallery-preview-stage>
        @if($isEdit && $item->is_photo && $item->media_url)
          <img src="{{ $item->media_url }}" alt="{{ $item->title }}">
        @elseif($isEdit && $item->is_video && $item->media_url)
          <iframe src="{{ $item->media_url }}" title="{{ $item->title }}" loading="lazy" allowfullscreen></iframe>
        @else
          <span>{{ $form['review_empty'] }}</span>
        @endif
      </div>
    </section>
  </form>

  <script>
    (() => {
      const form = document.querySelector('[data-gallery-video-form]');
      if (!form) return;

      const typeInput = form.querySelector('[data-gallery-type]');
      const photoField = form.querySelector('[data-gallery-photo-field]');
      const videoField = form.querySelector('[data-gallery-video-field]');
      const photoInput = form.querySelector('[data-gallery-photo-input]');
      const videoInput = form.querySelector('[data-gallery-video-url]');
      const stage = form.querySelector('[data-gallery-preview-stage]');
      const emptyText = @json($form['review_empty']);
      let previewUrl = null;

      function clearPreviewUrl() {
        if (previewUrl) {
          URL.revokeObjectURL(previewUrl);
          previewUrl = null;
        }
      }

      function setEmpty() {
        if (stage) {
          stage.replaceChildren(document.createElement('span'));
          stage.firstElementChild.textContent = emptyText;
        }
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

        if (!file) {
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

          if (host === 'youtu.be') {
            const id = parsed.pathname.split('/').filter(Boolean)[0];
            return id ? `https://www.youtube.com/embed/${encodeURIComponent(id)}` : '';
          }

          if (host.includes('youtube.com')) {
            const id = parsed.searchParams.get('v');
            if (id) return `https://www.youtube.com/embed/${encodeURIComponent(id)}`;

            const parts = parsed.pathname.split('/').filter(Boolean);
            const marker = parts.findIndex((part) => part === 'shorts' || part === 'embed');
            if (marker >= 0 && parts[marker + 1]) {
              return `https://www.youtube.com/embed/${encodeURIComponent(parts[marker + 1])}`;
            }
          }

          if (host.includes('tiktok.com')) {
            const match = parsed.pathname.match(/\/video\/(\d+)/);
            return match ? `https://www.tiktok.com/embed/v2/${match[1]}` : '';
          }

          if (host.includes('instagram.com')) {
            const match = parsed.pathname.match(/^\/(p|reel|tv)\/([^/]+)/);
            return match ? `https://www.instagram.com/${match[1]}/${encodeURIComponent(match[2])}/embed` : '';
          }

          if (host.includes('vimeo.com')) {
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
@endsection
