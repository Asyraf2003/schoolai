<section
  class="article-showcase"
  id="artikel"
  data-article-showcase
  aria-labelledby="homepage-article-heading"
>
  <div class="article-showcase__shell">
    <header class="article-showcase__header">
      <h2
        class="article-showcase__title"
        id="homepage-article-heading"
        data-text-role="display"
      >
        @foreach ($articleHeadingLines as $line)
          <span class="article-showcase__title-line">{{ $line }}</span>
        @endforeach
      </h2>

      @if ($articleDescription !== '')
        <p class="article-showcase__description" data-text-role="description">
          {{ $articleDescription }}
        </p>
      @endif
    </header>

    <div class="article-showcase__grid">
      @foreach ($articleItems as $article)
        <article class="article-showcase__card article-showcase__card--{{ $loop->iteration }}">
          <a class="article-showcase__card-link" href="{{ $article['href'] }}">
            <figure class="article-showcase__media">
              <img
                src="{{ $article['media_url'] }}"
                alt="{{ $article['title'] }}"
                width="1200"
                height="800"
                loading="lazy"
                decoding="async"
                fetchpriority="low"
              />
            </figure>

            <div class="article-showcase__body">
              <div class="article-showcase__meta">
                <span>{{ $article['category'] }}</span>
                <span>{{ $article['meta'] }}</span>
              </div>

              <h3>{{ $article['title'] }}</h3>
              <p>{{ $article['description'] }}</p>

              <span class="article-showcase__read">
                <span>{{ $articleReadLabel }}</span>
                <span aria-hidden="true">↗</span>
              </span>
            </div>
          </a>
        </article>
      @endforeach
    </div>

    <a class="article-showcase__all" href="{{ route('artikel') }}">
      <span>{{ $articleCtaLabel }}</span>
      <span aria-hidden="true">↗</span>
    </a>
  </div>
</section>
