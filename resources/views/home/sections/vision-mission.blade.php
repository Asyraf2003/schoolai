@php
  $directionLocale = app()->getLocale();
@endphp

<section
  class="section"
  id="visi-misi"
  aria-labelledby="vision-mission-title"
  data-vision-mission-static
>
  <div class="container">
    <header>
      <h2 class="section-title" id="vision-mission-title">
        {{ $visiMisi['section_title'] }}
      </h2>
      <p class="section-subtitle">
        {{ $visiMisi['section_subtitle'] }}
      </p>
    </header>

    <article aria-labelledby="vision-title">
      <h3 id="vision-title">{{ $visiMisi['vision']['title'] }}</h3>
      <p>
        @foreach ($visiMisi['vision']['text_parts'] as $part)
          @if (! empty($part['mark']))
            <strong>{{ $part['text'] }}</strong>
          @else
            {{ $part['text'] }}
          @endif
        @endforeach
      </p>
    </article>

    <section aria-labelledby="missions-title">
      <h3 id="missions-title">{{ $visiMisi['missions_intro']['title'] }}</h3>

      <ol data-vision-mission-list>
        @foreach ($visiMisi['missions'] as $mission)
          <li data-vision-mission-item>
            <article aria-labelledby="mission-title-{{ $loop->iteration }}">
              <h4 id="mission-title-{{ $loop->iteration }}">
                {{ $mission['title'] }}
              </h4>
              <p>
                @foreach ($mission['text_parts'] as $part)
                  @php
                    $hasArabicHonorific = $directionLocale === 'ar'
                      && str_contains($part['text'], 'ﷺ');
                    $partText = $hasArabicHonorific
                      ? trim(str_replace('ﷺ', '', $part['text']))
                      : $part['text'];
                  @endphp

                  @if (! empty($part['mark']))
                    <strong>{{ $partText }}</strong>
                  @else
                    {{ $partText }}
                  @endif

                  @if ($hasArabicHonorific)
                    <span data-vision-mission-honorific>صلى الله عليه وسلم</span>
                  @endif
                @endforeach
              </p>
            </article>
          </li>
        @endforeach
      </ol>
    </section>
  </div>
</section>
