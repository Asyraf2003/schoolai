@php
  $articleHeading = (string) ($articlesSection['title'] ?? '');
  $articleDescription = (string) ($articlesSection['subtitle'] ?? '');
  $articleItems = collect($articlesSection['items'] ?? [])->take(4)->values();
  $openingArticle = $articleItems->first();
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
      'id' => 'Mau lihat artikel selengkapnya?',
      'en' => 'Want to read more stories?',
      'ar' => 'هل ترغب في قراءة المزيد من المقالات؟',
      default => 'Want to read more stories?',
  };
@endphp

<section class="article-story" id="artikel" aria-labelledby="article-story-heading" data-article-story>
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
  @endif

  @if ($openingArticle)
    <div class="article-story__journey" data-article-journey>
      <div class="article-story__stage" data-article-stage>
        <article class="article-story__opening" data-article-opening>
          <div class="article-story__feature-frame">
            <div class="article-story__feature-media">
              @if (! empty($openingArticle['thumbnail_url']))
                <img
                  src="{{ $openingArticle['thumbnail_url'] }}"
                  alt=""
                  width="1600"
                  height="900"
                  loading="lazy"
                  decoding="async"
                />
              @else
                <span aria-hidden="true">{{ $openingArticle['emoji'] ?? '📰' }}</span>
              @endif
            </div>
            <p class="article-story__opening-title" aria-hidden="true">{{ $articleDisplayHeading }}</p>
          </div>
        </article>

        <div class="article-story__horizontal" data-article-horizontal aria-hidden="true">
          <div class="article-story__track" data-article-track>
            @foreach ($articleItems as $article)
              <article class="article-story__panel">
                <div class="article-story__panel-media">
                  @if (! empty($article['thumbnail_url']))
                    <img
                      src="{{ $article['thumbnail_url'] }}"
                      alt=""
                      width="1600"
                      height="1200"
                      loading="lazy"
                      decoding="async"
                    />
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

        <section class="article-story__closing" data-article-closing aria-label="{{ $articleClosingHeading }}">
          <div class="article-story__roll-window" data-article-roll-window aria-hidden="true">
            <div class="article-story__roll-stack" data-article-roll-stack>
              @foreach ($articleItems as $article)
                <figure class="article-story__roll-item article-story__roll-item--{{ $loop->iteration }}">
                  @if (! empty($article['thumbnail_url']))
                    <img
                      src="{{ $article['thumbnail_url'] }}"
                      alt=""
                      width="1000"
                      height="760"
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

          <div class="article-story__closing-copy">
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
              >
                <span>{{ $articleCta['label'] }}</span>
                <span aria-hidden="true">↗</span>
              </a>
            @endif
          </div>
        </section>
      </div>
    </div>
  @else
    <p class="article-story__empty">{{ $articlesSection['empty'] ?? __('home.artikel.empty') }}</p>
  @endif
</section>
