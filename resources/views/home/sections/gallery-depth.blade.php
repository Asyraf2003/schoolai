@php
  $depthPresets = [
      [
          'x' => -0.9,
          'fallback' => '#feca4f',
          'accent' => '#feca4f',
          'background' => '#fffaf0',
          'blob1' => '#ffdf94',
          'blob2' => '#fce7c4',
      ],
      [
          'x' => 0.8,
          'fallback' => '#80455a',
          'accent' => '#80455a',
          'background' => '#fffaf0',
          'blob1' => '#d29a41',
          'blob2' => '#bb96af',
      ],
      [
          'x' => -0.7,
          'fallback' => '#fa7b71',
          'accent' => '#fa7b71',
          'background' => '#5f81ab',
          'blob1' => '#f88b8d',
          'blob2' => '#cfbbdd',
      ],
      [
          'x' => 1,
          'fallback' => '#3c72c6',
          'accent' => '#3c72c6',
          'background' => '#5b9bc2',
          'blob1' => '#ffaa00',
          'blob2' => '#00e1ff',
      ],
      [
          'x' => -0.7,
          'fallback' => '#fdd895',
          'accent' => '#fdd895',
          'background' => '#7d936e',
          'blob1' => '#fdd895',
          'blob2' => '#a5b599',
      ],
  ];
  $depthItems = collect($gallerySection['items'] ?? [])->values();
  $depthCount = max(1, $depthItems->count());
@endphp

<div
  class="depth-gallery is-depth-fallback"
  data-depth-gallery
  data-lightbox-label="{{ $gallerySection['lightbox_label'] ?? __('home.galeri.lightbox_label') }}"
  data-close-label="{{ $gallerySection['close_label'] ?? __('home.galeri.close_label') }}"
  data-video-title="{{ $gallerySection['video_title'] ?? __('home.galeri.video_title') }}"
  data-open-prefix="{{ $gallerySection['open_media_prefix'] ?? __('home.galeri.open_media_prefix') }}"
  style="--depth-gallery-count: {{ $depthCount }}"
>
  <div class="depth-gallery__journey" data-depth-gallery-journey>
    <div class="depth-gallery__viewport" data-depth-gallery-viewport>
      <canvas
        class="depth-gallery__canvas"
        data-depth-gallery-canvas
        role="button"
        tabindex="0"
        aria-label="{{ $gallerySection['aria_label'] ?? __('home.galeri.aria_label') }}"
      ></canvas>

      <section
        class="depth-gallery__labels"
        data-depth-gallery-labels
        aria-live="polite"
        aria-atomic="true"
      >
        <div class="depth-gallery__label-left">
          <p data-depth-gallery-title></p>
        </div>
        <article class="depth-gallery__label-right">
          <p data-depth-gallery-caption></p>
        </article>
      </section>

      <div
        class="depth-gallery__fallback-list"
        data-depth-gallery-fallback
        role="list"
        aria-label="{{ $gallerySection['aria_label'] ?? __('home.galeri.aria_label') }}"
      >
        @foreach ($depthItems as $item)
          @php
            $preset = $depthPresets[$loop->index % count($depthPresets)];
            $mediaUrl = (string) ($item['media_url'] ?? '');
            $thumbnailUrl = (string) ($item['thumbnail_url'] ?? '');
            $itemTitle = (string) ($item['title'] ?? '');
            $itemCaption = (string) ($item['caption'] ?? '');
          @endphp

          <article class="depth-gallery__fallback-item" role="listitem">
            <a
              class="depth-gallery__fallback-card"
              href="{{ $mediaUrl }}"
              data-depth-gallery-source
              data-gallery-index="{{ $loop->index }}"
              data-title="{{ $itemTitle }}"
              data-caption="{{ $itemCaption }}"
              data-media-url="{{ $mediaUrl }}"
              data-thumbnail-url="{{ $thumbnailUrl }}"
              data-is-video="{{ ! empty($item['is_video']) ? '1' : '0' }}"
              data-position-x="{{ $preset['x'] }}"
              data-fallback-color="{{ $preset['fallback'] }}"
              data-accent-color="{{ $preset['accent'] }}"
              data-background-color="{{ $preset['background'] }}"
              data-blob1-color="{{ $preset['blob1'] }}"
              data-blob2-color="{{ $preset['blob2'] }}"
              aria-label="{{ $gallerySection['open_media_prefix'] ?? __('home.galeri.open_media_prefix') }} {{ $itemTitle }}"
            >
              <span class="depth-gallery__fallback-media">
                @if ($thumbnailUrl !== '')
                  <img
                    src="{{ $thumbnailUrl }}"
                    alt="{{ $itemTitle }}"
                    loading="lazy"
                    decoding="async"
                  />
                @else
                  <span aria-hidden="true">{{ $item['fallback_icon'] ?? '📸' }}</span>
                @endif
              </span>
              <span class="depth-gallery__fallback-copy">
                <strong>{{ $itemTitle }}</strong>
                @if ($itemCaption !== '')
                  <small>{{ $itemCaption }}</small>
                @endif
              </span>
            </a>
          </article>
        @endforeach
      </div>
    </div>
  </div>
</div>
