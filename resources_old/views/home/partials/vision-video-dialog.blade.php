  <dialog class="vision-video-modal" data-about-video-modal aria-label="{{ $sectionLabel }} video">
    <div class="vision-video-modal__surface" data-about-video-shell>
      <button
        type="button"
        class="vision-video-modal__close"
        data-about-video-close
        aria-label="Close video"
      >
        <span aria-hidden="true">×</span>
      </button>

      <video
        class="vision-video-modal__player"
        data-about-video-player
        data-about-video-src="{{ config('media.homepage_about_video_url') }}"
        playsinline
        webkit-playsinline
        preload="none"
      ></video>

      <div class="vision-video-modal__controls" data-about-video-controls>
        <button
          type="button"
          class="vision-video-modal__control vision-video-modal__control--play"
          data-about-video-toggle
          aria-label="Play video"
        >
          <span data-about-video-toggle-icon aria-hidden="true">▶</span>
        </button>

        <span class="vision-video-modal__time" data-about-video-current aria-hidden="true">0:00</span>

        <input
          type="range"
          class="vision-video-modal__seek"
          data-about-video-seek
          min="0"
          max="1000"
          step="1"
          value="0"
          aria-label="Video timeline"
        >

        <span class="vision-video-modal__time" data-about-video-duration aria-hidden="true">0:00</span>

        <button
          type="button"
          class="vision-video-modal__control vision-video-modal__control--mute"
          data-about-video-mute
          aria-label="Mute video"
        >
          <span data-about-video-mute-icon aria-hidden="true">●</span>
        </button>

        <input
          type="range"
          class="vision-video-modal__volume"
          data-about-video-volume
          min="0"
          max="1"
          step="0.05"
          value="1"
          aria-label="Video volume"
        >

        <button
          type="button"
          class="vision-video-modal__control vision-video-modal__control--fullscreen"
          data-about-video-fullscreen
          aria-label="Enter fullscreen"
        >
          <span aria-hidden="true">⛶</span>
        </button>
      </div>
    </div>
  </dialog>
