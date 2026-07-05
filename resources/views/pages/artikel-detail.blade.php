{{-- PUBLIC_ARTICLE_DETAIL_DUMMY_FINAL --}}
@extends('layouts.public', ['title' => __('pages.artikel_detail.title'), 'description' => __('pages.artikel_detail.description')])

@php($article = __('pages.artikel_detail'))

@section('content')
  <article class="article-detail-page" aria-labelledby="article-detail-title">
    <header class="article-detail-hero">
      <div class="container article-detail-hero__grid">
        <div class="article-detail-hero__copy reveal">
          <a href="{{ route('artikel') }}" class="link-arrow">{{ $article['back_to_articles'] }}</a>

          <div class="article-detail-meta">
            <span>{{ $article['issue'] }}</span>
            <span>{{ $article['category'] }}</span>
            <span>{{ $article['date'] }}</span>
            <span>{{ $article['read_time'] }}</span>
          </div>

          <h1 id="article-detail-title">{{ $article['heading'] }}</h1>
          <p>{{ $article['lead'] }}</p>
          <small>{{ $article['hero_note'] }}</small>
        </div>

        <aside class="article-detail-card reveal" aria-label="{{ $article['toc_title'] }}">
          <span class="article-detail-card__emoji" aria-hidden="true">🌙</span>
          <h2>{{ $article['toc_title'] }}</h2>
          <ol>
            @foreach($article['toc'] as $item)
              <li>{{ $item }}</li>
            @endforeach
          </ol>
        </aside>
      </div>
    </header>

    <section class="article-detail-content">
      <div class="container article-detail-content__grid">
        <div class="article-detail-body">
          @foreach($article['sections'] as $section)
            <section class="article-detail-section reveal">
              <h2>{{ $section['heading'] }}</h2>

              @foreach($section['body'] as $paragraph)
                <p>{{ $paragraph }}</p>
              @endforeach
            </section>
          @endforeach

          <blockquote class="article-detail-quote reveal">
            <p>“{{ $article['quote']['text'] }}”</p>
            <cite>{{ $article['quote']['author'] }}</cite>
          </blockquote>
        </div>

        <aside class="article-detail-related reveal" aria-label="{{ $article['related_title'] }}">
          <h2>{{ $article['related_title'] }}</h2>

          @foreach($article['related'] as $related)
            <a href="{{ route('artikel.detail') }}" class="article-related-card">
              <strong>{{ $related['title'] }}</strong>
              <span>{{ $related['meta'] }}</span>
            </a>
          @endforeach
        </aside>
      </div>
    </section>
  </article>
@endsection
