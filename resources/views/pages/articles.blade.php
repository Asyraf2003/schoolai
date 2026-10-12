@extends('portal.shell')
@section('title', __('pages.common.nav.artikel'))
@section('content')
    @include('pages.partials.navigation')
    <h1>{{ __('pages.common.nav.artikel') }}</h1>
    <div class="public-grid" data-public-articles>
    @forelse($articles as $article)
        <article class="public-card">
            @if($article->coverPosterUrl())
                <a href="{{ $article->linkForLocale(app()->getLocale()) }}" aria-label="{{ $article->titleForLocale(app()->getLocale()) ?: __('pages.common.read_more') }}"><img src="{{ $article->coverPosterUrl() }}" alt="" width="1200" height="675" loading="lazy" decoding="async"></a>
            @endif
            <h2><a href="{{ $article->linkForLocale(app()->getLocale()) }}">{{ $article->titleForLocale(app()->getLocale()) ?: __('pages.common.nav.artikel') }}</a></h2>
            @if($article->published_date)<time datetime="{{ $article->published_date->toDateString() }}">{{ $article->published_date->format('d/m/Y') }}</time>@endif
            <p>{{ $article->descriptionForLocale(app()->getLocale()) }}</p>
        </article>
    @empty
        <p>{{ __('public_content.articles_empty') }}</p>
    @endforelse
    </div>
    @include('pages.partials.pagination', ['paginator' => $articles])
@endsection
