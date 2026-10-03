  <form method="POST" action="{{ $action }}" class="gallery-lite-form" enctype="multipart/form-data" data-gallery-video-form novalidate>
    @csrf
    @if($isEdit)
      @method('PUT')
    @endif

    <header class="admin-topbar admin-topbar--compact">
      <div>
        <h1>{{ $isEdit ? $page['edit_title'] : $page['create_title'] }}</h1>
        <p>Indonesia adalah bahasa utama. English dan Arabic opsional; jika kosong, galeri publik akan memakai fallback yang tersedia.</p>
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

    <section class="gallery-lite-form__panel" data-language-tabs>
      <div class="admin-inline-actions" role="tablist" aria-label="Bahasa konten galeri">
        <button
          type="button"
          class="admin-primary-action {{ $activeLanguage === 'id' ? '' : 'admin-primary-action--ghost' }}"
          role="tab"
          aria-selected="{{ $activeLanguage === 'id' ? 'true' : 'false' }}"
          data-language-tab="id"
        >
          Indonesia · Utama {{ $languageCompletion['id'] ? '✓' : 'Belum' }}
        </button>
        <button
          type="button"
          class="admin-primary-action {{ $activeLanguage === 'en' ? '' : 'admin-primary-action--ghost' }}"
          role="tab"
          aria-selected="{{ $activeLanguage === 'en' ? 'true' : 'false' }}"
          data-language-tab="en"
        >
          English {{ $languageCompletion['en'] ? '✓' : 'Belum' }}
        </button>
        <button
          type="button"
          class="admin-primary-action {{ $activeLanguage === 'ar' ? '' : 'admin-primary-action--ghost' }}"
          role="tab"
          aria-selected="{{ $activeLanguage === 'ar' ? 'true' : 'false' }}"
          data-language-tab="ar"
        >
          العربية {{ $languageCompletion['ar'] ? '✓' : 'Belum' }}
        </button>
      </div>

      <div class="gallery-lite-form__grid" data-language-panel="id" @if($activeLanguage !== 'id') hidden @endif>
        <div class="admin-field admin-field--wide">
          <label for="title_id">{{ $form['title_id'] }}</label>
          <input id="title_id" name="title_id" value="{{ old('title_id', $item->title_id ?: $item->title) }}" maxlength="160" required>
          @error('title_id') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field admin-field--wide">
          <label for="category_id">{{ $form['category_id'] }}</label>
          <input id="category_id" name="category_id" value="{{ old('category_id', $item->category_id ?: $item->category) }}" maxlength="80" required>
          @error('category_id') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field admin-field--wide">
          <label for="caption_id">{{ $form['caption_id'] }}</label>
          <textarea id="caption_id" name="caption_id" rows="3" maxlength="1000">{{ old('caption_id', $item->caption_id ?: $item->caption) }}</textarea>
          <em>Opsional.</em>
          @error('caption_id') <small>{{ $message }}</small> @enderror
        </div>
      </div>

      <div class="gallery-lite-form__grid" data-language-panel="en" @if($activeLanguage !== 'en') hidden @endif>
        <div class="admin-field admin-field--wide">
          <label for="title_en">{{ $form['title_en'] }}</label>
          <input id="title_en" name="title_en" value="{{ old('title_en', $item->title_en) }}" maxlength="160" lang="en">
          @error('title_en') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field admin-field--wide">
          <label for="category_en">{{ $form['category_en'] }}</label>
          <input id="category_en" name="category_en" value="{{ old('category_en', $item->category_en) }}" maxlength="80" lang="en">
          @error('category_en') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field admin-field--wide">
          <label for="caption_en">{{ $form['caption_en'] }}</label>
          <textarea id="caption_en" name="caption_en" rows="3" maxlength="1000" lang="en">{{ old('caption_en', $item->caption_en) }}</textarea>
          <em>Opsional. Jika kosong, versi English fallback ke Indonesia.</em>
          @error('caption_en') <small>{{ $message }}</small> @enderror
        </div>
      </div>

      <div class="gallery-lite-form__grid" data-language-panel="ar" @if($activeLanguage !== 'ar') hidden @endif>
        <div class="admin-field admin-field--wide">
          <label for="title_ar">Judul Arabic</label>
          <input id="title_ar" name="title_ar" value="{{ old('title_ar', $item->title_ar) }}" maxlength="160" lang="ar" dir="rtl">
          @error('title_ar') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field admin-field--wide">
          <label for="category_ar">Kategori Arabic</label>
          <input id="category_ar" name="category_ar" value="{{ old('category_ar', $item->category_ar) }}" maxlength="80" lang="ar" dir="rtl">
          @error('category_ar') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field admin-field--wide">
          <label for="caption_ar">Caption Arabic</label>
          <textarea id="caption_ar" name="caption_ar" rows="3" maxlength="1000" lang="ar" dir="rtl">{{ old('caption_ar', $item->caption_ar) }}</textarea>
          <em>Opsional. Jika kosong, versi Arabic fallback ke Indonesia lalu English.</em>
          @error('caption_ar') <small>{{ $message }}</small> @enderror
        </div>
      </div>
    </section>

    <section class="gallery-lite-form__panel">
      <div class="gallery-lite-form__grid">
        <div class="admin-field">
          <label for="type">{{ $form['type'] }}</label>
          <select id="type" name="type" required data-gallery-type>
            @foreach($typeOptions as $value => $label)
              <option value="{{ $value }}" @selected($currentType === $value)>{{ $label }}</option>
            @endforeach
          </select>
          @error('type') <small>{{ $message }}</small> @enderror
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
          <em>{{ $form['video_hint'] }} Facebook menerima URL Reel, Watch, atau Embed. Link share/r belum didukung.</em>
          @error('media_url') <small>{{ $message }}</small> @enderror
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

        @include('admin.gallery.form.placements')
      </div>
    </section>

    <section class="gallery-media-review" data-gallery-review>
      <div class="gallery-media-review__head">
        <strong>{{ $form['review_title'] }}</strong>
      </div>

      <div class="gallery-media-review__stage" data-gallery-preview-stage>
        @if($isEdit && $item->is_photo && $item->media_url)
          <img src="{{ $item->media_url }}" alt="{{ $item->admin_title }}">
        @elseif($isEdit && $item->is_video && $item->media_url)
          <iframe src="{{ $item->media_url }}" title="{{ $item->admin_title }}" loading="lazy" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture; web-share" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>
        @else
          <span>{{ $form['review_empty'] }}</span>
        @endif
      </div>
    </section>
  </form>
