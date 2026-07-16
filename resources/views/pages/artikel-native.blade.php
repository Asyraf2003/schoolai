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
            <li>{{ $tag }}</li>
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
  </article>
@endsection
