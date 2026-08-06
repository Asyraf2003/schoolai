      <!-- ======================= PROGRAM UNGGULAN ======================= -->
      @php
        $programItems = collect($featuredPrograms['items'] ?? [])->values();
        $firstProgram = $programItems->first() ?? [];
        $firstDescription = collect($firstProgram['text_parts'] ?? [])->pluck('text')->implode('');
        $programUi = match (app()->getLocale()) {
          'en' => [
            'eyebrow' => 'Al-Mustaqbal learning atlas',
            'journey' => 'School journey',
            'pillars' => 'Learning pillars',
          ],
          'ar' => [
            'eyebrow' => 'خريطة التعلّم في المستقبل',
            'journey' => 'المراحل الدراسية',
            'pillars' => 'دعائم التعلّم',
          ],
          default => [
            'eyebrow' => 'Atlas belajar Al-Mustaqbal',
            'journey' => 'Jalur sekolah',
            'pillars' => 'Penguat pengalaman',
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
          <div class="program-showcase__meta" aria-hidden="true">
            <span>{{ $programUi['eyebrow'] }}</span>
            <span class="program-showcase__count">
              <strong data-program-stage-index>01</strong>
              / {{ str_pad((string) $programItems->count(), 2, '0', STR_PAD_LEFT) }}
            </span>
          </div>

          <div class="program-section__shell">
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
                  $programDescription = collect($program['text_parts'] ?? [])->pluck('text')->implode('');
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
                  data-program-description="{{ $programDescription }}"
                  data-program-accent="{{ $program['accent'] ?? '#0ea5e9' }}"
                  aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                  style="--program-accent: {{ $program['accent'] ?? '#0ea5e9' }}"
                >
                  <span class="program-card__orb" aria-hidden="true"></span>

                  <span class="program-card__head">
                    <span class="program-card__code">{{ $program['code'] }}</span>
                    <span class="program-card__label">{{ $program['label'] }}</span>
                  </span>

                  <span class="program-card__body">
                    <span class="program-card__title">{{ $program['title'] }}</span>
                    <span class="program-card__summary">{{ $program['summary'] }}</span>
                    <span class="program-card__description">{{ $programDescription }}</span>
                  </span>

                  <span class="program-card__footer" aria-hidden="true">
                    <span class="program-card__arrow">↗</span>
                  </span>
                </button>
              @empty
                <p class="program-empty">{{ $featuredPrograms['empty'] }}</p>
              @endforelse
            </div>

            <article
              class="program-spotlight reveal"
              data-program-stage
              style="--program-accent: {{ $firstProgram['accent'] ?? '#0ea5e9' }}"
            >
              <span class="program-spotlight__mesh" aria-hidden="true"></span>

              <div class="program-spotlight__visual" data-program-stage-visual aria-hidden="true">
                <span class="program-spotlight__ring"></span>
                <span class="program-spotlight__ring"></span>
                <span class="program-spotlight__ring"></span>
                <span class="program-spotlight__code" data-program-stage-code>
                  {{ $firstProgram['code'] ?? '' }}
                </span>
              </div>

              <div class="program-spotlight__body" data-program-stage-content>
                <p class="program-spotlight__kicker" data-program-stage-label>
                  {{ $firstProgram['label'] ?? '' }}
                </p>
                <h3 class="program-spotlight__title" data-program-stage-title>
                  {{ $firstProgram['title'] ?? ($featuredPrograms['title'] ?? '') }}
                </h3>
                <p class="program-spotlight__subtitle" data-program-stage-summary>
                  {{ $firstProgram['summary'] ?? ($featuredPrograms['subtitle'] ?? '') }}
                </p>
                <p class="program-spotlight__description" data-program-stage-description>
                  {{ $firstDescription }}
                </p>
              </div>

              <div
                class="program-spotlight__chips"
                aria-label="{{ $featuredPrograms['chips_aria_label'] ?? __('home.program_unggulan.chips_aria_label') }}"
              >
                @foreach (($featuredPrograms['chips'] ?? []) as $chip)
                  <span>{{ $chip }}</span>
                @endforeach
              </div>
            </article>
          </div>
        </div>
      </section>
