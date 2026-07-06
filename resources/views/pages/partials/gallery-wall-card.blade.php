@php
  $type = $item['type'] ?? 'photo';
  $isVideo = $type === 'video';
  $title = $item['title'] ?? '';
  $label = $item['label'] ?? $title;
  $mediaUrl = $item['media_url'] ?? null;
  $emoji = $item['emoji'] ?? ($isVideo ? '▶️' : '📸');
  $badge = $item['badge'] ?? ($isVideo ? 'Video' : 'Foto');
  $gradient = $item['gradient'] ?? ['#DCF1F7', '#FFC93C'];
@endphp

<article
  class="gallery-wall-card reveal"
  tabindex="0"
  role="button"
  data-gallery-wall-card
  data-gallery-title="{{ $label }}"
  data-gallery-media-url="{{ $mediaUrl ?? '' }}"
  data-gallery-is-video="{{ $isVideo ? '1' : '0' }}"
  data-gallery-emoji="{{ $emoji }}"
  data-gallery-badge="{{ $badge }}"
  style="--gallery-g1: {{ $gradient[0] ?? '#DCF1F7' }}; --gallery-g2: {{ $gradient[1] ?? '#FFC93C' }};"
>
  <div class="gallery-wall-card__media">
    @if($mediaUrl && $isVideo)
      <iframe
        data-lazy-media
        data-lazy-src="{{ $mediaUrl }}"
        title="{{ $label }}"
        loading="lazy"
        allow="fullscreen; picture-in-picture"
        allowfullscreen
        referrerpolicy="strict-origin-when-cross-origin"
      ></iframe>
    @elseif($mediaUrl)
      <img
        data-lazy-media
        data-lazy-src="{{ $mediaUrl }}"
        alt="{{ $label }}"
        loading="lazy"
        decoding="async"
      >
    @else
      <span aria-hidden="true">{{ $emoji }}</span>
    @endif
  </div>

  @if($badge || $title)
    <div class="gallery-wall-card__caption">
      @if($badge)
        <span>{{ $badge }}</span>
      @endif

      @if($title)
        <h3>{{ $title }}</h3>
      @endif
    </div>
  @endif
</article>
