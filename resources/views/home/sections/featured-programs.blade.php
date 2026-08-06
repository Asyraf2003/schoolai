@php
  $programItems = collect($featuredPrograms['items'] ?? [])->values();
  $programMedia = [
    ['main' => 'media/home/vision-paper-01.webp', 'secondary' => 'media/home/9.png'],
    ['main' => 'media/home/vision-paper-02.webp', 'secondary' => 'media/home/10.png'],
    ['main' => 'media/home/vision-paper-03.webp', 'secondary' => 'media/home/11.png'],
    ['main' => 'media/home/9.png', 'secondary' => 'media/home/vision-paper-01.webp'],
    ['main' => 'media/home/10.png', 'secondary' => 'media/home/vision-paper-02.webp'],
    ['main' => 'media/home/11.png', 'secondary' => 'media/home/vision-paper-03.webp'],
  ];
  $firstProgram = $programItems->first() ?? [];
  $firstDescription = collect($firstProgram['text_parts'] ?? [])->pluck('text')->implode('');
  $programUi = match (app()->getLocale()) {
    'en' => [
      'eyebrow' => 'A living curriculum', 'journey' => 'School journey',
      'pillars' => 'Learning pillars', 'scroll' => 'Scroll to explore',
      'chapter' => 'Active chapter', 'apply' => 'Start admission',
      'gallery' => 'See school life', 'manifesto' => 'From foundation to contribution',
      'steps' => [['Rooted', 'Qur’an and character become daily habits.'], ['Exploring', 'Language, literacy, and projects open the world.'], ['Contributing', 'Children grow independent and useful to others.']],
    ],
    'ar' => [
      'eyebrow' => 'منهج حيّ', 'journey' => 'المراحل الدراسية',
      'pillars' => 'دعائم التعلّم', 'scroll' => 'مرّر للاستكشاف',
      'chapter' => 'الفصل النشط', 'apply' => 'ابدأ التسجيل',
      'gallery' => 'شاهد حياة المدرسة', 'manifesto' => 'من الجذور إلى الأثر',
      'steps' => [['راسخ', 'القرآن والأخلاق عادات يومية.'], ['مستكشف', 'اللغة والقراءة والمشاريع تفتح العالم.'], ['مؤثر', 'ينمو الطفل مستقلاً ونافعاً.']],
    ],
    default => [
      'eyebrow' => 'Kurikulum yang hidup', 'journey' => 'Jalur sekolah',
      'pillars' => 'Penguat pengalaman', 'scroll' => 'Scroll untuk menjelajah',
      'chapter' => 'Bab aktif', 'apply' => 'Mulai PPDB',
      'gallery' => 'Lihat kehidupan sekolah', 'manifesto' => 'Dari fondasi menuju kontribusi',
      'steps' => [['Berakar', 'Al-Qur’an dan adab hadir sebagai kebiasaan harian.'], ['Bereksplorasi', 'Bahasa, literasi, dan proyek membuka dunia anak.'], ['Berkontribusi', 'Anak tumbuh mandiri, berani, dan bermanfaat.']],
    ],
  };
@endphp

<section
  class="program-section section program-showcase"
  id="program"
  aria-labelledby="vision-program-title"
  data-program-showcase
>
  <div class="container">
    <header class="program-showcase__meta">
      <p>{{ $programUi['eyebrow'] }}</p>
      <div class="program-showcase__meta-progress" aria-hidden="true">
        <span data-program-stage-index>01</span>
        <i></i>
        <span>{{ str_pad((string) $programItems->count(), 2, '0', STR_PAD_LEFT) }}</span>
      </div>
      <p>{{ $programUi['scroll'] }} <span aria-hidden="true">↓</span></p>
    </header>

    <div class="program-section__shell program-showcase__layout">
      <div
        class="program-flow"
        aria-label="{{ $featuredPrograms['flow_aria_label'] ?? __('home.program_unggulan.flow_aria_label') }}"
      >
        @forelse ($programItems as $program)
          @if ($loop->index === 0)
            <p class="program-flow__group">{{ $programUi['journey'] }}</p>
          @elseif ($loop->index === 3)
            <p class="program-flow__group">{{ $programUi['pillars'] }}</p>
          @endif

          @php
            $description = collect($program['text_parts'] ?? [])->pluck('text')->implode('');
            $media = $programMedia[$loop->index] ?? $programMedia[0];
          @endphp

          <button
            type="button"
            class="program-card{{ $loop->first ? ' is-active' : '' }}"
            data-featured-program-card
            data-program-index="{{ $loop->iteration }}"
            data-program-code="{{ $program['code'] }}"
            data-program-label="{{ $program['label'] }}"
            data-program-title="{{ $program['title'] }}"
            data-program-summary="{{ $program['summary'] }}"
            data-program-description="{{ $description }}"
            data-program-accent="{{ $program['accent'] ?? '#0ea5e9' }}"
            data-program-media="{{ asset($media['main']) }}"
            data-program-secondary="{{ asset($media['secondary']) }}"
            aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
            style="--program-accent: {{ $program['accent'] ?? '#0ea5e9' }}"
          >
            <span class="program-card__index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
            <span class="program-card__body">
              <span class="program-card__label">{{ $program['label'] }}</span>
              <span class="program-card__title">{{ $program['title'] }}</span>
              <span class="program-card__summary">{{ $program['summary'] }}</span>
              <span class="program-card__description">{{ $description }}</span>
            </span>
            <span class="program-card__footer" aria-hidden="true">↗</span>
          </button>
        @empty
          <p class="program-empty">{{ $featuredPrograms['empty'] }}</p>
        @endforelse
      </div>

      <article
        class="program-spotlight"
        data-program-stage
        style="--program-accent: {{ $firstProgram['accent'] ?? '#0ea5e9' }}"
      >
        <figure class="program-stage__media" data-program-stage-visual>
          <img
            class="program-stage__image"
            src="{{ asset($programMedia[0]['main']) }}"
            alt="{{ $firstProgram['title'] ?? '' }}"
            width="1600"
            height="1200"
            loading="lazy"
            decoding="async"
            data-program-stage-image
          />
          <img
            class="program-stage__secondary"
            src="{{ asset($programMedia[0]['secondary']) }}"
            alt=""
            width="800"
            height="1000"
            loading="lazy"
            decoding="async"
            data-program-stage-secondary
          />
          <span class="program-stage__grid" aria-hidden="true"></span>
          <span class="program-stage__code" data-program-stage-code>{{ $firstProgram['code'] ?? '' }}</span>
          <figcaption>
            <span>{{ $programUi['chapter'] }}</span>
            <strong data-program-stage-label>{{ $firstProgram['label'] ?? '' }}</strong>
          </figcaption>
        </figure>

        <div class="program-spotlight__body" data-program-stage-content>
          <h3 class="program-spotlight__title" data-program-stage-title>
            {{ $firstProgram['title'] ?? ($featuredPrograms['title'] ?? '') }}
          </h3>
          <p class="program-spotlight__subtitle" data-program-stage-summary>
            {{ $firstProgram['summary'] ?? ($featuredPrograms['subtitle'] ?? '') }}
          </p>
          <p class="program-spotlight__description" data-program-stage-description>{{ $firstDescription }}</p>
          <div class="program-spotlight__chips">
            @foreach (($featuredPrograms['chips'] ?? []) as $chip)<span>{{ $chip }}</span>@endforeach
          </div>
          <div class="program-stage__actions">
            <a href="/ppdb">{{ $programUi['apply'] }} <span aria-hidden="true">↗</span></a>
            <a href="#galeri">{{ $programUi['gallery'] }} <span aria-hidden="true">↓</span></a>
          </div>
        </div>
        <span class="program-stage__progress" aria-hidden="true"><i></i></span>
      </article>
    </div>

    <footer class="program-manifesto">
      <p>{{ $programUi['manifesto'] }}</p>
      <div>
        @foreach ($programUi['steps'] as [$title, $description])
          <article><span>0{{ $loop->iteration }}</span><h3>{{ $title }}</h3><p>{{ $description }}</p></article>
        @endforeach
      </div>
    </footer>
  </div>
</section>
