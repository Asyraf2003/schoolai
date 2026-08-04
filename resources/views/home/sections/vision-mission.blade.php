@php
  $directionLocale = app()->getLocale();
  $visionAssets = [9, 10, 11, 12];
  $arabicHonorific = 'صلى الله عليه وسلم';
  $programTitle = $featuredPrograms['section_title']
    ?? __('home.program_unggulan.section_title');
@endphp

<section
  class="vision-story"
  id="visi-misi"
  aria-labelledby="vision-story-title"
  data-vision-story
>
  <div class="vision-story__pin" data-vision-pin>
    <div class="vision-story__stage">
      <div class="vision-story__track" data-vision-track>
        <header
          class="vision-story__editorial vision-story__track-item"
          data-vision-editorial
        >
          <p class="vision-story__eyebrow">
            {{ $visiMisi['section_subtitle'] }}
          </p>
          <h2 id="vision-story-title">
            {{ $visiMisi['section_title'] }}
          </h2>
        </header>

        <span
          class="vision-story__divider"
          data-vision-divider
          aria-hidden="true"
        ></span>

        <article
          class="vision-story__panel vision-story__panel--vision vision-story__track-item"
          data-vision-panel
          data-vision-panel-kind="vision"
          aria-labelledby="vision-panel-title"
        >
          <div class="vision-story__art" aria-hidden="true">
            @foreach ($visionAssets as $asset)
              <img
                src="{{ asset('media/home/'.$asset.'.png') }}"
                alt=""
                width="1600"
                height="2000"
                loading="lazy"
                decoding="async"
                fetchpriority="low"
                data-vision-art
              />
            @endforeach
          </div>

          <div class="vision-story__content">
            <span class="vision-story__label">
              {{ $visiMisi['vision']['title'] }}
            </span>
            <h3 id="vision-panel-title">
              {{ $visiMisi['vision']['title'] }}
            </h3>
            <p>
              @foreach ($visiMisi['vision']['text_parts'] as $part)
                @if (! empty($part['mark']))
                  <strong>{{ $part['text'] }}</strong>
                @else
                  {{ $part['text'] }}
                @endif
              @endforeach
            </p>
          </div>
        </article>

        <ol class="vision-story__missions" data-vision-mission-list>
          @foreach ($visiMisi['missions'] as $mission)
            @php
              $isArabicFirstMission = $directionLocale === 'ar' && $loop->first;
            @endphp

            <li class="vision-story__track-item">
              <article
                class="vision-story__panel vision-story__panel--mission"
                data-vision-panel
                data-vision-panel-kind="mission"
                aria-labelledby="vision-mission-title-{{ $loop->iteration }}"
              >
                <div class="vision-story__content">
                  <span class="vision-story__label">
                    {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                  </span>
                  <h4 id="vision-mission-title-{{ $loop->iteration }}">
                    {{ $mission['title'] }}
                  </h4>
                  <p>
                    @foreach ($mission['text_parts'] as $part)
                      @php
                        $partText = $isArabicFirstMission
                          ? trim(str_replace(['ﷺ', $arabicHonorific], '', $part['text']))
                          : $part['text'];
                      @endphp

                      @if (! empty($part['mark']))
                        <strong>{{ $partText }}</strong>
                      @else
                        {{ $partText }}
                      @endif
                    @endforeach

                    @if ($isArabicFirstMission)
                      <span data-vision-mission-honorific>{{ $arabicHonorific }}</span>
                    @endif
                  </p>
                </div>
              </article>
            </li>
          @endforeach
        </ol>

        <div
          class="vision-story__outro vision-story__track-item"
          data-vision-outro
        >
          <h3>{{ $programTitle }}</h3>
        </div>

        <div
          class="vision-story__canvas vision-story__track-item"
          data-vision-canvas
          aria-hidden="true"
        ></div>
      </div>
    </div>
  </div>
</section>
