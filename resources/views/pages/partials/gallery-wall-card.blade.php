<div
  class="gallery-wall-card reveal"
  tabindex="0"
  role="button"
  data-gallery-wall-card
  data-gallery-title="{{ $label }}"
  data-gallery-media-url="{{ $mediaUrl }}"
  data-gallery-thumb-url="{{ $thumbnailUrl ?? '' }}"
  data-gallery-is-video="{{ $isVideo ? '1' : '0' }}"
  data-gallery-is-direct-video="{{ $isDirectVideo ? '1' : '0' }}"
  data-gallery-emoji="{{ $emoji }}"
  data-gallery-badge="{{ $badge }}"
  style="--gallery-g1: {{ $gradient[0] ?? '#DCF1F7' }}; --gallery-g2: {{ $gradient[1] ?? '#FFC93C' }};"
>
  <div class="gallery-wall-card__media">
    @if($isDirectVideo)
      <video
        class="gallery-wall-card__direct-video"
        src="{{ $mediaUrl }}"
        muted
        playsinline
        webkit-playsinline
        preload="metadata"
        aria-hidden="true"
      ></video>
      <span class="gallery-wall-card__play" aria-hidden="true">▶</span>
    @elseif($isVideo && $thumbnailUrl)
      <img
        data-lazy-media
        data-lazy-src="{{ $thumbnailUrl }}"
        alt="{{ $label }}"
        loading="lazy"
        decoding="async"
      >
      <span class="gallery-wall-card__play" aria-hidden="true">▶</span>
    @elseif($isVideo)
      <span
        class="gallery-wall-card__fallback social-video-cover social-video-cover--{{ $videoProvider }}"
        aria-hidden="true"
      >
        @if($videoProviderLogoUrl)
          <img
            src="{{ $videoProviderLogoUrl }}"
            alt=""
            class="social-video-cover__logo"
            loading="lazy"
            decoding="async"
          >
        @endif

        <span class="social-video-cover__brand">{{ $videoProviderLabel }}</span>
        <span class="social-video-cover__hint">{{ __('pages.common.play_media') }}</span>
      </span>
      <span class="gallery-wall-card__play social-video-cover__play" aria-hidden="true">▶</span>
    @else
      <img
        data-lazy-media
        data-lazy-src="{{ $mediaUrl }}"
        alt="{{ $label }}"
        loading="lazy"
        decoding="async"
      >
    @endif
  </div>
</div>
