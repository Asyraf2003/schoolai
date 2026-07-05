{{-- REAL_GALLERY_CRUD_FORM_FINAL --}}
@php
  $page = __('admin.gallery');
  $form = $page['form'];
  $isEdit = $mode === 'edit';
  $action = $isEdit ? route('admin.galeri.update', $item) : route('admin.galeri.store');
  $publishedAtValue = old('published_at', optional($item->published_at)->format('Y-m-d\TH:i'));
@endphp

@extends('layouts.admin', [
  'title' => $isEdit ? $page['edit_title'] : $page['create_title'],
  'activeAdminPage' => 'galeri',
])

@section('content')
  <form
    method="POST"
    action="{{ $action }}"
    class="gallery-lite-form"
    enctype="multipart/form-data"
    data-gallery-upload-form
    data-uploading-text="{{ $form['uploading'] }}"
    data-upload-done-text="{{ $form['upload_done'] }}"
    data-upload-failed-text="{{ $form['upload_failed'] }}"
  >
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
        <button type="submit" class="admin-primary-action" data-gallery-submit>{{ $isEdit ? $page['update_button'] : $page['save_button'] }}</button>
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
              <option value="{{ $value }}" @selected(old('type', $item->type) === $value)>{{ $label }}</option>
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

        <div class="admin-field admin-field--wide">
          <label for="media_file">{{ $form['media_file'] }}</label>
          <input
            id="media_file"
            name="media_file"
            type="file"
            @required(! $isEdit)
            data-gallery-media-input
          >
          <em>
            {{ $form['media_hint'] }}
            @if($isEdit && $item->media_url)
              {{ $form['current_media'] }}: {{ $item->media_filename }}
            @endif
          </em>
          @error('media_file') <small>{{ $message }}</small> @enderror
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
        <span data-gallery-review-status>{{ $form['review_empty'] }}</span>
      </div>

      <div class="gallery-media-review__stage" data-gallery-preview-stage>
        @if($isEdit && $item->media_url && $item->type === 'photo')
          <img src="{{ $item->media_url }}" alt="{{ $item->title }}" data-existing-media>
        @elseif($isEdit && $item->media_url && $item->is_video)
          <video src="{{ $item->media_url }}#t=0.1" controls preload="metadata" data-existing-media></video>
        @else
          <span>{{ $form['review_empty'] }}</span>
        @endif
      </div>

      <canvas class="gallery-video-thumb" width="320" height="180" hidden data-gallery-video-thumb></canvas>
    </section>

    <section class="gallery-upload-progress" hidden data-gallery-upload-progress>
      <div class="gallery-upload-progress__text">
        <strong data-gallery-upload-status>{{ $form['uploading'] }}</strong>
        <span data-gallery-upload-percent>0%</span>
      </div>
      <div class="gallery-upload-progress__bar">
        <span style="width: 0%;" data-gallery-upload-bar></span>
      </div>
    </section>
  </form>

  <script>
    (() => {
      const form = document.querySelector('[data-gallery-upload-form]');

      if (!form) {
        return;
      }

      const typeInput = form.querySelector('[data-gallery-type]');
      const mediaInput = form.querySelector('[data-gallery-media-input]');
      const previewStage = form.querySelector('[data-gallery-preview-stage]');
      const reviewStatus = form.querySelector('[data-gallery-review-status]');
      const videoThumb = form.querySelector('[data-gallery-video-thumb]');
      const submitButton = form.querySelector('[data-gallery-submit]');
      const progressBox = form.querySelector('[data-gallery-upload-progress]');
      const progressBar = form.querySelector('[data-gallery-upload-bar]');
      const progressPercent = form.querySelector('[data-gallery-upload-percent]');
      const progressStatus = form.querySelector('[data-gallery-upload-status]');

      const copy = {
        empty: @json($form['review_empty']),
        ready: @json($form['review_ready']),
        processing: @json($form['review_processing']),
        videoThumb: @json($form['review_video_thumb']),
        uploading: @json($form['uploading']),
        done: @json($form['upload_done']),
        failed: @json($form['upload_failed']),
      };

      let previewUrl = null;

      const updateAccept = () => {
        if (!mediaInput || !typeInput) {
          return;
        }

        mediaInput.accept = typeInput.value === 'video'
          ? 'video/mp4,video/webm,video/quicktime,video/x-m4v'
          : 'image/jpeg,image/png,image/webp';
      };

      const resetPreviewUrl = () => {
        if (previewUrl) {
          URL.revokeObjectURL(previewUrl);
          previewUrl = null;
        }
      };

      const setStatus = (message) => {
        if (reviewStatus) {
          reviewStatus.textContent = message;
        }
      };

      const clearPreview = () => {
        resetPreviewUrl();

        if (previewStage) {
          previewStage.innerHTML = `<span>${copy.empty}</span>`;
        }

        if (videoThumb) {
          videoThumb.hidden = true;
        }

        setStatus(copy.empty);
      };

      const drawVideoFrame = (video) => {
        if (!videoThumb || !video.videoWidth || !video.videoHeight) {
          return;
        }

        const context = videoThumb.getContext('2d');

        if (!context) {
          return;
        }

        videoThumb.width = video.videoWidth;
        videoThumb.height = video.videoHeight;
        context.drawImage(video, 0, 0, videoThumb.width, videoThumb.height);
        videoThumb.hidden = false;
        setStatus(copy.videoThumb);
      };

      const previewFile = (file) => {
        if (!file || !previewStage) {
          clearPreview();
          return;
        }

        resetPreviewUrl();
        previewUrl = URL.createObjectURL(file);
        previewStage.innerHTML = '';
        setStatus(copy.processing);

        if (file.type.startsWith('image/')) {
          const image = document.createElement('img');
          image.alt = file.name;
          image.onload = () => setStatus(copy.ready);
          image.src = previewUrl;

          previewStage.appendChild(image);

          if (videoThumb) {
            videoThumb.hidden = true;
          }

          return;
        }

        if (file.type.startsWith('video/')) {
          const video = document.createElement('video');
          video.controls = true;
          video.muted = true;
          video.preload = 'metadata';
          video.src = previewUrl;

          video.addEventListener('loadedmetadata', () => {
            const targetTime = Math.min(0.1, Math.max(video.duration || 0.1, 0.1));

            try {
              video.currentTime = targetTime;
            } catch {
              drawVideoFrame(video);
            }
          }, { once: true });

          video.addEventListener('seeked', () => drawVideoFrame(video));
          video.addEventListener('loadeddata', () => drawVideoFrame(video), { once: true });

          previewStage.appendChild(video);
          return;
        }

        clearPreview();
      };

      const setUploadProgress = (percent, message = copy.uploading) => {
        const normalized = Math.max(0, Math.min(100, Math.round(percent)));

        if (progressBox) {
          progressBox.hidden = false;
        }

        if (progressBar) {
          progressBar.style.width = `${normalized}%`;
        }

        if (progressPercent) {
          progressPercent.textContent = `${normalized}%`;
        }

        if (progressStatus) {
          progressStatus.textContent = message;
        }
      };

      updateAccept();

      typeInput?.addEventListener('change', () => {
        updateAccept();

        if (mediaInput?.files?.length) {
          mediaInput.value = '';
          clearPreview();
        }
      });

      mediaInput?.addEventListener('change', () => {
        previewFile(mediaInput.files?.[0] ?? null);
      });

      form.addEventListener('submit', (event) => {
        if (!form.checkValidity()) {
          return;
        }

        event.preventDefault();

        const xhr = new XMLHttpRequest();
        const formData = new FormData(form);

        submitButton?.setAttribute('disabled', 'disabled');
        setUploadProgress(0);

        xhr.open(form.method || 'POST', form.action, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Accept', 'text/html,application/xhtml+xml');

        xhr.upload.addEventListener('progress', (progressEvent) => {
          if (progressEvent.lengthComputable) {
            setUploadProgress((progressEvent.loaded / progressEvent.total) * 100);
          } else {
            setUploadProgress(15);
          }
        });

        xhr.addEventListener('load', () => {
          if (xhr.status >= 200 && xhr.status < 400) {
            setUploadProgress(100, copy.done);
            window.location.href = xhr.responseURL || @json(route('admin.galeri'));
            return;
          }

          submitButton?.removeAttribute('disabled');
          setUploadProgress(100, copy.failed);
          document.open();
          document.write(xhr.responseText);
          document.close();
        });

        xhr.addEventListener('error', () => {
          submitButton?.removeAttribute('disabled');
          setUploadProgress(100, copy.failed);
        });

        xhr.send(formData);
      });
    })();
  </script>
@endsection
