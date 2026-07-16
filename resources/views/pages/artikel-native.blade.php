@extends('layouts.public', [
  'title' => $articleTitle,
  'description' => $articleDescription,
])

@push('head')
  @vite('resources/css/pages/article-reader.css')
@endpush

@section('content')
  <article class="native-article" aria-labelledby="native-article-title">
    <header class="native-article__header">
      <a href="{{ route('artikel') }}" class="native-article__back">← Semua artikel</a>
      <h1 id="native-article-title">{{ $articleTitle }}</h1>

      @if($articleSubtitle !== '')
        <p class="native-article__subtitle">{{ $articleSubtitle }}</p>
      @endif

      <div class="native-article__byline">
        <span class="native-article__avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($article->authorForDisplay(), 0, 1)) }}</span>
        <span>
          <strong>{{ $article->authorForDisplay() }}</strong>
          <small>
            {{ $article->published_at?->translatedFormat('j F Y') }}
            · {{ $readingMinutes }} menit baca
          </small>
        </span>
      </div>

      @if(! empty($article->tags))
        <ul class="native-article__tags" aria-label="Tag artikel">
          @foreach($article->tags as $tag)
            <li><a href="{{ route('artikel', ['kategori' => $tag]) }}">{{ $tag }}</a></li>
          @endforeach
        </ul>
      @endif
    </header>

    <div class="native-article__body">
      {!! $articleContent !!}
    </div>

    <footer class="native-article__footer">
      <span class="native-article__avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($article->authorForDisplay(), 0, 1)) }}</span>
      <div>
        <strong>Ditulis oleh {{ $article->authorForDisplay() }}</strong>
        <p>Ikuti kabar dan cerita terbaru dari Al Mustaqbal School.</p>
      </div>
    </footer>

    @if(! empty($relatedArticles))
      <aside class="native-article__related" aria-labelledby="related-articles-title">
        <span class="native-article__related-eyebrow">Lanjut membaca</span>
        <h2 id="related-articles-title">Mungkin Anda juga suka</h2>

        <div class="native-article__related-grid">
          @foreach($relatedArticles as $related)
            <article class="native-related-card">
              <a href="{{ $related['href'] }}">
                <span class="native-related-card__image">
                  <img src="{{ $related['thumbnail_url'] }}" alt="" loading="lazy" decoding="async">
                </span>
                <span class="native-related-card__body">
                  @if(! empty($related['categories']))
                    <small>{{ implode(' · ', array_slice($related['categories'], 0, 2)) }}</small>
                  @endif
                  <strong>{{ $related['title'] }}</strong>
                  @if(! empty($related['description']))
                    <span>{{ $related['description'] }}</span>
                  @endif
                  <em>{{ $related['reading_minutes'] }} menit baca</em>
                </span>
              </a>
            </article>
          @endforeach
        </div>
      </aside>
    @endif
  </article>
@endsection
