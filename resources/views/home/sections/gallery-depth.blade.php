@php
  $depthPalettes = [
      ['#d9a327', '#f8c85d', '#e7c88d'],
      ['#bfd96b', '#e7ef94', '#8eb89a'],
      ['#5f81ab', '#f88b8d', '#cfbbdd'],
      ['#5b9bc2', '#ffaa00', '#00e1ff'],
      ['#7d936e', '#fdd895', '#a5b599'],
      ['#bb96af', '#f4c5a7', '#d29a41'],
  ];
  $depthItems = collect($gallerySection['items'] ?? [])->values();
  $depthCount = max(1, $depthItems->count());
@endphp

<div
  class="depth-gallery"
  data-depth-gallery
  data-lightbox-label="{{ $gallerySection['lightbox_label'] ?? __('home.galeri.lightbox_label') }}"
  data-close-label="{{ $gallerySection['close_label'] ?? __('home.galeri.close_label') }}"
  data-video-title="{{ $gallerySection['video_title'] ?? __('home.galeri.video_title') }}"
  style="--depth-gallery-count: {{ $depthCount }}"
>
  <div class="depth-gallery__journey" data-depth-gallery-journey>
    <div class="depth-gallery__viewport" data-depth-gallery-viewport>
      <canvas class="depth-gallery__canvas" data-depth-gallery-canvas aria-hidden="true"></canvas>
      <div class="depth-gallery__veil" aria-hidden="true"></div>

      <svg
        class="depth-gallery__trail"
        viewBox="0 0 1000 1000"
        preserveAspectRatio="none"
        aria-hidden="true"
      >
        <path
          class="depth-gallery__trail-glow"
          d="M 72 960 C 80 760 330 810 288 610 C 246 410 740 540 690 300 C 650 118 890 210 930 34"
        />
        <path
          class="depth-gallery__trail-line"
          data-depth-gallery-trail
          pathLength="1"
          d="M 72 960 C 80 760 330 810 288 610 C 246 410 740 540 690 300 C 650 118 890 210 930 34"
        />
      </svg>

      <div
        class="depth-gallery__stage"
        data-depth-gallery-stage
        role="list"
        aria-label="{{ $gallerySection['aria_label'] ?? __('home.galeri.aria_label') }}"
      >
        @foreach ($depthItems as $item)
          @php
            $palette = $depthPalettes[$loop->index % count($depthPalettes)];
            $side = $loop->even ? 1 : -1;
            $mediaUrl = (string) ($item['media_url'] ?? '');
            $thumbnailUrl = (string) ($item['thumbnail_url'] ?? '');
            $itemTitle = (string) ($item['title'] ?? '');
          @endphp

          <article class="depth-gallery__item" role="listitem" data-depth-gallery-item>
            <a
              class="depth-gallery__card{{ $loop->even ? ' depth-gallery__card--reverse' : '' }}"
              href="{{ $mediaUrl }}"
              data-depth-gallery-card
              data-gallery-index="{{ $loop->index }}"
              data-title="{{ $itemTitle }}"
              data-caption="{{ $item['caption'] ?? '' }}"
              data-category="{{ $item['category'] ?? '' }}"
              data-date="{{ $item['date'] ?? '' }}"
              data-type-label="{{ $item['type_label'] ?? '' }}"
              data-media-url="{{ $mediaUrl }}"
              data-is-video="{{ ! empty($item['is_video']) ? '1' : '0' }}"
              style="--depth-side: {{ $side }}; --depth-bg: {{ $palette[0] }}; --depth-blob-a: {{ $palette[1] }}; --depth-blob-b: {{ $palette[2] }}"
              aria-label="{{ $gallerySection['open_media_prefix'] ?? __('home.galeri.open_media_prefix') }} {{ $itemTitle }}"
            >
              <span class="depth-gallery__media">
                @if ($thumbnailUrl !== '')
                  <img
                    class="depth-gallery__image"
                    src="{{ $thumbnailUrl }}"
                    alt="{{ $itemTitle }}"
                    loading="lazy"
                    decoding="async"
                  />
                @else
                  <span class="depth-gallery__fallback" aria-hidden="true">
                    {{ $item['fallback_icon'] ?? '📸' }}
                  </span>
                @endif

                @if (! empty($item['is_video']))
                  <span class="depth-gallery__play" aria-hidden="true">▶</span>
                @endif
              </span>

              <span class="depth-gallery__copy">
                <strong>{{ $itemTitle }}</strong>
                @if (! empty($item['caption']))
                  <small>{{ $item['caption'] }}</small>
                @endif
              </span>
            </a>
          </article>
        @endforeach
      </div>
    </div>
  </div>
</div>
