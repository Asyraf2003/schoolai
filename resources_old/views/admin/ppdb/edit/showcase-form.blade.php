      <div class="gallery-lite-panel ppdb-showcase-admin__panel">
        <div class="ppdb-showcase-admin__head">
          <div>
            <h2>{{ $showcaseFormIsEdit ? 'Edit item' : 'Tambah item' }}</h2>
            <p>Indonesia adalah bahasa utama. English dan Arabic opsional dan memakai fallback jika kosong.</p>
          </div>
          @if ($showcaseFormIsEdit)
            <a href="{{ route('admin.ppdb') }}#ppdb-showcase-admin" class="admin-small-action admin-small-action--ghost">Batal edit</a>
          @endif
        </div>

        @if (! \Illuminate\Support\Facades\Schema::hasTable('ppdb_showcase_items'))
          <p class="ppdb-showcase-form-note">Tabel item PPDB belum ada. Jalankan migration dulu.</p>
        @endif

        <form method="POST" action="{{ $showcaseFormIsEdit ? route('admin.ppdb.showcase.update', $showcaseItemForm) : route('admin.ppdb.showcase.store') }}" enctype="multipart/form-data" class="gallery-lite-form" data-ppdb-showcase-form>
          @csrf
          @if ($showcaseFormIsEdit)
            @method('PUT')
          @endif

          <div class="gallery-lite-form__grid">
            <div class="admin-field">
              <label for="showcase_audience">Target tab</label>
              <select id="showcase_audience" name="audience" required>
                @foreach ($audienceOptions as $value => $label)
                  <option value="{{ $value }}" @selected($showcaseAudience === $value)>{{ $label }}</option>
                @endforeach
              </select>
              @error('audience')<small>{{ $message }}</small>@enderror
            </div>

            <div class="admin-field">
              <label for="showcase_media_type">Tipe media</label>
              <select id="showcase_media_type" name="media_type" required data-ppdb-media-type>
                @foreach ($mediaTypeOptions as $value => $label)
                  <option value="{{ $value }}" @selected($showcaseMediaType === $value)>{{ $label }}</option>
                @endforeach
              </select>
              @error('media_type')<small>{{ $message }}</small>@enderror
            </div>
          </div>

          <div data-language-tabs>
            <div class="admin-inline-actions" role="tablist" aria-label="Bahasa konten PPDB" style="margin: 14px 0;">
              <button type="button" class="admin-primary-action {{ $showcaseActiveLanguage === 'id' ? '' : 'admin-primary-action--ghost' }}" role="tab" aria-selected="{{ $showcaseActiveLanguage === 'id' ? 'true' : 'false' }}" data-language-tab="id">
                Indonesia · Utama {{ $showcaseLanguageCompletion['id'] ? '✓' : 'Belum' }}
              </button>
              <button type="button" class="admin-primary-action {{ $showcaseActiveLanguage === 'en' ? '' : 'admin-primary-action--ghost' }}" role="tab" aria-selected="{{ $showcaseActiveLanguage === 'en' ? 'true' : 'false' }}" data-language-tab="en">
                English {{ $showcaseLanguageCompletion['en'] ? '✓' : 'Belum' }}
              </button>
              <button type="button" class="admin-primary-action {{ $showcaseActiveLanguage === 'ar' ? '' : 'admin-primary-action--ghost' }}" role="tab" aria-selected="{{ $showcaseActiveLanguage === 'ar' ? 'true' : 'false' }}" data-language-tab="ar">
                العربية {{ $showcaseLanguageCompletion['ar'] ? '✓' : 'Belum' }}
              </button>
            </div>

            <div class="gallery-lite-form__grid" data-language-panel="id" @if($showcaseActiveLanguage !== 'id') hidden @endif>
              <div class="admin-field admin-field--wide">
                <label for="showcase_title_id">Judul Indonesia</label>
                <input id="showcase_title_id" type="text" name="title_id" value="{{ old('title_id', $showcaseItemForm->title_id ?? '') }}" maxlength="180" required>
                @error('title_id')<small>{{ $message }}</small>@enderror
              </div>

              <div class="admin-field admin-field--wide">
                <label for="showcase_description_id">Deskripsi Indonesia</label>
                <textarea id="showcase_description_id" name="description_id" rows="4" maxlength="1200" required>{{ old('description_id', $showcaseItemForm->description_id ?? '') }}</textarea>
                @error('description_id')<small>{{ $message }}</small>@enderror
              </div>
            </div>

            <div class="gallery-lite-form__grid" data-language-panel="en" @if($showcaseActiveLanguage !== 'en') hidden @endif>
              <div class="admin-field admin-field--wide">
                <label for="showcase_title_en">Judul English</label>
                <input id="showcase_title_en" type="text" name="title_en" value="{{ old('title_en', $showcaseItemForm->title_en ?? '') }}" maxlength="180" lang="en">
                @error('title_en')<small>{{ $message }}</small>@enderror
              </div>

              <div class="admin-field admin-field--wide">
                <label for="showcase_description_en">Deskripsi English</label>
                <textarea id="showcase_description_en" name="description_en" rows="4" maxlength="1200" lang="en">{{ old('description_en', $showcaseItemForm->description_en ?? '') }}</textarea>
                <em>Opsional. Jika kosong, versi English fallback ke Indonesia.</em>
                @error('description_en')<small>{{ $message }}</small>@enderror
              </div>
            </div>

            <div class="gallery-lite-form__grid" data-language-panel="ar" @if($showcaseActiveLanguage !== 'ar') hidden @endif>
              <div class="admin-field admin-field--wide">
                <label for="showcase_title_ar">Judul Arabic</label>
                <input id="showcase_title_ar" type="text" name="title_ar" value="{{ old('title_ar', $showcaseItemForm->title_ar ?? '') }}" maxlength="180" lang="ar" dir="rtl">
                @error('title_ar')<small>{{ $message }}</small>@enderror
              </div>

              <div class="admin-field admin-field--wide">
                <label for="showcase_description_ar">Deskripsi Arabic</label>
                <textarea id="showcase_description_ar" name="description_ar" rows="4" maxlength="1200" lang="ar" dir="rtl">{{ old('description_ar', $showcaseItemForm->description_ar ?? '') }}</textarea>
                <em>Opsional. Jika kosong, versi Arabic fallback ke Indonesia lalu English.</em>
                @error('description_ar')<small>{{ $message }}</small>@enderror
              </div>
            </div>
          </div>

          <div class="gallery-lite-form__grid" style="margin-top: 14px;">
            <div class="admin-field admin-field--wide ppdb-media-input" data-ppdb-media-photo>
              <label for="showcase_media_file">Upload foto</label>
              <input id="showcase_media_file" type="file" name="media_file" accept="image/jpeg,image/png,image/webp">
              <em>Wajib untuk item foto baru. Maksimal {{ $showcaseLimits['max_photo_mb'] ?? 10 }}MB.</em>
              @error('media_file')<small>{{ $message }}</small>@enderror
            </div>

            <div class="admin-field admin-field--wide ppdb-media-input" data-ppdb-media-url>
              <label for="showcase_media_url">URL video / embed</label>
              <input id="showcase_media_url" type="url" name="media_url" value="{{ old('media_url', $showcaseItemForm->is_video ? $showcaseItemForm->media_url : '') }}" maxlength="2048" placeholder="https://youtube.com/watch?v=...">
              <em>Wajib untuk tipe URL. Mendukung YouTube, TikTok, Instagram, atau Vimeo.</em>
              @error('media_url')<small>{{ $message }}</small>@enderror
            </div>
          </div>

          @if ($showcaseFormIsEdit && $showcaseItemForm->media_url)
            <div class="gallery-media-review ppdb-showcase-preview">
              <div class="gallery-media-review__head">
                <strong>Media saat ini</strong>
                <span class="admin-counter">{{ $showcaseItemForm->media_label }}</span>
              </div>
              <div class="gallery-media-review__stage ppdb-showcase-preview__stage">
                @if ($showcaseItemForm->is_video)
                  <iframe src="{{ $showcaseItemForm->media_url }}" loading="lazy" allowfullscreen title="Preview video PPDB"></iframe>
                @else
                  <img src="{{ $showcaseItemForm->media_url }}" alt="Preview media PPDB">
                @endif
              </div>
            </div>
          @endif

          <div class="admin-inline-actions" style="margin-top: 14px;">
            <button type="submit" class="admin-primary-action">{{ $showcaseFormIsEdit ? 'Simpan Item' : 'Tambah Item' }}</button>
          </div>
        </form>
      </div>
