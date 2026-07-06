@php
  $type = $item['type'] ?? 'photo';
  $isVideo = $type === 'video';
  $title = $item['title'] ?? '';
  $label = $item['label'] ?? $title;
  $mediaUrl = $item['media_url'] ?? null;
  $thumbnailUrl = $item['thumbnail_url'] ?? null;
  $emoji = $item['emoji'] ?? ($isVideo ? '▶️' : '📸');
  $badge = $item['badge'] ?? ($isVideo ? __('pages.common.media_video') : __('pages.common.media_photo'));
  $gradient = $item['gradient'] ?? ['#DCF1F7', '#FFC93C'];
@endphp

<div
  class="gallery-wall-card reveal"
  tabindex="0"
  role="button"
  data-gallery-wall-card
  data-gallery-title="{{ $label }}"
  data-gallery-media-url="{{ $mediaUrl ?? '' }}"
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
      <span class="gallery-wall-card__fallback" aria-hidden="true">{{ $emoji }}</span>
      <span class="gallery-wall-card__play" aria-hidden="true">▶</span>
    @elseif($mediaUrl)
      <img
        data-lazy-media
        data-lazy-src="{{ $mediaUrl }}"
        alt="{{ $label }}"
        loading="lazy"
        decoding="async"
      >
    @else
      <span class="gallery-wall-card__fallback" aria-hidden="true">{{ $emoji }}</span>
    @endif
  </div>
</div>
