<div
  class="depth-gallery is-depth-fallback"
  data-depth-gallery
  data-depth-gallery-end-steps="{{ $depthEndSteps }}"
  style="--depth-gallery-count: {{ $depthJourneyCount }}"
>
  <div
    class="depth-gallery__journey"
    data-depth-gallery-journey
    data-depth-gallery-transition="sticky-scale"
  >
    <div class="depth-gallery__viewport" data-depth-gallery-viewport>
      <canvas
        class="depth-gallery__canvas"
        data-depth-gallery-canvas
        aria-hidden="true"
      ></canvas>

      <section class="depth-gallery__labels" data-depth-gallery-labels aria-hidden="true">
        <article class="depth-gallery__copy" data-depth-gallery-copy>
          <p class="depth-gallery__copy-title" data-depth-gallery-title></p>
          <p class="depth-gallery__copy-caption" data-depth-gallery-caption></p>
        </article>
      </section>

      <div
        class="depth-gallery__fallback-list"
        data-depth-gallery-fallback
        role="list"
        aria-label="{{ $gallerySection['aria_label'] ?? __('home.galeri.aria_label') }}"
      >
        @foreach ($depthItems as $item)
          <article
            class="depth-gallery__fallback-item"
            role="listitem"
            data-depth-gallery-source
            data-gallery-index="{{ $loop->index }}"
            data-title="{{ $item['title'] }}"
            data-caption="{{ $item['caption'] }}"
            data-thumbnail-url="{{ $item['thumbnail_url'] }}"
            data-position-x="{{ $item['preset']['x'] }}"
            data-fallback-color="{{ $item['preset']['fallback'] }}"
            data-accent-color="{{ $item['preset']['accent'] }}"
            data-background-color="{{ $item['preset']['background'] }}"
            data-blob1-color="{{ $item['preset']['blob1'] }}"
            data-blob2-color="{{ $item['preset']['blob2'] }}"
          >
            <div class="depth-gallery__fallback-card">
              <span class="depth-gallery__fallback-media">
                @if ($item['thumbnail_url'] !== '')
                  <img
                    src="{{ $item['thumbnail_url'] }}"
                    alt="{{ $item['title'] }}"
                    loading="lazy"
                    decoding="async"
                  />
                @else
                  <span aria-hidden="true">{{ $item['fallback_icon'] ?? '📸' }}</span>
                @endif
              </span>
              <span class="depth-gallery__fallback-copy">
                <strong>{{ $item['title'] }}</strong>
                @if ($item['caption'] !== '')
                  <small>{{ $item['caption'] }}</small>
                @endif
              </span>
            </div>
          </article>
        @endforeach
      </div>

      @if ($hasDepthCta)
        <div class="depth-gallery__end" data-depth-gallery-end>
          <div class="depth-gallery__end-showcase" aria-hidden="true">
            @foreach ($depthClosingMedia as $closingItem)
              <figure class="depth-gallery__end-media depth-gallery__end-media--{{ $loop->iteration }}">
                @if (! empty($closingItem['thumbnail_url']))
                  <img
                    src="{{ $closingItem['thumbnail_url'] }}"
                    alt=""
                    width="1200"
                    height="900"
                    loading="lazy"
                    decoding="async"
                  />
                @else
                  <span aria-hidden="true">{{ $closingItem['fallback_icon'] ?? '📸' }}</span>
                @endif
              </figure>
            @endforeach
          </div>

          <div class="depth-gallery__end-copy">
            @if ($depthClosingCopy !== '')
              <p>{{ $depthClosingCopy }}</p>
            @endif

            <a
              class="depth-gallery__end-link"
              href="{{ $depthCta['href'] }}"
              data-depth-gallery-end-link
            >
              <span>{{ $depthCta['label'] }}</span>
              <span aria-hidden="true">↗</span>
            </a>
          </div>
        </div>
      @endif
    </div>
  </div>
</div>
