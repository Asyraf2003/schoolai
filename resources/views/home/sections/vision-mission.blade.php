@php
  $locale = app()->getLocale();
  $arabicHonorific = 'صلى الله عليه وسلم';
  $visionLabel = match ($locale) {
    'ar' => 'الرؤية',
    'en' => 'VISION',
    default => 'VISI',
  };
  $missionLabel = match ($locale) {
    'ar' => 'الرسالة',
    'en' => 'MISSION',
    default => 'MISI',
  };
  $schoolImages = [
    asset('media/home/vision-paper-01.webp'),
    asset('media/home/vision-paper-02.webp'),
    asset('media/home/vision-paper-03.webp'),
  ];
@endphp

<section
  class="vision-paper"
  id="visi-misi"
  aria-labelledby="vision-paper-title"
  data-vision-story
>
  <div class="vision-paper__pin" data-vision-pin>
    <div class="vision-paper__track" data-vision-track>
      <div class="vision-paper__scene" data-vision-intro>
        <div class="vision-paper__copy-layout">
          <article
            class="vision-paper__copy vision-paper__copy--vision"
            data-vision-copy="vision"
          >
            <p class="vision-paper__kicker" data-vision-typography="vision">
              {{ $visionLabel }}
            </p>
            <h2 id="vision-paper-title" class="sr-only">
              {{ $visiMisi['section_title'] }}
            </h2>
            <p
              class="vision-paper__vision-text"
              data-vision-typography="vision"
            >
              @foreach ($visiMisi['vision']['text_parts'] as $part)
                @if (! empty($part['mark']))
                  <strong class="vision-paper__mark vision-paper__mark--{{ $part['mark'] }}">
                    {{ $part['text'] }}
                  </strong>
                @else
                  {{ $part['text'] }}
                @endif
              @endforeach
            </p>
          </article>

          <article
            class="vision-paper__copy vision-paper__copy--mission"
            data-vision-copy="mission"
          >
            <p class="vision-paper__kicker" data-vision-typography="mission">
              {{ $missionLabel }}
            </p>
            <p
              class="vision-paper__mission-text"
              data-vision-mission-text
              data-vision-typography="mission"
            >
              @foreach ($visiMisi['missions'] as $mission)
                @foreach ($mission['text_parts'] as $part)
                  @php
                    $partText = $locale === 'ar'
                      ? str_replace('ﷺ', $arabicHonorific, $part['text'])
                      : $part['text'];
                  @endphp
                  {{ $partText }}
                @endforeach
                @unless ($loop->last) {{ ' ' }} @endunless
              @endforeach
            </p>
          </article>
        </div>

        <figure class="vision-paper__square" data-vision-image-square>
          <img
            src="{{ $schoolImages[0] }}"
            alt="Aktivitas belajar di ruang kelas"
            width="1600"
            height="1600"
            loading="lazy"
            decoding="async"
            fetchpriority="low"
            data-vision-art
          />

          <div class="vision-paper__frame" data-vision-image-frame>
            <div class="vision-paper__image-stack" data-vision-image-stack>
              <img
                src="{{ $schoolImages[1] }}"
                alt="Guru mendampingi kegiatan belajar anak"
                width="1920"
                height="1080"
                loading="lazy"
                decoding="async"
                fetchpriority="low"
                data-vision-art
              />
              <img
                src="{{ $schoolImages[2] }}"
                alt="Anak belajar bersama di kelas"
                width="1920"
                height="1080"
                loading="lazy"
                decoding="async"
                fetchpriority="low"
                data-vision-art
              />
            </div>
          </div>
        </figure>
      </div>
    </div>
  </div>
</section>
