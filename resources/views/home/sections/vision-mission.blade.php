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

    <div class="vision-arch__visuals" data-vision-visuals aria-hidden="true">
      @foreach ($schoolImages as $image)
        <figure class="vision-arch__visual" data-vision-visual="{{ $loop->index }}">
          <img
            src="{{ $image }}"
            alt=""
            width="1920"
            height="1440"
            loading="lazy"
            decoding="async"
            fetchpriority="low"
            data-vision-art
          />
        </figure>
      @endforeach
    </div>
  </div>
</section>
