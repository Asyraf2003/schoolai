@extends('portal.shell')
@section('title', __('pages.common.nav.artikel'))
@section('content')
    <h1>{{ __('pages.common.nav.artikel') }}</h1>
    @foreach($articles as $article)
        <article class="article-card">
            <h2><a href="{{ $article->linkForLocale(app()->getLocale()) }}">{{ $article->titleForLocale(app()->getLocale()) ?: __('pages.common.nav.artikel') }}</a></h2>
            @if($article->published_date)<time datetime="{{ $article->published_date->toDateString() }}">{{ $article->published_date->format('d/m/Y') }}</time>@endif
            <p>{{ $article->descriptionForLocale(app()->getLocale()) }}</p>
        </article>
    @endforeach
    @if($articles->hasPages())<nav aria-label="{{ __('pages.common.nav.artikel') }}">
        @if($articles->previousPageUrl())<a href="{{ $articles->previousPageUrl() }}" rel="prev">←</a>@endif
        <span>{{ $articles->currentPage() }} / {{ $articles->lastPage() }}</span>
        @if($articles->nextPageUrl())<a href="{{ $articles->nextPageUrl() }}" rel="next">→</a>@endif
    </nav>@endif
@endsection
