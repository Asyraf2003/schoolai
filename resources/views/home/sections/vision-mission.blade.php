@php
  $directionLocale = app()->getLocale();
  $visionLabels = [
      'id' => 'Visi Pendidikan',
      'en' => 'Education Vision',
      'ar' => 'الرؤية التربوية',
  ];
  $visionLabel = $visionLabels[$directionLocale] ?? $visionLabels['id'];
  $visionBackgrounds = [9, 10, 11, 12];
@endphp

<section
  class="direction-story"
  id="visi-misi"
  aria-labelledby="direction-story-title"
  data-story-root
  data-story-kind="direction"
  data-story-locale="{{ $directionLocale }}"
>
  <div class="direction-story__background" aria-hidden="true">
    @foreach ($visionBackgrounds as $asset)
      <img
        class="direction-story__background-image direction-story__background-image--{{ $loop->iteration }}"
        data-story-bg-layer
        data-story-depth="{{ $loop->iteration }}"
        src="{{ asset('media/home/'.$asset.'.png') }}"
        alt=""
        width="1600"
        height="2000"
        loading="lazy"
        decoding="async"
      />
    @endforeach
  </div>

  <article class="direction-story__scene" data-story-scene>
    <div class="direction-story__sticky">
      <p class="direction-story__display" data-story-text data-story-effect="stretch">
        <span
          class="direction-story__label"
          id="direction-story-title"
          data-story-fragment
        >{{ $visionLabel }}</span>
        @foreach ($visiMisi['vision']['text_parts'] as $part)
          <span
            class="direction-story__fragment{{ ! empty($part['mark']) ? ' direction-story__mark direction-story__mark--'.$part['mark'] : '' }}"
            data-story-fragment
          >{{ $part['text'] }}</span>
        @endforeach
      </p>
    </div>
  </article>

  <div class="direction-story__scene direction-story__scene--bridge" data-story-scene>
    <div class="direction-story__sticky">
      <h3 class="direction-story__display" data-story-text data-story-effect="stretch">
        <span data-story-fragment>{{ $visiMisi['missions_intro']['title'] }}</span>
      </h3>
    </div>
  </div>

  <ol class="direction-story__list">
    @foreach ($visiMisi['missions'] as $mission)
      <li class="direction-story__scene" data-story-scene>
        <div class="direction-story__sticky">
          <div
            class="direction-story__accent"
            style="--mission-accent: {{ $mission['accent'] ?? '#ffffff' }}"
            aria-hidden="true"
          ></div>
          <h4 class="direction-story__display" data-story-text data-story-effect="stretch">
            <span class="direction-story__label" data-story-fragment>
              {{ $mission['title'] }}
            </span>
            @foreach ($mission['text_parts'] as $part)
              <span
                class="direction-story__fragment{{ ! empty($part['mark']) ? ' direction-story__mark direction-story__mark--'.$part['mark'] : '' }}"
                data-story-fragment
              >{{ $part['text'] }}</span>
            @endforeach
          </h4>
        </div>
      </li>
    @endforeach
  </ol>
</section>
