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

@include('home.debug.article-ruler')

<section class="article-story" id="artikel" aria-labelledby="article-story-heading" data-article-story>
  <p>tes1</p>
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
      <p>tes2</p>
      <div class="article-story__stage" data-article-stage>
        <p>tes3</p>
        <div class="article-story__horizontal" data-article-horizontal>
          <p>tes4</p>
          <div class="article-story__track" data-article-track>
            <p>tes5</p>
            @foreach ($articleItems as $article)
              <article
                class="article-story__panel{{ $loop->first ? ' article-story__panel--opening' : '' }}"
                data-article-panel
              >
                <p>tes6-{{ $loop->iteration }}</p>
                <div class="article-story__panel-media">
                  <p>tes7-{{ $loop->iteration }}</p>
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

                <div class="article-story__panel-heading" data-article-panel-heading>
                  <p>tes8-{{ $loop->iteration }}</p>
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

                <p>tes9-{{ $loop->iteration }}</p>
                <p class="article-story__panel-description" data-article-panel-description>
                  {{ $article['description'] }}
                </p>
              </article>
            @endforeach

            <section
              class="article-story__closing"
              data-article-closing
              aria-label="{{ $articleClosingHeading }}"
            >
              <p>tes10</p>
              <div class="article-story__roll-window" data-article-roll-window aria-hidden="true">
                <p>tes11</p>
                <div class="article-story__roll-stack" data-article-roll-stack>
                  <p>tes12</p>
                  @foreach ($articleItems as $article)
                    <figure class="article-story__roll-item article-story__roll-item--{{ $loop->iteration }}">
                      <p>tes13-{{ $loop->iteration }}</p>
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
                <p>tes14</p>
                <span>{{ $articleDisplayHeading }}</span>
                <h3>{{ $articleClosingHeading }}</h3>
                @if ($articleDescription !== '')
                  <p>{{ $articleDescription }}</p>
                @endif

                @if (! empty($articleCta['href']) && ! empty($articleCta['label']))
                  <p>tes15</p>
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
