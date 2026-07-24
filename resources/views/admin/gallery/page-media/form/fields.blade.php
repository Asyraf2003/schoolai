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
