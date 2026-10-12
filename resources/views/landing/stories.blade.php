<section class="stories" id="artikel" data-stories aria-labelledby="stories-title">
    <header class="stories__header">
        <h2 class="stories__heading" id="stories-title" data-stories-heading aria-label="{{ __('stories.title') }}">
            @foreach (__('stories.lines') as $line)
                <span class="stories__heading-clip"><span data-stories-heading-line aria-hidden="true">{{ $line }}</span></span>
            @endforeach
        </h2>
    </header>
    <div class="stories__sequence" data-stories-sequence>
        <div class="stories__viewport" data-stories-viewport>
            @foreach ($articles as $article)
                @php
                    $title = $article->titleForLocale(app()->getLocale()) ?: __('pages.common.nav.artikel');
                    $description = $article->descriptionForLocale(app()->getLocale());
                    $cover = $article->cover_url && \App\Support\PublicUrl::isSafe($article->cover_url) ? $article->cover_url : null;
                    $isVideo = $cover && \App\Support\HeroVideoUrl::isDirectVideo($cover);
                @endphp
                <article class="stories__feature" data-stories-feature
                         data-stories-number="{{ $loop->iteration }}"
                         aria-labelledby="stories-feature-title-{{ $loop->iteration }}">
                    <div class="stories__feature-media" aria-hidden="true">
                        @if($cover)
                            <img class="stories__feature-poster" data-stories-poster
                                 src="{{ $isVideo ? config('media.static.hero_school') : $cover }}"
                                 alt="" loading="lazy" decoding="async" width="1600" height="900">
                            @if($isVideo)
                                <video class="stories__feature-video" data-stories-video data-src="{{ $cover }}"
                                       poster="{{ config('media.static.hero_school') }}"
                                       preload="none" muted loop playsinline aria-hidden="true"></video>
                            @endif
                        @endif
                    </div>
                    <div class="stories__feature-copy" data-stories-copy>
                        <span class="stories__feature-index" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3 id="stories-feature-title-{{ $loop->iteration }}">
                            <a href="{{ $article->linkForLocale(app()->getLocale()) }}" aria-label="{{ $title }}">@foreach (explode(' ', $title) as $word)<span data-stories-word aria-hidden="true">{{ $word }}</span>{{ $loop->last ? '' : ' ' }}@endforeach</a>
                        </h3>
                        @if($description)<p><span class="sr-only">{{ $description }}</span>@foreach (explode(' ', $description) as $word)<span data-stories-word aria-hidden="true">{{ $word }}</span>{{ $loop->last ? '' : ' ' }}@endforeach</p>@endif
                        @if($article->published_date)<time datetime="{{ $article->published_date->toDateString() }}">{{ $article->published_date->format('d/m/Y') }}</time>@endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
