@include('home.debug.article-ruler')

<section
  class="article-story"
  id="artikel"
  aria-labelledby="article-story-heading"
  data-article-story
  style="--article-count: {{ max(1, $articleItems->count()) }};"
>
  <p class="article-debug-mark">tes1</p>
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

    <div class="article-story__journey" data-article-journey>
      <p class="article-debug-mark">tes2</p>
      <div class="article-story__stage" data-article-stage>
        <p class="article-debug-mark">tes3</p>
        <div class="article-story__horizontal" data-article-horizontal>
          <p class="article-debug-mark">tes4</p>
          <div class="article-story__track" data-article-track>
            <p class="article-debug-mark">tes5</p>

            @if ($openingArticle)
              <article class="article-story__opening" data-article-opening>
                <p class="article-debug-mark">tes6-1</p>

                @if ($articleOpeningHref !== '')
                  <a
                    class="article-story__opening-link"
                    href="{{ $articleOpeningHref }}"
                    aria-label="{{ $articleOpeningLabel !== '' ? $articleOpeningLabel : $articleDisplayHeading }}"
                    data-article-opening-link
                  >
                @else
                  <div class="article-story__opening-link">
                @endif

                  <div class="article-story__opening-media">
                    <p class="article-debug-mark">tes7-1</p>
                    @if (! empty($openingArticle['thumbnail_url']))
                      <img
                        src="{{ $openingArticle['thumbnail_url'] }}"
                        alt=""
                        width="1600"
                        height="1200"
                        loading="lazy"
                        decoding="async"
                      />
                    @else
                      <span aria-hidden="true">{{ $openingArticle['emoji'] ?? '📰' }}</span>
                    @endif
                  </div>

                  <div class="article-story__opening-heading">
                    <p class="article-debug-mark">tes8-1</p>
                    <h3>{{ $articleDisplayHeading }}</h3>
                  </div>

                  <div class="article-story__opening-description">
                    <p class="article-debug-mark">tes9-1</p>
                    @if ($articleDescription !== '')
                      <p>{{ $articleDescription }}</p>
                    @endif
                  </div>

                @if ($articleOpeningHref !== '')
                  </a>
                @else
                  </div>
                @endif
              </article>
            @endif

            @foreach ($articleItems as $article)
              <article class="article-story__main-item" data-article-main-item>
                <p class="article-debug-mark">tes16-{{ $loop->iteration }}</p>
                <div class="article-story__main-media">
                  <p class="article-debug-mark">tes17-{{ $loop->iteration }}</p>
                  @if (! empty($article['thumbnail_url']))
                    <img
                      src="{{ $article['thumbnail_url'] }}"
                      alt=""
                      width="1600"
                      height="1600"
                      loading="lazy"
                      decoding="async"
                      data-article-main-image
                    />
                  @else
                    <span aria-hidden="true">{{ $article['emoji'] ?? '📰' }}</span>
                  @endif
                </div>

                <div class="article-story__main-copy" data-article-main-copy>
                  <p class="article-debug-mark">tes18-{{ $loop->iteration }}</p>
                  <span>
                    {{ $articleDisplayHeading }}
                    ·
                    {{ $article['display_issue'] }}
                  </span>
                  <h3>
                    @if (! empty($article['href']))
                      <a href="{{ $article['href'] }}">{{ $article['title'] }}</a>
                    @else
                      {{ $article['title'] }}
                    @endif
                  </h3>
                  <p class="article-story__main-description">{{ $article['description'] }}</p>
                </div>
              </article>
            @endforeach

            <section
              class="article-story__closing"
              data-article-closing
              aria-label="{{ $articleClosingHeading }}"
            >
              <p class="article-debug-mark">tes10</p>
              <div class="article-story__roll-window" data-article-roll-window aria-hidden="true">
                <p class="article-debug-mark">tes11</p>
                <div class="article-story__roll-stack" data-article-roll-stack>
                  <p class="article-debug-mark">tes12</p>
                  @foreach ($articleItems as $article)
                    <figure class="article-story__roll-item article-story__roll-item--{{ $loop->iteration }}">
                      <p class="article-debug-mark">tes13-{{ $loop->iteration }}</p>
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

              <div class="article-story__closing-copy">
                <p class="article-debug-mark">tes14</p>
                <span>{{ $articleDisplayHeading }}</span>
                <h3>{{ $articleClosingHeading }}</h3>
                @if ($articleDescription !== '')
                  <p>{{ $articleDescription }}</p>
                @endif

                @if (! empty($articleCta['href']) && ! empty($articleCta['label']))
                  <p class="article-debug-mark">tes15</p>
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
      </div>
    </div>
  @else
    <p class="article-story__empty">{{ $articlesSection['empty'] ?? __('home.artikel.empty') }}</p>
  @endif
</section>
