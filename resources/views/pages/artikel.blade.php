{{-- PUBLIC_ARTIKEL_DB_FINAL --}}
@extends('layouts.public', [
  'title' => $page['title'] ?? __('pages.artikel.title'),
  'description' => $page['description'] ?? __('pages.artikel.description'),
])

@php
  $hero = $page['hero'] ?? [];
  $featured = $articles[0] ?? null;
  $articleList = array_slice($articles, 1);
  $readLabel = __('pages.common.read_more');
@endphp

@section('content')
  <section class="public-hero public-hero--artikel article-index-hero" aria-labelledby="artikel-title">
    <div class="container">
      <div class="article-hero-card article-index-hero__card reveal">
        <a href="{{ route('home') }}" class="link-arrow">{{ __('pages.common.back_home') }}</a>

        <h1 id="artikel-title" class="public-hero__title">
          {{ $hero['heading'] ?? $page['title'] ?? 'Artikel' }}
        </h1>

        @if(! empty($hero['subtitle']))
          <p class="public-hero__subtitle">{{ $hero['subtitle'] }}</p>
        @endif

        <div class="article-toolbar article-toolbar--search-only" aria-label="{{ $hero['search_label'] ?? $hero['heading'] ?? $page['title'] ?? 'Artikel' }}">
          <label class="public-sr-only" for="articleSearch">{{ $hero['search_placeholder'] ?? 'Cari artikel...' }}</label>
          <input
            id="articleSearch"
            type="search"
            class="article-search article-search--wide"
            data-public-search
            placeholder="{{ $hero['search_placeholder'] ?? 'Cari artikel...' }}"
          >
        </div>
      </div>
    </div>
  </section>

  @if($featured)
    <section class="public-section article-index-section">
      <div class="container">
        <article class="article-index-featured reveal" data-public-article>
          <a href="{{ $featured['href'] }}" class="article-index-featured__media" target="_blank" rel="noopener">
            @if(! empty($featured['thumbnail_url']))
              <img src="{{ $featured['thumbnail_url'] }}" alt="{{ $featured['title'] }}" loading="lazy" decoding="async">
            @else
              <span>{{ $featured['number'] }}</span>
            @endif
          </a>

          <div class="article-index-featured__body">
            <div class="article-meta article-index-meta">
              @if(! empty($featured['date']))
                <span>{{ $featured['date'] }}</span>
              @endif
              <span>{{ $featured['author'] }}</span>
            </div>

            <h2>
              <a href="{{ $featured['href'] }}" target="_blank" rel="noopener">{{ $featured['title'] }}</a>
            </h2>

            @if(! empty($featured['description']))
              <p>{{ $featured['description'] }}</p>
            @endif

            <a href="{{ $featured['href'] }}" class="link-arrow" target="_blank" rel="noopener">
              {{ $readLabel }} →
            </a>
          </div>
        </article>
      </div>
    </section>

    <section class="public-section public-section--soft article-index-list-section">
      <div class="container">
        @if(! empty($articleList))
          <div class="article-index-grid">
            @foreach($articleList as $article)
              <article class="article-index-card reveal" data-public-article>
                <a href="{{ $article['href'] }}" class="article-index-card__link" target="_blank" rel="noopener" aria-label="{{ $readLabel }}: {{ $article['title'] }}">
                  <span class="article-index-card__media">
                    @if(! empty($article['thumbnail_url']))
                      <img src="{{ $article['thumbnail_url'] }}" alt="{{ $article['title'] }}" loading="lazy" decoding="async">
                    @else
                      <span>{{ $article['number'] }}</span>
                    @endif
                  </span>

                  <span class="article-index-card__overlay">
                    @if(! empty($article['date']))
                      <span class="article-index-card__date">{{ $article['date'] }}</span>
                    @endif
                    <strong>{{ $article['title'] }}</strong>
                  </span>
                </a>
              </article>
            @endforeach
          </div>
        @endif
      </div>
    </section>
  @else
    <section class="public-section">
      <div class="container">
        <div class="article-index-empty reveal">
          <h2>{{ $page['empty_title'] ?? 'Belum ada artikel.' }}</h2>
          <p>{{ $page['empty_description'] ?? 'Artikel sekolah akan tampil di sini setelah admin menambahkannya.' }}</p>
        </div>
      </div>
    </section>
  @endif
@endsection
