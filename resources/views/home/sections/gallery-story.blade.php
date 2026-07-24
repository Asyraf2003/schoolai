          <div
            class="galeri-story"
            id="galeriGrid"
            data-gallery-story
            data-lightbox-label="{{ $gallerySection['lightbox_label'] ?? __('home.galeri.lightbox_label') }}"
            data-close-label="{{ $gallerySection['close_label'] ?? __('home.galeri.close_label') }}"
            data-video-title="{{ $gallerySection['video_title'] ?? __('home.galeri.video_title') }}"
          >
            <div class="galeri-story__copy" aria-label="{{ $gallerySection['aria_label'] ?? __('home.galeri.aria_label') }}">
              @foreach ($gallerySection['items'] as $item)
                <div
                  class="galeri-story-card{{ $loop->first ? ' is-active' : '' }}"
                  tabindex="0"
                  role="button"
                  data-gallery-story-item
                  data-gallery-index="{{ $loop->index }}"
                  data-title="{{ $item['title'] ?? '' }}"
                  data-caption="{{ $item['caption'] ?? '' }}"
                  data-category="{{ $item['category'] ?? '' }}"
                  data-date="{{ $item['date'] ?? '' }}"
                  data-type-label="{{ $item['type_label'] ?? ($gallerySection['default_type_label'] ?? __('home.galeri.default_type_label')) }}"
                  data-media-url="{{ $item['media_url'] ?? '' }}"
                  data-is-video="{{ ! empty($item['is_video']) ? '1' : '0' }}"
                  style="--g1: {{ $item['g1'] ?? 'var(--color-orange)' }}; --g2: {{ $item['g2'] ?? 'var(--color-yellow)' }}; --gallery-accent: {{ $item['accent'] ?? '#f97316' }}"
                >
                  <span class="galeri-story-card__number">
                    {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                  </span>

                  <div class="galeri-story-card__mobile-media">
                    @if (! empty($item['is_video']))
                      @if (! empty($item['thumbnail_url']))
                        <img
                          data-lazy-media
                          data-lazy-src="{{ $item['thumbnail_url'] }}"
                          alt="{{ $item['title'] }}"
                          class="galeri-story-card__mobile-image"
                          loading="lazy"
                          decoding="async"
                        />
                        <span class="galeri-story-card__mobile-play" aria-hidden="true">▶</span>
                      @else
                        <span
                          class="galeri-story-card__mobile-fallback social-video-cover social-video-cover--{{ $item['video_provider'] ?? 'video' }}"
                          aria-hidden="true"
                        >
                          @if (! empty($item['video_provider_logo_url']))
                            <img
                              src="{{ $item['video_provider_logo_url'] }}"
                              alt=""
                              class="social-video-cover__logo"
                              loading="lazy"
                              decoding="async"
                            />
                          @endif

                          <span class="social-video-cover__brand">
                            {{ $item['video_provider_label'] ?? __('pages.common.media_video') }}
                          </span>
                          <span class="social-video-cover__hint">
                            {{ __('pages.common.play_media') }}
                          </span>
                        </span>
                        <span class="galeri-story-card__mobile-play social-video-cover__play" aria-hidden="true">▶</span>
                      @endif
                    @elseif (! empty($item['media_url']))
                      <img
                        data-lazy-media
                        data-lazy-src="{{ $item['media_url'] }}"
                        alt="{{ $item['title'] }}"
                        class="galeri-story-card__mobile-image"
                        loading="lazy"
                        decoding="async"
                      />
                    @else
                      <span class="galeri-story-card__mobile-fallback">
                        {{ $item['fallback_icon'] ?? ($item['emoji'] ?? '📸') }}
                      </span>
                    @endif

                    <span class="galeri-story-card__mobile-badge">
                      {{ $item['type_label'] ?? ($gallerySection['default_type_label'] ?? __('home.galeri.default_type_label')) }}
                    </span>
                  </div>

                  <div class="galeri-story-card__content">
                    <div class="galeri-story-card__meta">
                      @if (! empty($item['category']))
                        <span>{{ $item['category'] }}</span>
                      @endif
                      <span>{{ $item['type_label'] ?? ($gallerySection['default_type_label'] ?? __('home.galeri.default_type_label')) }}</span>
                      @if (! empty($item['date']))
                        <span>{{ $item['date'] }}</span>
                      @endif
                    </div>

                    <h3>{{ $item['title'] }}</h3>

                    @if (! empty($item['caption']))
                      <p>{{ $item['caption'] }}</p>
                    @endif
                  </div>
                </div>
              @endforeach
            </div>

            <aside class="galeri-story__visual" aria-label="{{ $gallerySection['visual_aria_label'] ?? __('home.galeri.visual_aria_label') }}">
              <div class="galeri-story-visual__track" data-gallery-visual-track>
                @foreach ($gallerySection['items'] as $item)
                  <div
                    class="galeri-story-visual__panel{{ $loop->first ? ' is-active' : '' }}"
                    data-gallery-visual-panel
                    data-gallery-index="{{ $loop->index }}"
                    role="button"
                    tabindex="0"
                    aria-label="{{ $gallerySection['open_media_prefix'] ?? __('home.galeri.open_media_prefix') }} {{ $item['title'] ?? ($gallerySection['fallback_item_label'] ?? __('home.galeri.fallback_item_label')) }}"
                    style="--g1: {{ $item['g1'] ?? 'var(--color-orange)' }}; --g2: {{ $item['g2'] ?? 'var(--color-yellow)' }}; --gallery-accent: {{ $item['accent'] ?? '#f97316' }}"
                  >
                    <div class="galeri-story-visual__media">
                      @if (! empty($item['is_video']))
                        @if (! empty($item['thumbnail_url']))
                          <img
                            data-lazy-media
                            data-lazy-src="{{ $item['thumbnail_url'] }}"
                            alt="{{ $item['title'] }}"
                            class="galeri-story-visual__image"
                            loading="lazy"
                            decoding="async"
                          />
                          <span class="galeri-story-visual__play" aria-hidden="true">▶</span>
                        @else
                          <span
                            class="galeri-story-visual__fallback social-video-cover social-video-cover--{{ $item['video_provider'] ?? 'video' }}"
                            aria-hidden="true"
                          >
                            @if (! empty($item['video_provider_logo_url']))
                              <img
                                src="{{ $item['video_provider_logo_url'] }}"
                                alt=""
                                class="social-video-cover__logo"
                                loading="lazy"
                                decoding="async"
                              />
                            @endif

                            <span class="social-video-cover__brand">
                              {{ $item['video_provider_label'] ?? __('pages.common.media_video') }}
                            </span>
                            <span class="social-video-cover__hint">
                              {{ __('pages.common.play_media') }}
                            </span>
                          </span>
                          <span class="galeri-story-visual__play social-video-cover__play" aria-hidden="true">▶</span>
                        @endif
                      @elseif (! empty($item['media_url']))
                        <img
                          data-lazy-media
                          data-lazy-src="{{ $item['media_url'] }}"
                          alt="{{ $item['title'] }}"
                          class="galeri-story-visual__image"
                          loading="lazy"
                          decoding="async"
                        />
                      @else
                        <span class="galeri-story-visual__fallback">
                          {{ $item['fallback_icon'] ?? ($item['emoji'] ?? '📸') }}
                        </span>
                      @endif

                      <span class="galeri-story-visual__badge">
                        {{ $item['type_label'] ?? ($gallerySection['default_type_label'] ?? __('home.galeri.default_type_label')) }}
                      </span>
                    </div>
                  </div>
                @endforeach
              </div>
            </aside>
          </div>
