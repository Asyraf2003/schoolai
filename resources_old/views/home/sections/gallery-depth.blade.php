<div
  class="gallery-story"
  data-depth-gallery
  data-gallery-story
  style="--gallery-story-count: {{ max(1, $depthItems->count()) }}"
>
  <div class="gallery-story__handoff" data-gallery-story-handoff aria-hidden="true"></div>

  <div class="gallery-story__intro" data-gallery-story-intro>
    <div class="gallery-story__title-rail home-section-display__header">
      <h2
        class="gallery-story__title home-section-display__title"
        id="homepage-gallery-heading"
        data-text-role="display"
        data-gallery-story-title
      >
        <span class="gallery-story__title-line home-section-display__line">{{ $galleryHeading }}</span>
      </h2>
    </div>
  </div>

  <div
    class="gallery-story__stream"
  >
    @foreach ($depthItems as $item)
      <article
        class="gallery-story__item gallery-story__item--{{ $loop->odd ? 'right' : 'left' }}"
        data-gallery-story-item
        data-gallery-background="{{ $item['preset']['background'] }}"
      >
        <figure class="gallery-story__media" data-gallery-story-media>
          @if ($item['is_direct_video'] && $item['media_url'] !== '')
            <video
              class="gallery-story__visual gallery-story__video"
              data-gallery-story-visual
              data-gallery-video-preview
              data-gallery-video-src="{{ $item['media_url'] }}"
              muted
              loop
              playsinline
              webkit-playsinline
              preload="none"
              @if ($item['thumbnail_url'] !== '')
                poster="{{ $item['thumbnail_url'] }}"
              @endif
              aria-hidden="true"
            ></video>
          @elseif ($item['thumbnail_url'] !== '')
            <img
              class="gallery-story__visual"
              data-gallery-story-visual
              src="{{ $item['thumbnail_url'] }}"
              alt="{{ $item['title'] }}"
              @if (($item['media_width'] ?? 0) > 0 && ($item['media_height'] ?? 0) > 0)
                width="{{ (int) $item['media_width'] }}"
                height="{{ (int) $item['media_height'] }}"
              @endif
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
            <a
              class="gallery-story__item-link gallery-story__item-link--filled"
              href="{{ $depthCta['href'] }}"
              data-depth-gallery-end-link
            >
              <span class="gallery-story__cta-label">{{ $galleryMoreLabel }}</span>
              <span class="gallery-story__cta-icon" aria-hidden="true">↗</span>
            </a>
          @endif
        </div>
      </article>
    @endforeach
  </div>
</div>
