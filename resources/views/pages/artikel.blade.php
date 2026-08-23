@extends('layouts.public', [
  'title' => $page['title'] ?? __('pages.artikel.title'),
  'description' => $page['description'] ?? __('pages.artikel.description'),
])

@push('head')
  @vite('resources/css/pages/article-reader.css')
@endpush

@section('content')
  <section class="public-hero public-hero--artikel article-index-hero" aria-labelledby="artikel-title">
    <div class="container">
      <div class="article-hero-card article-index-hero__card reveal">
        <h1 id="artikel-title" class="public-hero__title">
          {{ $hero['heading'] ?? $page['title'] ?? __('runtime.article.title') }}
        </h1>

        @if(! empty($hero['subtitle']))
          <p class="public-hero__subtitle">{{ $hero['subtitle'] }}</p>
        @endif

        <div class="article-toolbar article-toolbar--search-only" aria-label="{{ $hero['search_label'] ?? $hero['heading'] ?? $page['title'] ?? __('runtime.article.title') }}">
          <label class="public-sr-only" for="articleSearch">{{ $hero['search_placeholder'] ?? __('runtime.article.search_placeholder') }}</label>
          <input
            id="articleSearch"
            type="search"
            class="article-search article-search--wide"
            data-public-search
            placeholder="{{ $hero['search_placeholder'] ?? __('runtime.article.search_placeholder') }}"
          >
        </div>

        @if(! empty($categoryItems))
          <nav class="article-category-filter" aria-label="{{ __('runtime.article.category_filter_aria') }}">
            <a href="{{ route('artikel') }}" @class(['is-active' => empty($activeCategory)])>{{ __('runtime.article.all') }}</a>
            @foreach($categoryItems as $category)
              <a
                href="{{ route('artikel', ['kategori' => $category]) }}"
                @class(['is-active' => isset($activeCategory) && $activeCategory === $category])
              >{{ $category }}</a>
            @endforeach
          </nav>
        @endif
      </div>
    </div>
  </section>

  <section class="public-section article-index-list-section">
    <div class="container">
      @if(! empty($activeCategory))
        <div class="article-category-result">
          <span>{{ __('runtime.article.category') }}</span>
          <strong>{{ $activeCategory }}</strong>
          <span>· {{ __('runtime.article.article_count', ['count' => count($articleItems)]) }}</span>
        </div>
      @endif

      @if(! empty($articleItems))
        <div class="article-index-grid">
          @foreach($articleItems as $article)
            <article class="article-index-card reveal" data-public-article>
              <a
                href="{{ $article['href'] }}"
                class="article-index-card__link"
                @if(! empty($article['external'])) target="_blank" rel="noopener" @endif
                aria-label="{{ $readLabel }}: {{ $article['title'] }}"
              >
                <span class="article-index-card__media">
                  @if(! empty($article['thumbnail_url']))
                    <img src="{{ $article['thumbnail_url'] }}" alt="{{ $article['title'] }}" loading="lazy" decoding="async">
                  @else
                    <span>{{ $article['number'] }}</span>
                  @endif
                </span>

                <span class="article-index-card__overlay">
                  @if(! empty($article['categories']))
                    <span class="article-index-card__categories">
                      @foreach($article['categories'] as $category)
                        <span>{{ $category }}</span>
                      @endforeach
                    </span>
                  @endif

                  <span class="article-index-card__meta">
                    @if(! empty($article['date']))
                      <span class="article-index-card__date">{{ $article['date'] }}</span>
                    @endif

                    @if(! empty($article['author']))
                      <span>{{ $article['author'] }}</span>
                    @endif

                    @if(! empty($article['reading_time']))
                      <span>{{ $article['reading_time'] }}</span>
                    @endif
                  </span>

                  <strong>{{ $article['title'] }}</strong>

                  @if(! empty($article['description']))
                    <span class="article-index-card__description">{{ $article['description'] }}</span>
                  @endif
                </span>
              </a>
            </article>
          @endforeach
        </div>
      @else
        <div class="article-index-empty reveal">
          <h2>{{ ! empty($activeCategory) ? __('runtime.article.empty_category_title') : ($page['empty_title'] ?? __('runtime.article.empty_title')) }}</h2>
          <p>{{ ! empty($activeCategory) ? __('runtime.article.empty_category_description') : ($page['empty_description'] ?? __('runtime.article.empty_description')) }}</p>
        </div>
      @endif
    </div>
  </section>
@endsection
