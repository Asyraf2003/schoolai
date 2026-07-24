      <!-- ======================= ARTIKEL ======================= -->
      <section class="artikel-section section artikel-section--digest" id="artikel" aria-labelledby="artikel-heading">
        <div class="container">
          <div class="section-head artikel-section__head">
            <h2 class="section-title" id="artikel-heading">{{ $articlesSection['title'] }}</h2>
            @if (! empty($articlesSection['subtitle']))
              <p class="section-subtitle artikel-section__subtitle">
                {{ $articlesSection['subtitle'] }}
              </p>
            @endif
          </div>

          @php
            $articleItems = array_slice($articlesSection['items'] ?? [], 0, 3);
            $featuredArticle = $articleItems[0] ?? null;
            $digestArticles = array_slice($articleItems, 1);
          @endphp

          @if ($featuredArticle)
            <div class="artikel-digest">
              <article
                class="artikel-digest__hero reveal"
                style="--artikel-g1: {{ $featuredArticle['gradient_from'] ?? 'var(--color-yellow-soft)' }}; --artikel-g2: {{ $featuredArticle['gradient_to'] ?? 'var(--color-orange-soft)' }}"
              >
                <div class="artikel-digest__hero-media">
                  @if (! empty($featuredArticle['thumbnail_url']))
                    <img
                      src="{{ $featuredArticle['thumbnail_url'] }}"
                      alt="{{ $featuredArticle['title'] }}"
                      class="artikel-digest__media-image"
                      loading="lazy"
                      decoding="async"
                    >
                  @else
                    <span class="artikel-digest__emoji" aria-hidden="true">{{ $featuredArticle['emoji'] ?? '📰' }}</span>
                  @endif

                  <span class="artikel-digest__issue" aria-hidden="true">{{ $featuredArticle['issue'] ?? '01' }}</span>
                  <span class="artikel-digest__spark artikel-digest__spark--one" aria-hidden="true"></span>
                  <span class="artikel-digest__spark artikel-digest__spark--two" aria-hidden="true"></span>
                </div>

                <div class="artikel-digest__hero-body">
                  <div class="artikel-digest__meta">
                    @if (! empty($featuredArticle['category']))
                      <span>{{ $featuredArticle['category'] }}</span>
                    @endif
                    <span>{{ $featuredArticle['date'] }}</span>
                    @if (! empty($featuredArticle['reading_time']))
                      <span>{{ $featuredArticle['reading_time'] }}</span>
                    @endif
                  </div>

                  <h3 class="artikel-digest__hero-title">
                    <a href="{{ $featuredArticle['href'] }}">
                      {{ $featuredArticle['title'] }}
                    </a>
                  </h3>

                  <p class="artikel-digest__hero-description">
                    {{ $featuredArticle['description'] }}
                  </p>

                  @if (! empty($featuredArticle['highlight']))
                    <p class="artikel-digest__highlight">
                      {{ $featuredArticle['highlight'] }}
                    </p>
                  @endif
                </div>
              </article>

              <div class="artikel-digest__rail" aria-label="{{ $articlesSection['rail_aria_label'] ?? __('home.artikel.rail_aria_label') }}">
                @forelse ($digestArticles as $article)
                  <article
                    class="artikel-digest-card reveal{{ $loop->index > 0 ? ' reveal--delay-' . min($loop->index, 3) : ' reveal--delay-1' }}"
                    style="--artikel-g1: {{ $article['gradient_from'] ?? 'var(--color-mint-soft)' }}; --artikel-g2: {{ $article['gradient_to'] ?? 'var(--color-blue-soft)' }}"
                  >
                    <a
                      href="{{ $article['href'] }}"
                      class="artikel-digest-card__link"
                      aria-label="{{ $articlesSection['read_more'] }}: {{ $article['title'] }}"
                    >
                      <span class="artikel-digest-card__media" aria-hidden="true">
                        @if (! empty($article['thumbnail_url']))
                          <img
                            src="{{ $article['thumbnail_url'] }}"
                            alt=""
                            class="artikel-digest-card__media-image"
                            loading="lazy"
                            decoding="async"
                          >
                        @else
                          <span class="artikel-digest-card__emoji">{{ $article['emoji'] ?? '📚' }}</span>
                        @endif

                        <span class="artikel-digest-card__issue">{{ $article['issue'] ?? str_pad((string) ($loop->iteration + 1), 2, '0', STR_PAD_LEFT) }}</span>
                      </span>

                      <span class="artikel-digest-card__content">
                        <span class="artikel-digest__meta">
                          @if (! empty($article['category']))
                            <span>{{ $article['category'] }}</span>
                          @endif
                          <span>{{ $article['date'] }}</span>
                          @if (! empty($article['reading_time']))
                            <span>{{ $article['reading_time'] }}</span>
                          @endif
                        </span>

                        <span class="artikel-digest-card__title">
                          {{ $article['title'] }}
                        </span>

                        <span class="artikel-digest-card__description">
                          {{ $article['description'] }}
                        </span>

                        @if (! empty($article['highlight']))
                          <span class="artikel-digest-card__highlight">
                            {{ $article['highlight'] }}
                          </span>
                        @endif
                      </span>
                    </a>
                  </article>
                @empty
                  <p class="artikel-empty">{{ $articlesSection['empty'] ?? __('home.artikel.empty') }}</p>
                @endforelse
              </div>
            </div>

            @if (! empty($articlesSection['cta']['href']) && ! empty($articlesSection['cta']['label']))
              <div class="artikel-section__action">
                <a href="{{ $articlesSection['cta']['href'] }}" class="btn btn--primary">
                  {{ $articlesSection['cta']['label'] }}
                </a>
              </div>
            @endif
          @else
            <p class="artikel-empty">{{ $articlesSection['empty'] ?? __('home.artikel.empty') }}</p>
          @endif
        </div>
      </section>
