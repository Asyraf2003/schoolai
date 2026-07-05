{{-- REAL_GALLERY_CRUD_FORM_FINAL --}}
@php
  $page = __('admin.gallery');
  $form = $page['form'];
  $isEdit = $mode === 'edit';
  $action = $isEdit ? route('admin.galeri.update', $item) : route('admin.galeri.store');
  $publishedAtValue = old('published_at', optional($item->published_at)->format('Y-m-d\TH:i'));
  $currentType = old('type', $item->type ?: 'photo');
@endphp

@extends('layouts.admin', [
  'title' => $isEdit ? $page['edit_title'] : $page['create_title'],
  'activeAdminPage' => 'galeri',
])

@section('content')
  <form method="POST" action="{{ $action }}" class="gallery-lite-form" enctype="multipart/form-data" data-gallery-embed-form>
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
      <div class="admin-error-box">
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

        <div class="admin-field admin-field--wide" data-gallery-photo-field>
          <label for="media_file">{{ $form['media_file'] }}</label>
          <input id="media_file" name="media_file" type="file" accept="image/jpeg,image/png,image/webp" data-gallery-photo-input>
          <em>
            {{ $form['photo_hint'] }}
            @if($isEdit && $item->is_photo && $item->media_url)
              {{ $form['current_media'] }}: {{ $item->media_label }}
            @endif
          </em>
          @error('media_file') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field admin-field--wide" data-gallery-embed-field>
          <label for="media_url">{{ $form['media_url'] }}</label>
          <input id="media_url" name="media_url" type="url" value="{{ old('media_url', $item->is_embed ? $item->media_url : '') }}" maxlength="2048" placeholder="https://www.youtube.com/watch?v=...">
          <em>{{ $form['embed_hint'] }}</em>
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
        @elseif($isEdit && $item->is_embed && $item->media_url)
          <iframe src="{{ $item->media_url }}" title="{{ $item->title }}" loading="lazy" allowfullscreen></iframe>
        @else
          <span>{{ $form['review_empty'] }}</span>
        @endif
      </div>
    </section>
  </form>

  <script>
    (() => {
      const form = document.querySelector('[data-gallery-embed-form]');
      if (!form) return;

      const typeInput = form.querySelector('[data-gallery-type]');
      const photoField = form.querySelector('[data-gallery-photo-field]');
      const embedField = form.querySelector('[data-gallery-embed-field]');
      const photoInput = form.querySelector('[data-gallery-photo-input]');
      const embedInput = form.querySelector('[name="media_url"]');
      const stage = form.querySelector('[data-gallery-preview-stage]');
      const emptyText = @json($form['review_empty']);
      let previewUrl = null;

      function clearPreviewUrl() {
        if (previewUrl) {
          URL.revokeObjectURL(previewUrl);
          previewUrl = null;
        }
      }

      function setStage(html) {
        if (stage) stage.innerHTML = html;
      }

      function updateFields() {
        const isEmbed = typeInput.value === 'embed';

        photoField.hidden = isEmbed;
        embedField.hidden = !isEmbed;

        photoInput.disabled = isEmbed;
        embedInput.disabled = !isEmbed;

        if (isEmbed) {
          photoInput.value = '';
        } else {
          embedInput.value = '';
        }
      }

      function previewPhoto(file) {
        clearPreviewUrl();

        if (!file) {
          setStage(`<span>${emptyText}</span>`);
          return;
        }

        previewUrl = URL.createObjectURL(file);
        setStage('');

        const img = document.createElement('img');
        img.alt = file.name;
        img.src = previewUrl;
        stage.appendChild(img);
      }

      function previewEmbed(url) {
        clearPreviewUrl();

        const cleanUrl = url.trim();

        if (!cleanUrl) {
          setStage(`<span>${emptyText}</span>`);
          return;
        }

        const iframe = document.createElement('iframe');
        iframe.src = cleanUrl;
        iframe.title = 'Preview embed';
        iframe.loading = 'lazy';
        iframe.allowFullscreen = true;

        setStage('');
        stage.appendChild(iframe);
      }

      typeInput.addEventListener('change', () => {
        updateFields();
        setStage(`<span>${emptyText}</span>`);
      });

      photoInput.addEventListener('change', () => previewPhoto(photoInput.files?.[0] || null));
      embedInput.addEventListener('input', () => previewEmbed(embedInput.value));

      updateFields();
    })();
  </script>
@endsection
