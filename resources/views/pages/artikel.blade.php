{{-- PUBLIC_ARTIKEL_DUMMY_FINAL --}}
@extends('layouts.public', ['title' => __('pages.artikel.title'), 'description' => __('pages.artikel.description')])

@php($page = __('pages.artikel'))
@php($featured = $page['featured'])

@section('content')
  <section class="public-hero public-hero--artikel" aria-labelledby="artikel-title">
    <div class="container">
      <div class="article-hero-card reveal">
        <a href="{{ route('home') }}" class="link-arrow">{{ __('pages.common.back_home') }}</a>
        <h1 id="artikel-title" class="public-hero__title">{{ $page['hero']['heading'] }}</h1>
        <p class="public-hero__subtitle">{{ $page['hero']['subtitle'] }}</p>

        <div class="article-toolbar" aria-label="{{ $page['hero']['heading'] ?? $page['title'] }}">
          <label class="public-sr-only" for="articleSearch">{{ $page['hero']['search_placeholder'] }}</label>
          <input id="articleSearch" type="search" class="article-search" data-public-search placeholder="{{ $page['hero']['search_placeholder'] }}" />

          <div class="public-filter-row">
            @foreach($page['categories'] as $category)
              <button type="button" class="public-filter-chip {{ $category['slug'] === 'all' ? 'is-active' : '' }}" data-public-filter="{{ $category['slug'] }}">
                {{ $category['label'] }}
              </button>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="public-section">
    <div class="container">
      <article class="featured-article reveal" data-public-article data-category="{{ $featured['category_slug'] }}">
        <div class="featured-article__issue">{{ $featured['issue'] }}</div>
        <div>
          <span class="article-chip">{{ $featured['category'] }}</span>
          <h2>{{ $featured['title'] }}</h2>
          <p>{{ $featured['excerpt'] }}</p>
          <div class="article-meta">
            <span>{{ $featured['date'] }}</span>
            <span>{{ $featured['read_time'] }}</span>
          </div>
          <a href="{{ route('artikel.detail') }}" class="link-arrow">{{ __('pages.common.read_more') }} →</a>
        </div>
      </article>
    </div>
  </section>

  <section class="public-section public-section--soft">
    <div class="container">
      <div class="article-grid">
        @foreach($page['articles'] as $article)
          <article class="article-list-card reveal" data-public-article data-category="{{ $article['category_slug'] }}">
            <div class="article-list-card__top">
              <span class="article-list-card__issue">#{{ $article['issue'] }}</span>
              <span class="article-chip">{{ $article['category'] }}</span>
            </div>
            <h2>{{ $article['title'] }}</h2>
            <p>{{ $article['excerpt'] }}</p>
            <div class="article-meta">
              <span>{{ $article['date'] }}</span>
              <span>{{ $article['read_time'] }}</span>
            </div>
            <a href="{{ $loop->first ? route('artikel.detail') : '/artikel#' . $article['issue'] }}" class="link-arrow">{{ __('pages.common.read_more') }} →</a>
          </article>
        @endforeach
      </div>
    </div>
  </section>
@endsection
