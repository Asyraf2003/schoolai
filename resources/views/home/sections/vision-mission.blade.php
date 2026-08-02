@php
  $directionLocale = app()->getLocale();
@endphp

<section
  class="direction-story"
  id="visi-misi"
  aria-labelledby="direction-story-title"
  data-story-root
  data-story-kind="direction"
  data-story-locale="{{ $directionLocale }}"
>
  <div class="direction-story__scene direction-story__scene--opening" data-story-scene>
    <div class="direction-story__sticky">
      <p class="direction-story__index">01</p>
      <h2
        class="direction-story__display"
        id="direction-story-title"
        data-story-text
        data-story-effect="stretch"
      >
        <span data-story-fragment>{{ $visiMisi['section_title'] }}</span>
      </h2>
    </div>
  </div>

  <article class="direction-story__scene" data-story-scene>
    <div class="direction-story__sticky">
      <p class="direction-story__index">02</p>
      <p class="direction-story__display" data-story-text data-story-effect="stretch">
        <span class="direction-story__label" data-story-fragment>
          {{ $visiMisi['vision']['title'] }}
        </span>
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
      <p class="direction-story__index">03</p>
      <h3 class="direction-story__display" data-story-text data-story-effect="stretch">
        <span data-story-fragment>{{ $visiMisi['missions_intro']['title'] }}</span>
      </h3>
    </div>
  </div>

  <ol class="direction-story__list">
    @foreach ($visiMisi['missions'] as $mission)
      <li class="direction-story__scene" data-story-scene>
        <div class="direction-story__sticky">
          <p class="direction-story__index">
            {{ str_pad((string) ($loop->iteration + 3), 2, '0', STR_PAD_LEFT) }}
          </p>
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
