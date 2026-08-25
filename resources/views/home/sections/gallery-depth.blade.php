<div
  class="gallery-story"
  data-depth-gallery
  data-gallery-story
  style="--gallery-story-count: {{ max(1, $depthItems->count()) }}"
>
  <div class="gallery-story__handoff" data-gallery-story-handoff aria-hidden="true"></div>

  <div class="gallery-story__title-rail" aria-hidden="true">
    <h2
      class="gallery-story__title"
      id="homepage-gallery-heading"
      data-gallery-story-title
    >
      {{ $galleryHeading }}
    </h2>
  </div>

  <div class="gallery-story__intro" data-gallery-story-intro>
    <span class="sr-only">{{ $galleryHeading }}</span>
  </div>

  <div
    class="gallery-story__stream"
    role="list"
    aria-label="{{ $gallerySection['aria_label'] ?? __('home.galeri.aria_label') }}"
  >
    @foreach ($depthItems as $item)
      <article
        class="gallery-story__item gallery-story__item--{{ ($loop->index % 5) + 1 }}"
        data-gallery-story-item
        role="listitem"
      >
        <figure class="gallery-story__media" data-gallery-story-media>
          @if ($item['thumbnail_url'] !== '')
            <img
              src="{{ $item['thumbnail_url'] }}"
              alt="{{ $item['title'] }}"
              width="1400"
              height="1050"
              loading="lazy"
              decoding="async"
            />
          @else
            <span class="gallery-story__media-fallback" aria-hidden="true">
              {{ $item['fallback_icon'] ?? '📸' }}
            </span>
          @endif
        </figure>

        <div class="gallery-story__copy" data-gallery-story-copy>
          <h3>{{ $item['title'] }}</h3>
          @if ($item['caption'] !== '')
            <p>{{ $item['caption'] }}</p>
          @endif
        </div>
      </article>
    @endforeach
  </div>

  <div class="gallery-story__closing" data-gallery-story-closing>
    <div class="gallery-story__closing-inner">
      @if ($depthClosingCopy !== '')
        <p>{{ $depthClosingCopy }}</p>
      @endif

      @if ($hasDepthCta)
        <a
          class="gallery-story__closing-link"
          href="{{ $depthCta['href'] }}"
          data-depth-gallery-end-link
        >
          <span>{{ $depthCta['label'] }}</span>
          <span aria-hidden="true">↗</span>
        </a>
      @endif
    </div>
  </div>
</div>
