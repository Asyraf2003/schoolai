@php
  $type = $item['type'] ?? 'photo';
  $isVideo = $type === 'video';
  $title = $item['title'] ?? '';
  $label = $item['label'] ?? $title;
  $mediaUrl = $item['media_url'] ?? null;
  $thumbnailUrl = $item['thumbnail_url'] ?? null;

  $fallbackImages = [
    'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1400&q=82',
    'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1400&q=82',
    'https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=1400&q=82',
    'https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=1400&q=82',
    'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1400&q=82',
    'https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&w=1400&q=82',
  ];

  if (! is_string($mediaUrl) || trim($mediaUrl) === '') {
    $fallbackKey = $label !== '' ? $label : 'Al Mustaqbal School';
    $fallbackIndex = ((int) sprintf('%u', crc32($fallbackKey))) % count($fallbackImages);
    $mediaUrl = $fallbackImages[$fallbackIndex];
    $thumbnailUrl = $mediaUrl;
    $type = 'photo';
    $isVideo = false;
  }

  $videoProvider = $item['video_provider'] ?? 'video';
  $videoProviderLogoUrl = $item['video_provider_logo_url'] ?? null;
  $videoProviderLabel = $item['video_provider_label'] ?? __('pages.common.media_video');
  $emoji = $item['emoji'] ?? ($isVideo ? '▶️' : '📸');
  $badge = $isVideo
    ? ($item['badge'] ?? __('pages.common.media_video'))
    : __('pages.common.media_photo');
  $gradient = $item['gradient'] ?? ['#DCF1F7', '#FFC93C'];
@endphp

<div
  class="gallery-wall-card reveal"
  tabindex="0"
  role="button"
  data-gallery-wall-card
  data-gallery-title="{{ $label }}"
  data-gallery-media-url="{{ $mediaUrl }}"
  data-gallery-thumb-url="{{ $thumbnailUrl ?? '' }}"
  data-gallery-is-video="{{ $isVideo ? '1' : '0' }}"
  data-gallery-emoji="{{ $emoji }}"
  data-gallery-badge="{{ $badge }}"
  style="--gallery-g1: {{ $gradient[0] ?? '#DCF1F7' }}; --gallery-g2: {{ $gradient[1] ?? '#FFC93C' }};"
>
  <div class="gallery-wall-card__media">
    @if($isVideo && $thumbnailUrl)
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
