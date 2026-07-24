      <!-- ======================= PROGRAM UNGGULAN ======================= -->
      <section class="program-section section" id="program" aria-labelledby="program-heading">
        <div class="container">
          <header class="section-head section-head--center program-section__head reveal">
            <h2 class="section-title" id="program-heading">{{ $featuredPrograms['section_title'] ?? $featuredPrograms['title'] }}</h2>
            @if (! empty($featuredPrograms['section_subtitle']))
              <p class="section-subtitle">{{ $featuredPrograms['section_subtitle'] }}</p>
            @elseif (! empty($featuredPrograms['subtitle']))
              <p class="section-subtitle">{{ $featuredPrograms['subtitle'] }}</p>
            @endif
          </header>

          <div class="program-section__shell">
            <aside class="program-spotlight reveal">
              <h3 class="program-spotlight__title">{{ $featuredPrograms['title'] }}</h3>
              <p class="program-spotlight__subtitle">
                {{ $featuredPrograms['subtitle'] }}
              </p>

              <div class="program-spotlight__chips" aria-label="{{ $featuredPrograms['chips_aria_label'] ?? __('home.program_unggulan.chips_aria_label') }}">
                @foreach (($featuredPrograms['chips'] ?? []) as $chip)
                  <span>{{ $chip }}</span>
                @endforeach
              </div>
            </aside>

            <div class="program-flow" aria-label="{{ $featuredPrograms['flow_aria_label'] ?? __('home.program_unggulan.flow_aria_label') }}">
              @forelse ($featuredPrograms['items'] as $program)
                <button
                  type="button"
                  class="program-card{{ $loop->first ? ' is-active' : '' }}"
                  data-featured-program-card
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
                    <span class="program-card__description">
                      @foreach ($program['text_parts'] as $part)
                        @if (! empty($part['mark']))
                          <span class="program-mark program-mark--{{ $part['mark'] }}">{{ $part['text'] }}</span>
                        @else
                          {{ $part['text'] }}
                        @endif
                      @endforeach
                    </span>
                  </span>

                  <span class="program-card__footer">
                    <span aria-hidden="true">→</span>
                  </span>
                </button>
              @empty
                <p class="program-empty">{{ $featuredPrograms['empty'] }}</p>
              @endforelse
            </div>
          </div>
        </div>
      </section>
