<section
  class="vision-arch"
  id="visi-misi"
  aria-labelledby="vision-arch-title"
  data-vision-story
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
          @if (! $loop->first) aria-hidden="true" @endif
        >
          @if ($loop->first)
            <button
              type="button"
              class="vision-arch__video-trigger"
              data-about-video-open
              aria-label="{{ $aboutLabel }} video"
            >
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
                data-about-video-poster
              />
              <span class="vision-arch__video-cue" aria-hidden="true">
                <svg viewBox="0 0 32 32">
                  <path d="M12 8l12 8-12 8Z" />
                </svg>
              </span>
            </button>
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

  <dialog class="vision-video-modal" data-about-video-modal aria-label="{{ $aboutLabel }} video">
    <div class="vision-video-modal__surface" data-about-video-shell>
      <button
        type="button"
        class="vision-video-modal__close"
        data-about-video-close
        aria-label="Close video"
      >
        <span aria-hidden="true">×</span>
      </button>

      <video
        class="vision-video-modal__player"
        data-about-video-player
        data-about-video-src="{{ config('media.homepage_about_video_url') }}"
        playsinline
        webkit-playsinline
        preload="none"
      ></video>

      <div class="vision-video-modal__controls" data-about-video-controls>
        <button
          type="button"
          class="vision-video-modal__control vision-video-modal__control--play"
          data-about-video-toggle
          aria-label="Play video"
        >
          <span data-about-video-toggle-icon aria-hidden="true">▶</span>
        </button>

        <span class="vision-video-modal__time" data-about-video-current aria-hidden="true">0:00</span>

        <input
          type="range"
          class="vision-video-modal__seek"
          data-about-video-seek
          min="0"
          max="1000"
          step="1"
          value="0"
          aria-label="Video timeline"
        >

        <span class="vision-video-modal__time" data-about-video-duration aria-hidden="true">0:00</span>

        <button
          type="button"
          class="vision-video-modal__control vision-video-modal__control--mute"
          data-about-video-mute
          aria-label="Mute video"
        >
          <span data-about-video-mute-icon aria-hidden="true">●</span>
        </button>

        <input
          type="range"
          class="vision-video-modal__volume"
          data-about-video-volume
          min="0"
          max="1"
          step="0.05"
          value="1"
          aria-label="Video volume"
        >

        <button
          type="button"
          class="vision-video-modal__control vision-video-modal__control--fullscreen"
          data-about-video-fullscreen
          aria-label="Enter fullscreen"
        >
          <span aria-hidden="true">⛶</span>
        </button>
      </div>
    </div>
  </dialog>
</section>
