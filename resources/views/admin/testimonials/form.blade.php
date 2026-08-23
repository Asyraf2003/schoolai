@extends('layouts.admin', [
  'title' => $isEdit ? 'Edit Media Testimoni' : 'Tambah Media Testimoni',
  'activeAdminPage' => 'testimoni',
])

@section('content')
  <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="gallery-lite-form" data-testimonial-admin-form novalidate>
    @csrf
    @if($isEdit)
      @method('PUT')
    @endif

    <header class="admin-topbar admin-topbar--compact">
      <div>
        <h1>{{ $isEdit ? 'Edit Media Testimoni' : 'Tambah Media Testimoni' }}</h1>
        <p>Foto memakai upload file. Video dapat diunggah langsung atau memakai embed YouTube, Vimeo, TikTok, Instagram, dan Facebook.</p>
      </div>

      <div class="admin-inline-actions">
        <a href="{{ route('admin.testimoni.index') }}" class="admin-primary-action admin-primary-action--ghost">Kembali</a>
        <button type="submit" class="admin-primary-action">Simpan</button>
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
        <div class="admin-field">
          <label for="type">Tipe Media</label>
          <select id="type" name="type" required data-testimonial-type>
            <option value="photo" @selected($currentType === 'photo')>Foto</option>
            <option value="video" @selected($currentType === 'video')>Video</option>
          </select>
          @error('type') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field" data-testimonial-source-field @if($currentType === 'photo') hidden @endif>
          <label for="source">Sumber Video</label>
          <select id="source" name="source" data-testimonial-source @disabled($currentType === 'photo')>
            <option value="upload" @selected($currentSource === 'upload')>Upload Video</option>
            <option value="embed" @selected($currentSource === 'embed')>Embed URL</option>
          </select>
          @error('source') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field admin-field--wide" data-testimonial-upload-field @if($currentSource === 'embed') hidden @endif>
          <label for="media_file">Upload Media</label>
          <input
            id="media_file"
            name="media_file"
            type="file"
            data-testimonial-file
            @disabled($currentSource === 'embed')
            accept="{{ $currentType === 'photo' ? 'image/jpeg,image/png,image/webp' : 'video/mp4,video/webm,video/quicktime' }}"
          >
          <em data-testimonial-file-hint>
            {{ $currentType === 'photo' ? 'JPG, PNG, atau WebP. Maksimal 10MB.' : 'MP4, WebM, atau MOV. Maksimal 100MB.' }}
            @if($isEdit && $item->source === 'upload' && $item->media_url)
              Media saat ini: {{ $item->media_label }}. Kosongkan jika tidak ingin mengganti file.
            @endif
          </em>
          @error('media_file') <small>{{ $message }}</small> @enderror
        </div>

        <div class="admin-field admin-field--wide" data-testimonial-embed-field @if($currentSource !== 'embed') hidden @endif>
          <label for="media_url">URL Video</label>
          <input
            id="media_url"
            name="media_url"
            type="url"
            value="{{ old('media_url', $item->source === 'embed' ? $item->media_url : '') }}"
            maxlength="2048"
            placeholder="https://www.youtube.com/watch?v=..."
            data-testimonial-url
            @disabled($currentSource !== 'embed')
          >
          <em>Tempel URL video publik. Sistem akan mengubahnya menjadi URL embed yang aman.</em>
          @error('media_url') <small>{{ $message }}</small> @enderror
        </div>

        <label class="admin-check-field">
          <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $item->is_published))>
          <span>Aktifkan di homepage</span>
        </label>
      </div>
    </section>

    @if($isEdit && $item->media_url)
      <section class="gallery-media-review">
        <div class="gallery-media-review__head">
          <strong>Media Saat Ini</strong>
        </div>
        <div class="gallery-media-review__stage">
          @if($item->is_photo)
            <img src="{{ $item->media_url }}" alt="Media testimoni">
          @elseif($item->is_embed)
            <iframe src="{{ $item->media_url }}" title="Video testimoni" loading="lazy" allow="autoplay; encrypted-media; fullscreen; picture-in-picture" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>
          @else
            <video src="{{ $item->media_url }}" controls playsinline preload="metadata"></video>
          @endif
        </div>
      </section>
    @endif
  </form>

  <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
    (() => {
      const form = document.querySelector('[data-testimonial-admin-form]');
      if (!form) return;

      const type = form.querySelector('[data-testimonial-type]');
      const source = form.querySelector('[data-testimonial-source]');
      const sourceField = form.querySelector('[data-testimonial-source-field]');
      const uploadField = form.querySelector('[data-testimonial-upload-field]');
      const embedField = form.querySelector('[data-testimonial-embed-field]');
      const file = form.querySelector('[data-testimonial-file]');
      const url = form.querySelector('[data-testimonial-url]');
      const hint = form.querySelector('[data-testimonial-file-hint]');

      function sync() {
        const isVideo = type.value === 'video';
        if (!isVideo) source.value = 'upload';

        sourceField.hidden = !isVideo;
        source.disabled = !isVideo;

        const isEmbed = isVideo && source.value === 'embed';
        uploadField.hidden = isEmbed;
        embedField.hidden = !isEmbed;
        file.disabled = isEmbed;
        url.disabled = !isEmbed;

        file.accept = isVideo
          ? 'video/mp4,video/webm,video/quicktime'
          : 'image/jpeg,image/png,image/webp';

        hint.firstChild.textContent = isVideo
          ? 'MP4, WebM, atau MOV. Maksimal 100MB. '
          : 'JPG, PNG, atau WebP. Maksimal 10MB. ';
      }

      type.addEventListener('change', sync);
      source.addEventListener('change', sync);
      sync();
    })();
  </script>
@endsection
