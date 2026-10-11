<section class="stories" id="artikel" data-stories aria-labelledby="stories-title">
    <header class="stories__header">
        <h2 class="stories__heading" id="stories-title" data-stories-heading aria-label="{{ __('stories.title') }}">
            @foreach (__('stories.lines') as $line)
                <span class="stories__heading-clip"><span data-stories-heading-line aria-hidden="true">{{ $line }}</span></span>
            @endforeach
        </h2>
    </header>
    <article class="stories__feature" data-stories-feature data-stories-demo aria-labelledby="stories-feature-title">
        <div class="stories__feature-media" aria-hidden="true">
            <img class="stories__feature-poster" data-stories-poster
                src="{{ config('media.static.school_life.labit') }}"
                alt="" loading="lazy" decoding="async" width="1600" height="900">
            <video class="stories__feature-video" data-stories-video
                data-src="{{ rtrim(config('media.public_url'), '/') }}/gallery/media/it-v1.mp4"
                poster="{{ config('media.static.school_life.labit') }}"
                preload="none" autoplay muted loop playsinline aria-hidden="true"></video>
        </div>
        <div class="stories__feature-copy">
            <span class="stories__feature-index" aria-hidden="true">01</span>
            <h3 id="stories-feature-title">{{ __('stories.feature.title') }}</h3>
            <p>{{ __('stories.feature.description') }}</p>
        </div>
    </article>
</section>
