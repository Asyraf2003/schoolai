@php
  $depthPalettes = [
      ['#c78f2b', '#ffd166', '#f4a261'],
      ['#34785f', '#7bd5a7', '#d9c857'],
      ['#4f6f9f', '#78c8e8', '#c9b8ff'],
      ['#9d4d5f', '#ff9a76', '#e8b5cf'],
      ['#66558f', '#a99bea', '#e0b96d'],
      ['#60783c', '#9fd06e', '#f0c75e'],
  ];
  $depthItems = collect($gallerySection['items'] ?? [])->values();
  $depthCount = max(1, $depthItems->count());
  $initialAtmosphere = $depthPalettes[0][0];
@endphp

<div
  class="depth-gallery"
  data-depth-gallery
  data-lightbox-label="{{ $gallerySection['lightbox_label'] ?? __('home.galeri.lightbox_label') }}"
  data-close-label="{{ $gallerySection['close_label'] ?? __('home.galeri.close_label') }}"
  data-video-title="{{ $gallerySection['video_title'] ?? __('home.galeri.video_title') }}"
  style="--depth-gallery-count: {{ $depthCount }}; --depth-atmosphere: {{ $initialAtmosphere }}"
>
  <div class="depth-gallery__journey" data-depth-gallery-journey>
    <div class="depth-gallery__viewport" data-depth-gallery-viewport>
      <canvas class="depth-gallery__canvas" data-depth-gallery-canvas aria-hidden="true"></canvas>
      <div class="depth-gallery__veil" aria-hidden="true"></div>

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
            $isReversed = $loop->index % 2 === 1;
            $mediaUrl = (string) ($item['media_url'] ?? '');
            $thumbnailUrl = (string) ($item['thumbnail_url'] ?? '');
            $itemTitle = (string) ($item['title'] ?? '');
            $itemType = (string) ($item['type_label'] ?? (
                $gallerySection['default_type_label'] ?? __('home.galeri.default_type_label')
            ));
          @endphp

          <article class="depth-gallery__item" role="listitem" data-depth-gallery-item>
            <a
              class="depth-gallery__card{{ $isReversed ? ' depth-gallery__card--reverse' : '' }}"
              href="{{ $mediaUrl }}"
              data-depth-gallery-card
              data-gallery-index="{{ $loop->index }}"
              data-title="{{ $itemTitle }}"
              data-caption="{{ $item['caption'] ?? '' }}"
              data-category="{{ $item['category'] ?? '' }}"
              data-date="{{ $item['date'] ?? '' }}"
              data-type-label="{{ $itemType }}"
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
                <span class="depth-gallery__eyebrow">
                  <b>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</b>
                  <span>{{ $itemType }}</span>
                  @if (! empty($item['date']))
                    <span>{{ $item['date'] }}</span>
                  @endif
                </span>
                <strong>{{ $itemTitle }}</strong>
                @if (! empty($item['caption']))
                  <small>{{ $item['caption'] }}</small>
                @endif
              </span>
            </a>
          </article>
        @endforeach
      </div>

      <div class="depth-gallery__meter" aria-hidden="true">
        <span data-depth-gallery-progress></span>
      </div>
    </div>
  </div>
</div>
