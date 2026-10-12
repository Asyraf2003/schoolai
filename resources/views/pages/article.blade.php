@extends('portal.shell')
@section('title', $article->titleForLocale(app()->getLocale()) ?: __('pages.common.nav.artikel'))
@section('content')
    @include('pages.partials.navigation')
    <article class="article-detail">
        @if($article->titleForLocale(app()->getLocale()))<h1>{{ $article->titleForLocale(app()->getLocale()) }}</h1>@endif
        @if($article->descriptionForLocale(app()->getLocale()))<p>{{ $article->descriptionForLocale(app()->getLocale()) }}</p>@endif
        @if($article->published_date)<time datetime="{{ $article->published_date->toDateString() }}">{{ $article->published_date->format('d/m/Y') }}</time>@endif
        @if($article->cover_url && \App\Support\PublicUrl::isSafe($article->cover_url))
            @if(\App\Support\HeroVideoUrl::isDirectVideo($article->cover_url))
                <video controls playsinline preload="none" src="{{ $article->cover_url }}" @if($article->coverPosterUrl()) poster="{{ $article->coverPosterUrl() }}" @endif aria-label="{{ $article->titleForLocale(app()->getLocale()) ?: __('pages.common.media_video') }}"></video>
            @else<img src="{{ $article->cover_url }}" alt="{{ $article->titleForLocale(app()->getLocale()) }}" width="1200" height="675">@endif
        @endif
        <div class="article-content" lang="{{ $article->contentLocale(app()->getLocale()) }}" dir="auto">{!! $article->contentForLocale(app()->getLocale()) !!}</div>
    </article>
    <div class="actions"><a class="action secondary" href="{{ route('artikel') }}">{{ __('pages.common.nav.artikel') }}</a></div>
@endsection
