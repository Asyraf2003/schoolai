<section
  class="vision-arch"
  id="visi-misi"
  aria-labelledby="vision-arch-title"
  data-vision-story
  style="--vision-about-pattern: url('{{ config('media.static.ornaments.geometry_33') }}'); --vision-vision-pattern: url('{{ config('media.static.ornaments.geometry_32') }}'); --vision-mission-pattern: url('{{ config('media.static.ornaments.geometry_33') }}')"
>
  <h2 id="vision-arch-title" class="sr-only">{{ $sectionLabel }}</h2>

  <div class="vision-arch__background" data-vision-background aria-hidden="true">
    <span class="vision-arch__background-layer" data-vision-background-layer="0"></span>
    <span class="vision-arch__background-layer" data-vision-background-layer="1"></span>
  </div>

  <div class="vision-arch__grid">
    <div class="vision-arch__stories" data-vision-stories>
      <article class="vision-arch__story" data-vision-panel="0">
        <div class="vision-arch__content">
          <p class="vision-arch__kicker">{{ $aboutLabel }}</p>
          <h3 class="vision-arch__heading">
            <span>{{ $aboutStory['headline_line_one'] ?? '' }}</span>
            <span>{{ $aboutStory['headline_line_two'] ?? '' }}</span>
          </h3>
          <p class="vision-arch__description">
            {{ $aboutStory['description'] ?? '' }}
          </p>
        </div>
      </article>

      <article class="vision-arch__story" data-vision-panel="1">
        <div class="vision-arch__content">
          <p class="vision-arch__kicker">{{ $visionLabel }}</p>
          <h3 class="vision-arch__heading">
            {{ $visiMisi['vision']['title'] ?? $visiMisi['section_title'] }}
          </h3>
          <p class="vision-arch__description vision-arch__description--vision">
            @foreach ($visiMisi['vision']['text_parts'] ?? [] as $part)
              @if (! empty($part['mark']))
                <strong class="vision-arch__mark vision-arch__mark--{{ $part['mark'] }}">
                  {{ $part['text'] }}
                </strong>
              @else
                {{ $part['text'] }}
              @endif
            @endforeach
          </p>
        </div>
      </article>

      <article class="vision-arch__story vision-arch__story--mission" data-vision-panel="2">
        <div class="vision-arch__content">
          <p class="vision-arch__kicker">{{ $missionLabel }}</p>
          <h3 class="vision-arch__heading">
            {{ $visiMisi['missions_intro']['title'] ?? $missionLabel }}
          </h3>
          <ol class="vision-arch__mission-list">
            @foreach ($visiMisi['missions'] ?? [] as $mission)
              <li>
                <h4>{{ $mission['title'] }}</h4>
                <p>
                  @foreach ($mission['text_parts'] ?? [] as $part)
                    {{ $part['text'] }}
                  @endforeach
                </p>
              </li>
            @endforeach
          </ol>
        </div>
      </article>
    </div>

    <div class="vision-arch__visuals" data-vision-visuals>
      @foreach ($schoolImages as $image)
        <figure
          class="vision-arch__visual"
          data-vision-visual="{{ $loop->index }}"
          @if ($loop->index === 1) aria-hidden="true" @endif
        >
          @if (isset($visionVideoSources[$loop->index]))
            <img class="vision-arch__preview-poster" data-vision-preview-poster
              src="{{ config('media.static.hero_school') }}" alt="" width="1920" height="1440"
              loading="lazy" decoding="async" fetchpriority="low" />
            @if ($visionVideoSources[$loop->index]['interactive'])
              <button
                type="button"
                class="vision-arch__video-trigger"
                data-vision-video-open
                data-vision-video-src="{{ $visionVideoSources[$loop->index]['url'] }}"
                data-vision-video-label="{{ $visionVideoSources[$loop->index]['label'] }}"
                aria-label="{{ $visionVideoSources[$loop->index]['label'] }} video"
              >
                <video
                  class="vision-arch__video-preview"
                  data-vision-video-preview
                  data-vision-video-src="{{ $visionVideoSources[$loop->index]['url'] }}"
                  data-vision-art
                  muted
                  loop
                  playsinline
                  webkit-playsinline
                  preload="none"
                  aria-hidden="true"
                ></video>
                <span class="vision-arch__video-cue" aria-hidden="true">
                  <svg viewBox="0 0 32 32">
                    <path d="M12 8l12 8-12 8Z" />
                  </svg>
                </span>
              </button>
            @else
              <video
                class="vision-arch__video-preview"
                data-vision-video-preview
                data-vision-video-src="{{ $visionVideoSources[$loop->index]['url'] }}"
                data-vision-art
                muted
                loop
                playsinline
                webkit-playsinline
                preload="none"
                aria-hidden="true"
              ></video>
            @endif
          @else
            <img
              data-lazy-media
              data-lazy-src="{{ $image }}"
              alt=""
              width="1920"
              height="1440"
              loading="lazy"
              decoding="async"
              fetchpriority="low"
              data-vision-art
            />
          @endif
        </figure>
      @endforeach
    </div>
  </div>

  @include('home.partials.vision-video-dialog')
</section>
