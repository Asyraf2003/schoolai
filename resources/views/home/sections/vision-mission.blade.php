@php
  $directionLocale = app()->getLocale();
  $visionLabels = [
      'id' => 'Visi Pendidikan',
      'en' => 'Education Vision',
      'ar' => 'الرؤية التربوية',
  ];
  $visionLabel = $visionLabels[$directionLocale] ?? $visionLabels['id'];
  $visionBackgrounds = [9, 10, 11, 12];
  $missionEffects = ['effect22', 'effect23', 'effect27', 'effect28'];
  $missionMotions = ['orbit', 'sweep', 'zoom', 'fold'];
  $missionColors = ['#075e62', '#7a3828', '#4d2c75', '#175b45'];
  $missionAssets = [9, 10, 11, 12];
  $missionPositions = ['start', 'center', 'end', 'center'];
@endphp

<section
  class="direction-story"
  id="visi-misi"
  aria-labelledby="direction-story-title"
  data-story-root
  data-story-kind="direction"
  data-story-locale="{{ $directionLocale }}"
>
  <article
    class="direction-story__scene direction-story__scene--vision direction-story__scene--position-center"
    data-story-scene
    data-story-position="center"
    data-story-color="#061d4f"
    data-story-art-motion="drift"
    style="--scene-color: #061d4f"
  >
    <div class="direction-story__scene-background" aria-hidden="true">
      @foreach ($visionBackgrounds as $asset)
        <img
          class="direction-story__art direction-story__art--vision-{{ $loop->iteration }}"
          data-story-scene-art
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

    <div class="direction-story__sticky">
      <p class="direction-story__display" data-story-text data-story-effect="effect25">
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

  <div
    class="direction-story__scene direction-story__scene--bridge direction-story__scene--position-center"
    data-story-scene
    data-story-position="center"
    data-story-color="#1e2a78"
    style="--scene-color: #1e2a78"
  >
    <div class="direction-story__scene-background" aria-hidden="true"></div>
    <div class="direction-story__sticky">
      <h3 class="direction-story__display" data-story-text data-story-effect="effect25">
        <span data-story-fragment>{{ $visiMisi['missions_intro']['title'] }}</span>
      </h3>
    </div>
  </div>

  <ol class="direction-story__list">
    @foreach ($visiMisi['missions'] as $mission)
      <li
        class="direction-story__scene direction-story__scene--mission direction-story__scene--position-{{ $missionPositions[$loop->index] }}"
        data-story-scene
        data-story-position="{{ $missionPositions[$loop->index] }}"
        data-story-color="{{ $missionColors[$loop->index] }}"
        data-story-art-motion="{{ $missionMotions[$loop->index] }}"
        style="--scene-color: {{ $missionColors[$loop->index] }}"
      >
        <div class="direction-story__scene-background" aria-hidden="true">
          <img
            class="direction-story__art direction-story__art--mission"
            data-story-scene-art
            data-story-depth="{{ $loop->iteration }}"
            src="{{ asset('media/home/'.$missionAssets[$loop->index].'.png') }}"
            alt=""
            width="1600"
            height="2000"
            loading="lazy"
            decoding="async"
          />
        </div>

        <div class="direction-story__sticky">
          <div
            class="direction-story__accent"
            style="--mission-accent: {{ $mission['accent'] ?? '#ffffff' }}"
            aria-hidden="true"
          ></div>
          <h4
            class="direction-story__display"
            data-story-text
            data-story-effect="{{ $missionEffects[$loop->index] }}"
          >
            <span class="direction-story__label" data-story-fragment>
              {{ $mission['title'] }}
            </span>
            @foreach ($mission['text_parts'] as $part)
              @php
                $hasArabicHonorific = $directionLocale === 'ar'
                  && str_contains($part['text'], 'ﷺ');
                $partText = $hasArabicHonorific
                  ? trim(str_replace('ﷺ', '', $part['text']))
                  : $part['text'];
              @endphp
              <span
                class="direction-story__fragment{{ ! empty($part['mark']) ? ' direction-story__mark direction-story__mark--'.$part['mark'] : '' }}"
                data-story-fragment
              >{{ $partText }}@if ($hasArabicHonorific)<span class="direction-story__honorific" data-story-honorific>صلى الله عليه وسلم</span>@endif</span>
            @endforeach
          </h4>
        </div>
      </li>
    @endforeach
  </ol>
</section>
