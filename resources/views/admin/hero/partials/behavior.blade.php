  <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
    (function () {
      var form = document.querySelector('[data-hero-media-form]');
      if (!form) return;

      var typeSelect = form.querySelector('[data-hero-type]');
      var typeHint = form.querySelector('[data-hero-type-hint]');
      var mediaFile = form.querySelector('[data-hero-media-file]');
      var mediaFileLabel = form.querySelector('[data-hero-media-file-label]');
      var mediaFileHint = form.querySelector('[data-hero-media-file-hint]');
      var mediaUrl = form.querySelector('[data-hero-media-url]');
      var mediaUrlLabel = form.querySelector('[data-hero-media-url-label]');
      var mediaUrlHint = form.querySelector('[data-hero-media-url-hint]');
      var videoOnlyFields = Array.prototype.slice.call(form.querySelectorAll('[data-hero-video-only]'));
      var posterInputs = Array.prototype.slice.call(form.querySelectorAll('[data-hero-poster-input]'));

      if (!typeSelect || !mediaFile || !mediaUrl) return;

      function syncMediaFields(resetSelectedFile) {
        var isVideo = typeSelect.value === 'video';

        if (resetSelectedFile) {
          mediaFile.value = '';
        }

        mediaFile.accept = isVideo
          ? '.mp4,.webm,.ogg,.ogv,video/mp4,video/webm,video/ogg'
          : '.jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp';

        if (mediaFileLabel) {
          mediaFileLabel.textContent = isVideo ? 'Upload video' : 'Upload gambar';
        }

        if (mediaFileHint) {
          mediaFileHint.textContent = isVideo
            ? 'MP4, WebM, OGG, atau OGV maksimal 50 MB. Kosongkan untuk mempertahankan video yang ada.'
            : 'JPG/JPEG, PNG, atau WebP maksimal 10 MB. Kosongkan untuk mempertahankan gambar yang ada.';
        }

        if (mediaUrlLabel) {
          mediaUrlLabel.textContent = isVideo ? 'URL video' : 'URL gambar';
        }

        mediaUrl.placeholder = isVideo
          ? 'https://.../video.mp4'
          : 'https://.../gambar.jpg';

        if (mediaUrlHint) {
          mediaUrlHint.textContent = isVideo
            ? 'Video harus berupa URL HTTPS langsung ke MP4/WebM/OGG/OGV. Link YouTube tidak digunakan untuk background hero.'
            : 'Gunakan URL gambar publik. Jika kosong, thumbnail artikel akan dipakai sebagai fallback.';
        }

        if (typeHint) {
          typeHint.textContent = isVideo
            ? 'Mode video aktif. Opsi poster ditampilkan di bawah.'
            : 'Mode gambar aktif. Opsi poster video disembunyikan.';
        }

        videoOnlyFields.forEach(function (field) {
          field.hidden = !isVideo;
        });

        posterInputs.forEach(function (input) {
          input.disabled = !isVideo;
        });
      }

      typeSelect.addEventListener('change', function () {
        syncMediaFields(true);
      });

      syncMediaFields(false);
    })();
  </script>
