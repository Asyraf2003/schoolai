@php
  $locale = app()->getLocale();
  $programItems = collect($featuredPrograms['items'] ?? [])->values();
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
  $programMedia = [
    ['url' => 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=2400&q=82', 'position' => 'center 42%'],
    ['url' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=2400&q=82', 'position' => 'center 46%'],
    ['url' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=2400&q=82', 'position' => 'center 40%'],
    ['url' => 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&w=2400&q=82', 'position' => 'center 48%'],
    ['url' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=2400&q=82', 'position' => 'center 44%'],
    ['url' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?auto=format&fit=crop&w=2400&q=82', 'position' => 'center 50%'],
  ];
  $programUi = match ($locale) {
    'en' => ['more' => 'Learn more', 'rail' => 'Program journey'],
    'ar' => ['more' => 'اكتشف المزيد', 'rail' => 'رحلة البرامج'],
    default => ['more' => 'Selengkapnya', 'rail' => 'Perjalanan program'],
  };
  $handoffKicker = match ($locale) {
    'ar' => 'الرسالة',
    'en' => 'MISSION',
    default => 'MISI',
  };
  $handoffMission = collect($visiMisi['missions'] ?? [])
    ->flatMap(fn ($mission) => collect($mission['text_parts'] ?? [])->pluck('text'))
    ->implode(' ');
  if ($locale === 'ar') {
    $handoffMission = str_replace('ﷺ', 'صلى الله عليه وسلم', $handoffMission);
  }
  $handoffMedia = [
    asset('media/home/vision-paper-01.webp'),
    asset('media/home/vision-paper-02.webp'),
    asset('media/home/vision-paper-03.webp'),
  ];
  $programLink = route('portal.login');
@endphp

<section
  class="program-journey section"
  id="program"
  aria-labelledby="program-journey-title"
  data-program-journey
  data-program-total="{{ $programItems->count() }}"
>
  <div class="program-journey__sticky" data-program-sticky>
    <div class="program-journey__stage" data-program-stage>
      <div class="program-journey__handoff" data-program-handoff aria-hidden="true">
        <div class="program-handoff__mission" data-program-handoff-mission>
          <p class="program-handoff__kicker">{{ $handoffKicker }}</p>
          <p class="program-handoff__mission-text">{{ $handoffMission }}</p>
        </div>

        <figure class="program-handoff__image program-handoff__image--main" data-program-handoff-main>
          <img src="{{ $handoffMedia[0] }}" alt="" width="1600" height="1600" loading="lazy" decoding="async" />
        </figure>
        <figure class="program-handoff__image program-handoff__image--thumb-one" data-program-handoff-thumb-one>
          <img src="{{ $handoffMedia[1] }}" alt="" width="1920" height="1080" loading="lazy" decoding="async" />
        </figure>
        <figure class="program-handoff__image program-handoff__image--thumb-two" data-program-handoff-thumb-two>
          <img src="{{ $handoffMedia[2] }}" alt="" width="1920" height="1080" loading="lazy" decoding="async" />
        </figure>
      </div>

      <div class="program-journey__backgrounds" data-program-backgrounds>
        @forelse ($programItems as $program)
          @php
            $description = collect($program['text_parts'] ?? [])->pluck('text')->implode('');
            $media = $programMedia[$loop->index] ?? $programMedia[0];
          @endphp
          <article
            class="program-frame"
            id="program-frame-{{ $loop->iteration }}"
            data-program-frame
            data-program-index="{{ $loop->index }}"
            data-program-title-value="{{ $program['title'] }}"
            data-program-description-value="{{ $description }}"
            data-program-link-value="{{ $programLink }}"
            data-program-accent="{{ $program['accent'] ?? '#0ea5e9' }}"
            style="--program-accent: {{ $program['accent'] ?? '#0ea5e9' }}"
          >
            <figure class="program-frame__media" data-program-media>
              <img
                src="{{ $media['url'] }}"
                alt="{{ $program['title'] }}: {{ $program['summary'] }}"
                width="2400"
                height="1600"
                loading="lazy"
                decoding="async"
                referrerpolicy="strict-origin-when-cross-origin"
                style="object-position: {{ $media['position'] }}"
              />
            </figure>
            <div class="program-frame__fallback">
              <h3>{{ $program['title'] }}</h3>
              <p>{{ $description }}</p>
              <a href="{{ $programLink }}">{{ $programUi['more'] }} <span aria-hidden="true">↗</span></a>
            </div>
          </article>
        @empty
          <p class="program-empty">{{ $featuredPrograms['empty'] }}</p>
        @endforelse
      </div>

      <div class="program-journey__showcase" data-program-showcase>
        <div class="program-journey__title-box" data-program-title-box>
          <h2 id="program-journey-title" data-program-hud-title>{{ $programCopy['title'] }}</h2>
        </div>
        <div class="program-journey__copy-box" data-program-copy-box>
          <p data-program-hud-description>{{ $programCopy['description'] }}</p>
          <a class="program-journey__link" href="{{ $programLink }}" data-program-active-link>
            <span>{{ $programUi['more'] }}</span>
            <span aria-hidden="true">↗</span>
          </a>
        </div>
      </div>

      <nav class="program-journey__rail" data-program-rail aria-label="{{ $programUi['rail'] }}">
        <ol>
          @foreach ($programItems as $program)
            <li>
              <span
                data-program-rail-item
                data-program-index="{{ $loop->index }}"
                aria-current="false"
              >
                <i aria-hidden="true"></i>
                <b>{{ $program['title'] }}</b>
              </span>
            </li>
          @endforeach
        </ol>
      </nav>
    </div>
  </div>
</section>
