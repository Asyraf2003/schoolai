<div
  class="gallery-story"
  data-depth-gallery
  data-gallery-story
  style="--gallery-story-count: {{ max(1, $depthItems->count()) }}"
>
  <div class="gallery-story__handoff" data-gallery-story-handoff aria-hidden="true"></div>

  <div class="gallery-story__intro" data-gallery-story-intro>
    <div class="gallery-story__title-rail">
      <h2
        class="gallery-story__title"
        id="homepage-gallery-heading"
        data-text-role="display"
        data-gallery-story-title
      >
        <span class="gallery-story__title-line">{{ $galleryHeading }}</span>
      </h2>
    </div>
  </div>

  <div
    class="gallery-story__stream"
    role="list"
    aria-label="{{ $gallerySection['aria_label'] ?? __('home.galeri.aria_label') }}"
  >
    @foreach ($depthItems as $item)
      <article
        class="gallery-story__item gallery-story__item--{{ $loop->odd ? 'right' : 'left' }}"
        data-gallery-story-item
        data-gallery-background="{{ $item['preset']['background'] }}"
        role="listitem"
      >
        <figure class="gallery-story__media" data-gallery-story-media>
          @if ($item['thumbnail_url'] !== '')
            <img
              class="gallery-story__visual"
              data-gallery-story-visual
              src="{{ $item['thumbnail_url'] }}"
              alt="{{ $item['title'] }}"
              width="1400"
              height="1050"
              loading="lazy"
              decoding="async"
            />
          @else
            <span
              class="gallery-story__media-fallback gallery-story__visual"
              data-gallery-story-visual
              aria-hidden="true"
            >
              {{ $item['fallback_icon'] ?? '📸' }}
            </span>
          @endif
        </figure>

        <div class="gallery-story__copy" data-gallery-story-copy>
          <h3>{{ $item['title'] }}</h3>
          @if ($item['caption'] !== '')
            <p>{{ $item['caption'] }}</p>
          @endif

          @if ($loop->last && $hasDepthCta)
            <div class="gallery-story__cta-variants" aria-label="{{ __('Pilihan tampilan tombol galeri') }}">
              <a
                class="gallery-story__item-link gallery-story__item-link--filled"
                href="{{ $depthCta['href'] }}"
                data-depth-gallery-end-link
                data-gallery-cta-variant="filled"
              >
                <span class="gallery-story__cta-label">{{ $galleryMoreLabel }}</span>
                <span class="gallery-story__cta-icon" aria-hidden="true">↗</span>
              </a>

              <a
                class="gallery-story__item-link gallery-story__item-link--outline"
                href="{{ $depthCta['href'] }}"
                data-gallery-cta-variant="outline"
              >
                <span class="gallery-story__cta-label">{{ $galleryMoreLabel }}</span>
                <span class="gallery-story__cta-icon" aria-hidden="true">↗</span>
              </a>

              <a
                class="gallery-story__item-link gallery-story__item-link--split"
                href="{{ $depthCta['href'] }}"
                data-gallery-cta-variant="split"
              >
                <span class="gallery-story__cta-label">{{ $galleryMoreLabel }}</span>
                <span class="gallery-story__cta-icon" aria-hidden="true">↗</span>
              </a>
            </div>
          @endif
        </div>
      </article>
    @endforeach
  </div>
</div>
