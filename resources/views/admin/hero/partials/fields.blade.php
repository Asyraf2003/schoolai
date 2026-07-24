  <form
    method="POST"
    action="{{ $action }}"
    class="gallery-lite-form"
    enctype="multipart/form-data"
    data-hero-media-form
  >
    @csrf
    @if($isEdit)
      @method('PUT')
    @endif

    <header class="admin-topbar admin-topbar--compact">
      <div>
        <h1>{{ $isEdit ? 'Edit Hero Slide' : 'Tambah Hero Slide' }}</h1>
        <p>Hero adalah penempatan artikel, bukan konten kedua. Judul, ringkasan, bahasa, dan link baca selalu mengikuti artikel yang dipilih.</p>
      </div>

      <div class="admin-inline-actions">
        <a href="{{ route('admin.hero') }}" class="admin-primary-action admin-primary-action--ghost">Kembali</a>
        <button type="submit" class="admin-primary-action">{{ $isEdit ? 'Update' : 'Simpan' }}</button>
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
          <label for="article_id">Artikel yang ditampilkan</label>
          <select id="article_id" name="article_id" required>
            <option value="">Pilih artikel terbit</option>
            @foreach($articles as $articleOption)
              <option value="{{ $articleOption->getKey() }}" @selected($currentArticleId === (int) $articleOption->getKey())>
                {{ $articleOption->admin_title }} · {{ $articleOption->published_at?->translatedFormat('d M Y, H:i') ?? '-' }}
              </option>
            @endforeach
          </select>
          <em>Artikel terbaru otomatis dapat mengisi hero ketika belum ada penempatan manual. Daftar ini dipakai untuk mengatur pilihan dan urutannya.</em>
          @error('article_id') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field">
          <label for="type">Tipe media</label>
          <select id="type" name="type" required data-hero-type>
            <option value="image" @selected($currentType === 'image')>Gambar</option>
            <option value="video" @selected($currentType === 'video')>Video</option>
          </select>
          <em data-hero-type-hint>Form media akan menyesuaikan tipe yang dipilih.</em>
        </div>

        <div class="admin-field admin-field--wide">
          <label for="media_file" data-hero-media-file-label>Upload gambar</label>
          <input
            id="media_file"
            name="media_file"
            type="file"
            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
            data-hero-media-file
          >
          <em data-hero-media-file-hint>JPG/JPEG, PNG, atau WebP maksimal 10 MB. Kosongkan untuk mempertahankan media yang ada.</em>
          @error('media_file') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field admin-field--wide">
          <label for="media_url" data-hero-media-url-label>URL gambar</label>
          <input
            id="media_url"
            name="media_url"
            value="{{ old('media_url', $slide->media_url) }}"
            maxlength="2048"
            placeholder="https://.../gambar.jpg"
            data-hero-media-url
          >
          <em data-hero-media-url-hint>Gunakan URL gambar publik. Jika kosong, thumbnail artikel akan dipakai sebagai fallback.</em>
          @error('media_url') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field admin-field--wide" data-hero-video-only>
          <label for="poster_file">Upload poster video</label>
          <input
            id="poster_file"
            name="poster_file"
            type="file"
            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
            data-hero-poster-input
          >
          <em>Opsional. JPG/JPEG, PNG, atau WebP maksimal 10 MB. Poster ditampilkan sebelum video mulai diputar atau jika video tidak tersedia.</em>
          @error('poster_file') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field admin-field--wide" data-hero-video-only>
          <label for="poster_url">URL poster video</label>
          <input
            id="poster_url"
            name="poster_url"
            value="{{ old('poster_url', $slide->poster_url) }}"
            maxlength="2048"
            placeholder="https://.../poster.jpg"
            data-hero-poster-input
          >
          <em>Jika poster tidak diisi, thumbnail artikel dipakai sebagai fallback.</em>
          @error('poster_url') <small>{{ $message }}</small> @enderror
        </div>
      </div>
    </section>

    <section class="gallery-lite-form__panel">
      <div class="gallery-lite-form__grid">
        <div class="admin-field">
          <label for="focal_position">Focal position</label>
          <input id="focal_position" name="focal_position" value="{{ old('focal_position', $slide->focal_position ?: 'center center') }}" maxlength="60" placeholder="center center">
        </div>

        <div class="admin-field">
          <label for="overlay_strength">Overlay</label>
          <input id="overlay_strength" name="overlay_strength" type="number" min="0.28" max="0.88" step="0.01" value="{{ old('overlay_strength', $slide->overlay_strength ?? 0.46) }}">
        </div>

        <label class="admin-check-field">
          <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $slide->is_active ?? true))>
          <span>Tampilkan artikel ini di hero homepage</span>
        </label>
      </div>
    </section>
  </form>
