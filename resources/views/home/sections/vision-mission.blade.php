@php
  $locale = app()->getLocale();
  $arabicHonorific = 'صلى الله عليه وسلم';
  $visionLabel = match ($locale) {
    'ar' => 'الرؤية',
    'en' => 'Vision',
    default => 'Visi',
  };
  $missionLabel = match ($locale) {
    'ar' => 'الرسالة',
    'en' => 'Mission',
    default => 'Misi',
  };
  $programCopy = match ($locale) {
    'ar' => [
      'title' => 'برامج مدرسية للنمو والتعلّم وبناء الشخصية',
      'description' => 'تجارب تعليمية مترابطة تجمع بين الإيمان والعلم والإبداع والاستقلالية والحياة اليومية.',
    ],
    'en' => [
      'title' => 'School programs for growth, learning, and character',
      'description' => 'Connected learning experiences that bring together faith, knowledge, creativity, independence, and everyday life.',
    ],
    default => [
      'title' => 'Program sekolah untuk tumbuh, belajar, dan berkarakter',
      'description' => 'Rangkaian pengalaman belajar yang menghubungkan iman, ilmu, kreativitas, kemandirian, dan kehidupan sehari-hari.',
    ],
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
            <p class="vision-paper__kicker">{{ $visionLabel }}</p>
            <h2 id="vision-paper-title" class="sr-only">
              {{ $visiMisi['section_title'] }}
            </h2>
            <p class="vision-paper__vision-text">
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
            <p class="vision-paper__kicker">{{ $missionLabel }}</p>
            <ol class="vision-paper__mission-list">
              @foreach ($visiMisi['missions'] as $mission)
                @php
                  $isArabicFirstMission = $locale === 'ar' && $loop->first;
                @endphp
                <li>
                  <h3 id="vision-mission-title-{{ $loop->iteration }}">
                    {{ $mission['title'] }}
                  </h3>
                  <p class="vision-paper__mission-detail">
                    @foreach ($mission['text_parts'] as $part)
                      @php
                        $partText = $isArabicFirstMission
                          ? trim(str_replace(['ﷺ', $arabicHonorific], '', $part['text']))
                          : $part['text'];
                      @endphp
                      @if (! empty($part['mark']))
                        <strong class="vision-paper__mark vision-paper__mark--{{ $part['mark'] }}">
                          {{ $partText }}
                        </strong>
                      @else
                        {{ $partText }}
                      @endif
                    @endforeach
                    @if ($isArabicFirstMission)
                      <span data-vision-mission-honorific>{{ $arabicHonorific }}</span>
                    @endif
                  </p>
                </li>
              @endforeach
            </ol>
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

      <section
        class="vision-paper__program"
        aria-labelledby="vision-program-title"
        data-vision-program
      >
        <h2 id="vision-program-title">{{ $programCopy['title'] }}</h2>
        <p>{{ $programCopy['description'] }}</p>
      </section>
    </div>
  </div>
</section>
