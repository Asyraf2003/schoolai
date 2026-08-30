<section
  class="article-showcase"
  id="artikel"
  data-article-showcase
  aria-labelledby="homepage-article-heading"
>
  <header
    class="article-showcase__header home-section-display__header"
    data-editorial-heading
  >
    <h2
      class="article-showcase__title home-section-display__title"
      id="homepage-article-heading"
      data-text-role="display"
    >
      @foreach ($articleHeadingLines as $line)
        <span class="article-showcase__title-line article-showcase__title-line--{{ $loop->first ? 'top' : 'bottom' }} home-section-display__line">
          <span class="article-showcase__title-text">{{ $line }}</span>
        </span>
      @endforeach
    </h2>
  </header>

  <div class="article-showcase__shell">
    @if (empty($articleItems))
      <div class="article-showcase__empty">
        <div class="article-showcase__empty-copy">
          <span class="article-showcase__empty-kicker">{{ $articleEmptyKicker }}</span>
          <h3>{{ $articleEmptyTitle }}</h3>
          <p>{{ $articleEmptyDescription }}</p>
        </div>
      </div>
    @else
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
              </div>
            </a>
          </article>
        @endforeach
      </div>

      <a class="article-showcase__all" href="{{ route('artikel') }}">
        <span class="article-showcase__all-label">{{ $articleCtaLabel }}</span>
        <span class="article-showcase__all-icon" aria-hidden="true">↗</span>
      </a>
    @endif
  </div>
</section>
