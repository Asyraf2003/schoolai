@php
  $programItems = collect($featuredPrograms['items'] ?? [])->values();
  $programMedia = [
    ['url' => 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=2400&q=82', 'position' => 'center 42%'],
    ['url' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=2400&q=82', 'position' => 'center 46%'],
    ['url' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=2400&q=82', 'position' => 'center 40%'],
    ['url' => 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&w=2400&q=82', 'position' => 'center 48%'],
    ['url' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=2400&q=82', 'position' => 'center 44%'],
    ['url' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?auto=format&fit=crop&w=2400&q=82', 'position' => 'center 50%'],
  ];
  $programUi = match (app()->getLocale()) {
    'en' => [
      'eyebrow' => 'Al-Mustaqbal programs', 'more' => 'Learn more',
      'rail' => 'Program journey', 'photo' => 'Temporary photo from Unsplash',
      'next' => 'Continue to our values',
    ],
    'ar' => [
      'eyebrow' => 'برامج المستقبل', 'more' => 'اكتشف المزيد',
      'rail' => 'رحلة البرامج', 'photo' => 'صورة مؤقتة من أنسبلاش',
      'next' => 'تابع إلى قيمنا',
    ],
    default => [
      'eyebrow' => 'Program Al-Mustaqbal', 'more' => 'Selengkapnya',
      'rail' => 'Perjalanan program', 'photo' => 'Foto sementara dari Unsplash',
      'next' => 'Lanjut ke nilai-nilai kami',
    ],
  };
  $programLink = route('portal.login');
@endphp

<section
  class="program-journey section"
  id="program"
  aria-labelledby="vision-program-title"
  data-program-journey
  data-program-total="{{ $programItems->count() }}"
>
  <div class="program-journey__hud" data-program-hud>
    <div class="program-journey__meta" aria-hidden="true">
      <p data-program-active-label>{{ $programUi['eyebrow'] }}</p>
      <p data-program-active-count></p>
    </div>
    <div class="program-journey__title-slot" data-program-title-slot></div>
    <div class="program-journey__copy-slot" data-program-copy-slot>
      <div data-program-description-slot></div>
      <a
        class="program-journey__link"
        href="{{ $programLink }}"
        data-program-active-link
        aria-label="{{ $programUi['more'] }}"
      >
        <span data-program-active-link-label>{{ $programUi['more'] }}</span>
        <span aria-hidden="true">↗</span>
      </a>
    </div>

    <nav class="program-journey__rail" aria-label="{{ $programUi['rail'] }}">
      <span class="program-journey__rail-focus" aria-hidden="true"></span>
      <div class="program-journey__rail-window" data-program-rail-window>
        <ol data-program-rail>
          @foreach ($programItems as $program)
            <li>
              <button
                type="button"
                data-program-rail-item
                data-program-index="{{ $loop->index }}"
                aria-label="{{ $program['title'] }}"
                aria-current="{{ $loop->first ? 'true' : 'false' }}"
              >
                <i aria-hidden="true"></i>
                <span>{{ $program['title'] }}</span>
              </button>
            </li>
          @endforeach
        </ol>
      </div>
    </nav>
  </div>

  <div class="program-journey__frames" data-program-frames>
    @forelse ($programItems as $program)
      @php
        $description = collect($program['text_parts'] ?? [])->pluck('text')->implode('');
        $media = $programMedia[$loop->index] ?? $programMedia[0];
      @endphp
      <article
        class="program-frame{{ $loop->first ? ' is-active' : '' }}"
        id="program-frame-{{ $loop->iteration }}"
        data-program-frame
        data-program-index="{{ $loop->index }}"
        data-program-label="{{ $program['label'] }}"
        data-program-title="{{ $program['title'] }}"
        data-program-description="{{ $description }}"
        data-program-link="{{ $programLink }}"
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
          <figcaption>
            <span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
            <span>{{ $program['label'] }}</span>
            <small>{{ $programUi['photo'] }}</small>
          </figcaption>
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

  <div class="program-journey__exit" data-program-exit aria-hidden="true">
    <div class="program-journey__exit-lines" data-program-exit-lines>
      @for ($line = 0; $line < 8; $line++)<span></span>@endfor
    </div>
    <p>{{ $programUi['next'] }}</p>
  </div>
</section>
