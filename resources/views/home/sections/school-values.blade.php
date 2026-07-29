      <!-- ======================= NILAI SEKOLAH ======================= -->
      <section class="nilai-section section" id="nilai" aria-labelledby="nilai-heading">
        <div class="container">
          <div class="nilai-section__shell">
            @include('home.partials.editorial-section-heading', [
              'title' => $schoolValues['title'],
              'description' => $schoolValues['subtitle'],
              'headingId' => 'nilai-heading',
              'className' => 'nilai-section__intro',
            ])

            <div class="nilai-grid" aria-label="{{ $schoolValues['aria_label'] ?? __('home.nilai_sekolah.aria_label') }}">
              @foreach ($schoolValues['items'] as $value)
                <button
                  type="button"
                  class="nilai-card{{ $loop->first ? ' is-active' : '' }} reveal{{ $loop->index > 0 ? ' reveal--delay-' . $loop->index : '' }}"
                  data-school-value-card
                  aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                  style="--nilai-accent: {{ $value['accent'] ?? '#a855f7' }}"
                >
                  <span class="nilai-card__glow" aria-hidden="true"></span>
                  <span class="nilai-card__top">
                    <span class="nilai-card__code">{{ $value['code'] }}</span>
                    <span class="nilai-card__spark" aria-hidden="true"></span>
                  </span>

                  <span class="nilai-card__content">
                    <span class="nilai-card__title">{{ $value['title'] }}</span>
                    <span class="nilai-card__summary">{{ $value['summary'] }}</span>
                    <span class="nilai-card__description">
                      @foreach ($value['text_parts'] as $part)
                        @if (! empty($part['mark']))
                          <span class="nilai-mark nilai-mark--{{ $part['mark'] }}">{{ $part['text'] }}</span>
                        @else
                          {{ $part['text'] }}
                        @endif
                      @endforeach
                    </span>
                  </span>
                </button>
              @endforeach
            </div>
          </div>
        </div>
      </section>
