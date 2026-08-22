@php
  $articleHeading = (string) ($articlesSection['title'] ?? '');
  $articleDescription = (string) ($articlesSection['subtitle'] ?? '');
  $articleItems = collect($articlesSection['items'] ?? [])->take(4)->values();
  $articleCta = is_array($articlesSection['cta'] ?? null)
      ? $articlesSection['cta']
      : [];
  $articleDisplayHeading = match (app()->getLocale()) {
      'id' => 'ARTIKEL',
      'en' => 'ARTICLES',
      'ar' => 'المقالات',
      default => 'ARTICLES',
  };
  $articleClosingHeading = match (app()->getLocale()) {
      'id' => 'Lihat artikel selengkapnya',
      'en' => 'Explore more articles',
      'ar' => 'استكشف المزيد من المقالات',
      default => 'Explore more articles',
  };
@endphp

@env('local')
  <style>
    [data-article-layout-mark] {
      position: relative !important;
      box-shadow: inset 0 0 0 1px rgb(255 0 0 / .38);
    }

    [data-article-layout-mark]::after {
      content: attr(data-article-layout-mark);
      position: absolute;
      z-index: 9999;
      inset: 6px auto auto 6px;
      min-width: 2.4rem;
      padding: .28rem .45rem;
      border: 1px solid rgb(255 255 255 / .9);
      border-radius: 4px;
      background: rgb(12 12 12 / .88);
      color: #fff;
      font: 700 11px/1 system-ui, sans-serif;
      letter-spacing: .04em;
      text-align: center;
      pointer-events: none;
    }
  </style>
@endenv

<section
  class="article-story"
  id="artikel"
  aria-labelledby="article-story-heading"
  data-article-story
  data-article-layout-mark="A00"
>
  <h2 class="sr-only" id="article-story-heading">{{ $articleHeading }}</h2>

  @if ($articleItems->isNotEmpty())
    <nav class="article-story__semantic-links" aria-label="{{ $articlesSection['rail_aria_label'] ?? $articleHeading }}">
      <ul>
        @foreach ($articleItems as $article)
          @if (! empty($article['href']))
            <li><a href="{{ $article['href'] }}">{{ $article['title'] }}</a></li>
          @endif
        @endforeach
      </ul>
    </nav>

    <div class="article-story__journey" data-article-journey data-article-layout-mark="A01">
      <div class="article-story__stage" data-article-stage data-article-layout-mark="A02">
        <div class="article-story__horizontal" data-article-horizontal data-article-layout-mark="A03">
          <div class="article-story__track" data-article-track data-article-layout-mark="A04">
            @foreach ($articleItems as $article)
              <article
                class="article-story__panel{{ $loop->first ? ' article-story__panel--opening' : '' }}"
                data-article-panel
                data-article-layout-mark="P{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}"
              >
                <div
                  class="article-story__panel-media"
                  data-article-layout-mark="M{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}"
                >
                  @if (! empty($article['thumbnail_url']))
                    <img
                      src="{{ $article['thumbnail_url'] }}"
                      alt=""
                      width="1600"
                      height="1200"
                      loading="lazy"
                      decoding="async"
                      data-article-panel-image
                    />
                  @else
                    <span aria-hidden="true">{{ $article['emoji'] ?? '📰' }}</span>
                  @endif
                </div>

                <div
                  class="article-story__panel-heading"
                  data-article-panel-heading
                  data-article-layout-mark="H{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}"
                >
                  <span>
                    {{ $articleDisplayHeading }}
                    ·
                    {{ $article['issue'] ?? str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                  </span>
                  <h3>
                    @if (! empty($article['href']))
                      <a href="{{ $article['href'] }}">{{ $article['title'] }}</a>
                    @else
                      {{ $article['title'] }}
                    @endif
                  </h3>
                </div>

                <p
                  class="article-story__panel-description"
                  data-article-panel-description
                  data-article-layout-mark="D{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}"
                >
                  {{ $article['description'] }}
                </p>
              </article>
            @endforeach

            <section
              class="article-story__closing"
              data-article-closing
              data-article-layout-mark="C00"
              aria-label="{{ $articleClosingHeading }}"
            >
              <div
                class="article-story__roll-window"
                data-article-roll-window
                data-article-layout-mark="C01"
                aria-hidden="true"
              >
                <div
                  class="article-story__roll-stack"
                  data-article-roll-stack
                  data-article-layout-mark="C02"
                >
                  @foreach ($articleItems as $article)
                    <figure
                      class="article-story__roll-item article-story__roll-item--{{ $loop->iteration }}"
                      data-article-layout-mark="R{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}"
                    >
                      @if (! empty($article['thumbnail_url']))
                        <img
                          src="{{ $article['thumbnail_url'] }}"
                          alt=""
                          width="900"
                          height="900"
                          loading="lazy"
                          decoding="async"
                        />
                      @else
                        <span aria-hidden="true">{{ $article['emoji'] ?? '📰' }}</span>
                      @endif
                    </figure>
                  @endforeach
                </div>
              </div>

              <div class="article-story__closing-copy" data-article-layout-mark="C03">
                <span>{{ $articleDisplayHeading }}</span>
                <h3>{{ $articleClosingHeading }}</h3>
                @if ($articleDescription !== '')
                  <p>{{ $articleDescription }}</p>
                @endif

                @if (! empty($articleCta['href']) && ! empty($articleCta['label']))
                  <a
                    class="article-story__final-cta"
                    href="{{ $articleCta['href'] }}"
                    data-article-final-cta
                    data-article-layout-mark="C04"
                  >
                    <span>{{ $articleCta['label'] }}</span>
                    <span aria-hidden="true">↗</span>
                  </a>
                @endif
              </div>
            </section>
          </div>
        </div>
      </div>
    </div>
  @else
    <p class="article-story__empty">{{ $articlesSection['empty'] ?? __('home.artikel.empty') }}</p>
  @endif
</section>
