@php
  $directionLocale = app()->getLocale();
  $missionEffects = ['rise', 'fan', 'focus', 'stretch'];
@endphp

<section
  class="direction-story"
  id="visi-misi"
  aria-labelledby="direction-story-title"
  data-story-root
  data-story-kind="direction"
  data-story-locale="{{ $directionLocale }}"
>
  <header class="direction-story__intro" data-story-scene>
    <p class="direction-story__kicker">{{ $visiMisi['section_subtitle'] }}</p>
    <h2
      class="direction-story__heading"
      id="direction-story-title"
      data-story-text
      data-story-effect="rise"
    >{{ $visiMisi['section_title'] }}</h2>
  </header>

  <article class="direction-story__vision" data-story-scene>
    <p class="direction-story__index">00</p>
    <div class="direction-story__vision-copy">
      <h3 data-story-text data-story-effect="focus">
        {{ $visiMisi['vision']['title'] }}
      </h3>
      <p>
        @foreach ($visiMisi['vision']['text_parts'] as $part)
          @if (! empty($part['mark']))
            <span class="direction-story__mark">{{ $part['text'] }}</span>
          @else
            {{ $part['text'] }}
          @endif
        @endforeach
      </p>
    </div>
  </article>

  <div class="direction-story__missions" aria-labelledby="direction-missions-title">
    <h3
      class="direction-story__missions-heading"
      id="direction-missions-title"
      data-story-text
      data-story-effect="stretch"
      data-story-scene
    >{{ $visiMisi['missions_intro']['title'] }}</h3>

    <ol class="direction-story__list">
      @foreach ($visiMisi['missions'] as $mission)
        <li
          class="direction-story__mission"
          data-story-scene
          style="--mission-accent: {{ $mission['accent'] ?? '#ffffff' }}"
        >
          <p class="direction-story__index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</p>
          <div class="direction-story__mission-copy">
            <h4
              data-story-text
              data-story-effect="{{ $missionEffects[$loop->index] ?? 'rise' }}"
            >{{ $mission['title'] }}</h4>
            <p>
              @foreach ($mission['text_parts'] as $part)
                @if (! empty($part['mark']))
                  <span class="direction-story__mark">{{ $part['text'] }}</span>
                @else
                  {{ $part['text'] }}
                @endif
              @endforeach
            </p>
          </div>
        </li>
      @endforeach
    </ol>
  </div>
</section>
