<section class="stories" id="artikel" data-stories aria-labelledby="stories-title">
    <header class="stories__header">
        <h2 class="stories__heading" id="stories-title" data-stories-heading aria-label="{{ __('stories.title') }}">
            @foreach (__('stories.lines') as $line)
                <span class="stories__heading-clip"><span data-stories-heading-line aria-hidden="true">{{ $line }}</span></span>
            @endforeach
        </h2>
    </header>
    @php
        $demoMedia = [
            ['poster' => 'labit', 'video' => 'it'],
            ['poster' => 'mushalla', 'video' => 'musola'],
            ['poster' => 'renang', 'video' => 'renang'],
            ['poster' => 'taekwondo', 'video' => 'tekwondo'],
        ];
    @endphp
    <div class="stories__sequence" data-stories-sequence>
        <div class="stories__viewport" data-stories-viewport>
            @foreach ($demoMedia as $media)
                <article class="stories__feature" data-stories-feature data-stories-demo
                         data-stories-number="{{ $loop->iteration }}"
                         aria-labelledby="stories-feature-title-{{ $loop->iteration }}">
                    <div class="stories__feature-media" aria-hidden="true">
                        <img class="stories__feature-poster" data-stories-poster
                             src="{{ config('media.static.school_life.'.$media['poster']) }}"
                             alt="" loading="lazy" decoding="async" width="1600" height="900">
                        <video class="stories__feature-video" data-stories-video
                               data-src="{{ rtrim(config('media.public_url'), '/') }}/gallery/media/{{ $media['video'] }}-v1.mp4"
                               poster="{{ config('media.static.school_life.'.$media['poster']) }}"
                               preload="none" autoplay muted loop playsinline aria-hidden="true"></video>
                    </div>
                    @php($feature = __('stories.features.'.$loop->index))
                    <div class="stories__feature-copy" data-stories-copy>
                        <span class="stories__feature-index" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3 id="stories-feature-title-{{ $loop->iteration }}" aria-label="{{ $feature['title'] }}">@foreach (explode(' ', $feature['title']) as $word)<span data-stories-word aria-hidden="true">{{ $word }}</span>{{ $loop->last ? '' : ' ' }}@endforeach</h3>
                        <p aria-label="{{ $feature['description'] }}">@foreach (explode(' ', $feature['description']) as $word)<span data-stories-word aria-hidden="true">{{ $word }}</span>{{ $loop->last ? '' : ' ' }}@endforeach</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
