@php
  $articleHeading = (string) ($articlesSection['title'] ?? '');
  $articleDescription = (string) ($articlesSection['subtitle'] ?? '');
  $articleItems = collect($articlesSection['items'] ?? [])->take(4)->values();
  $featuredArticle = $articleItems->first();
  $horizontalArticles = $articleItems->count() > 1
      ? $articleItems->slice(1)->values()
      : $articleItems;
  $finalArticle = $articleItems->last();
  $articleCta = is_array($articlesSection['cta'] ?? null)
      ? $articlesSection['cta']
      : [];
@endphp

<section class="article-story" id="artikel" aria-labelledby="article-story-heading" data-article-story>
  <h2 class="sr-only" id="article-story-heading">{{ $articleHeading }}</h2>
  <div class="sr-only">
    <p class="welcome-editorial-heading__description">
      <span class="sr-only">{{ $articleDescription }}</span>
      <span class="welcome-editorial-heading__description-clip" aria-hidden="true">
        <span class="welcome-editorial-heading__description-line">{{ $articleDescription }}</span>
      </span>
    </p>
  </div>

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
  @endif

  @if ($featuredArticle)
    <div class="article-story__journey" data-article-journey>
      <div class="article-story__stage" data-article-stage>
        <article class="article-story__opening" data-article-opening>
          <div class="article-story__feature-frame">
            <div class="article-story__feature-media">
              @if (! empty($featuredArticle['thumbnail_url']))
                <img src="{{ $featuredArticle['thumbnail_url'] }}" alt=""
                  width="1600" height="900" loading="lazy" decoding="async" />
              @else
                <span aria-hidden="true">{{ $featuredArticle['emoji'] ?? '📰' }}</span>
              @endif
            </div>

            <div class="article-story__feature-title">
              <span>{{ $featuredArticle['category'] ?? '' }}</span>
              <h3>
                @if (! empty($featuredArticle['href']))
                  <a href="{{ $featuredArticle['href'] }}">{{ $featuredArticle['title'] }}</a>
                @else
                  {{ $featuredArticle['title'] }}
                @endif
              </h3>
            </div>

            <p class="article-story__feature-description">{{ $featuredArticle['description'] }}</p>
          </div>
        </article>

        <div class="article-story__horizontal" data-article-horizontal aria-hidden="true">
          <div class="article-story__track" data-article-track>
            @foreach ($horizontalArticles as $article)
              <article class="article-story__panel">
                <div class="article-story__panel-media">
                  @if (! empty($article['thumbnail_url']))
                    <img src="{{ $article['thumbnail_url'] }}" alt="" width="1600" height="1200" loading="lazy" decoding="async" />
                  @else
                    <span aria-hidden="true">{{ $article['emoji'] ?? '📰' }}</span>
                  @endif
                </div>
                <div class="article-story__panel-copy">
                  <span>{{ $article['issue'] ?? str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                  <h3>{{ $article['title'] }}</h3>
                  <p>{{ $article['description'] }}</p>
                </div>
              </article>
            @endforeach
          </div>
        </div>

        <div class="article-story__roll" data-article-roll>
          <div class="article-story__roll-window" data-article-roll-window>
            <div class="article-story__roll-stack" data-article-roll-stack>
              @foreach ($articleItems as $article)
                <div class="article-story__roll-item">
                  @if (! empty($article['thumbnail_url']))
                    <img src="{{ $article['thumbnail_url'] }}" alt="" width="1200" height="900" loading="lazy" decoding="async" />
                  @else
                    <span aria-hidden="true">{{ $article['emoji'] ?? '📰' }}</span>
                  @endif
                </div>
              @endforeach
            </div>
          </div>

          @if (! empty($articleCta['href']) && ! empty($articleCta['label']))
            <a
              class="article-story__final-media article-story__final-link"
              href="{{ $articleCta['href'] }}"
              data-article-final-cta
              aria-label="{{ $articleCta['label'] }}"
            >
              @if (! empty($finalArticle['thumbnail_url']))
                <img src="{{ $finalArticle['thumbnail_url'] }}" alt="" width="1200" height="1600" loading="lazy" decoding="async" />
              @else
                <span aria-hidden="true">{{ $finalArticle['emoji'] ?? '📰' }}</span>
              @endif
              <span class="article-story__final-label">{{ $articleCta['label'] }}</span>
            </a>
          @endif
        </div>
      </div>
    </div>
  @else
    <p class="article-story__empty">{{ $articlesSection['empty'] ?? __('home.artikel.empty') }}</p>
  @endif
</section>
