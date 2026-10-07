<section class="about" id="visi-misi" data-about data-background="{{ $about['background'] }}" aria-labelledby="about-title">
    <h2 class="sr-only" id="about-title">{{ $about['section_label'] }}</h2>
    <div class="about__layout">
        <div class="about__stories">
            @foreach ($about['stories'] as $key => $story)
                <article class="about__story" data-about-story="{{ $key }}" aria-labelledby="about-{{ $key }}-title"
                    data-poster="{{ $story['media']['poster'] }}" data-preview="{{ $story['media']['preview'] }}"
                    @isset($story['media']['full']) data-full="{{ $story['media']['full'] }}" data-open-label="{{ $story['open_label'] }}" @endisset>
                    <div class="about__inline about__frame" data-about-inline>
                        <img src="{{ $story['media']['poster'] }}" alt="" width="1280" height="720" loading="lazy" decoding="async" fetchpriority="low">
                        <video data-about-preview data-preview="{{ $story['media']['preview'] }}" muted loop playsinline preload="none" width="1280" height="720" aria-hidden="true"></video>
                        @isset($story['media']['full'])
                            <button class="about__open" type="button" data-about-open aria-label="{{ $story['open_label'] }}" aria-haspopup="dialog" aria-controls="about-dialog" hidden>
                                <span class="about__play" aria-hidden="true">▶</span>
                            </button>
                        @endisset
                    </div>
                    <div class="about__copy">
                        <p class="about__label">{{ $story['label'] }}</p>
                        <h3 id="about-{{ $key }}-title" class="about__headline">
                            @foreach ($story['headline'] as $line)<span>{{ $line }}</span>@endforeach
                        </h3>
                        @isset($story['body'])<p class="about__body">{{ $story['body'] }}</p>@endisset
                        @isset($story['items'])
                            <ol class="about__missions">
                                @foreach ($story['items'] as $item)
                                    <li><h4>{{ $item['title'] }}</h4><p>{{ $item['body'] }}</p></li>
                                @endforeach
                            </ol>
                        @endisset
                    </div>
                </article>
            @endforeach
        </div>
        <div class="about__stage" data-about-stage hidden>
            <div class="about__frame">
                @for ($layer = 0; $layer < 2; $layer++)
                    <div class="about__layer" data-about-layer>
                        <img alt="" width="1280" height="720" decoding="async">
                        <video muted loop playsinline preload="none" width="1280" height="720" aria-hidden="true"></video>
                    </div>
                @endfor
                <button class="about__open" type="button" data-about-stage-open aria-haspopup="dialog" aria-controls="about-dialog" hidden>
                    <span class="about__play" aria-hidden="true">▶</span>
                </button>
            </div>
        </div>
    </div>
    <dialog class="about__dialog" id="about-dialog" data-about-dialog aria-label="{{ $about['section_label'] }}">
        <button class="about__close" type="button" data-about-close aria-label="{{ $about['close_video'] }}" autofocus><span aria-hidden="true">×</span></button>
        <video controls playsinline preload="none" width="1920" height="1080"></video>
    </dialog>
</section>
