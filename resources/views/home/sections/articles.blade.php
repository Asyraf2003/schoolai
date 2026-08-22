@php
  $articleHeading = (string) ($articlesSection['title'] ?? '');
  $articleDescription = (string) ($articlesSection['subtitle'] ?? '');
  $articleItems = collect($articlesSection['items'] ?? [])->take(4)->values();
  $articleCta = is_array($articlesSection['cta'] ?? null)
      ? $articlesSection['cta']
      : [];
@endphp

<section class="article-story" id="artikel" aria-labelledby="article-story-heading" data-article-story>
  <header class="article-story__intro">
    <p class="article-story__eyebrow">Artikel</p>
    <h2 id="article-story-heading">{{ $articleHeading }}</h2>
    @if ($articleDescription !== '')
      <p class="article-story__intro-copy">{{ $articleDescription }}</p>
    @endif
  </header>

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
      <div class="article-story__stack" data-article-stack>
        @foreach ($articleItems as $article)
          <article
            class="article-story__sticky"
            data-article-sticky
            data-article-index="{{ $loop->index }}"
          >
            <div class="article-story__media">
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

            <div class="article-story__copy">
              <span class="article-story__meta">
                {{ $article['category'] ?? ($article['issue'] ?? str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT)) }}
              </span>

              <h3>
                @if (! empty($article['href']))
                  <a href="{{ $article['href'] }}">{{ $article['title'] }}</a>
                @else
                  {{ $article['title'] }}
                @endif
              </h3>

              @if (! empty($article['description']))
                <p>{{ $article['description'] }}</p>
              @endif
            </div>
          </article>
        @endforeach
      </div>
    </div>

    @if (! empty($articleCta['href']) && ! empty($articleCta['label']))
      <div class="article-story__outro">
        <a
          class="article-story__final-cta"
          href="{{ $articleCta['href'] }}"
          data-article-final-cta
        >
          <span>{{ $articleCta['label'] }}</span>
          <span aria-hidden="true">↗</span>
        </a>
      </div>
    @endif
  @else
    <p class="article-story__empty">{{ $articlesSection['empty'] ?? __('home.artikel.empty') }}</p>
  @endif
</section>
