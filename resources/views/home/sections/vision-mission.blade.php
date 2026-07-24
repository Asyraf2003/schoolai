      <!-- ======================= VISI MISI ======================= -->
      <section class="visi-misi section" id="visi-misi">
        <div class="container">
          <header class="section-head section-head--center visi-misi__head reveal">
            <h2 class="section-title">{{ $visiMisi['section_title'] ?? $visiMisi['vision']['title'] }}</h2>
            @if (! empty($visiMisi['section_subtitle']))
              <p class="section-subtitle">{{ $visiMisi['section_subtitle'] }}</p>
            @endif
          </header>

          <div class="visi-misi__shell">
            <article class="visi-card reveal" tabindex="0">
              <div class="visi-card__topline">
                <span class="visi-card__pulse" aria-hidden="true"></span>
              </div>

              <h3 class="visi-card__title">{{ $visiMisi['vision']['title'] }}</h3>

              <p class="visi-card__text">
                @foreach ($visiMisi['vision']['text_parts'] as $part)
                  @if (! empty($part['mark']))
                    <span class="vm-mark vm-mark--{{ $part['mark'] }}">{{ $part['text'] }}</span>
                  @else
                    {{ $part['text'] }}
                  @endif
                @endforeach
              </p>
            </article>

            <div class="misi-panel reveal reveal--delay-1">
              <div class="misi-panel__head">
                <h3>{{ $visiMisi['missions_intro']['title'] }}</h3>
              </div>

              <ol class="misi-list">
                @foreach ($visiMisi['missions'] as $mission)
                  <li class="misi-list__item">
                    <button
                      type="button"
                      class="misi-card{{ $loop->first ? ' is-active' : '' }}"
                      data-mission-card
                      aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                      style="--misi-accent: {{ $mission['accent'] ?? '#0ea5e9' }}"
                    >
                      <span class="misi-card__number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>

                      <span class="misi-card__body">
                        <span class="misi-card__title">{{ $mission['title'] }}</span>
                        <span class="misi-card__text">
                          @foreach ($mission['text_parts'] as $part)
                            @if (! empty($part['mark']))
                              <span class="vm-mark vm-mark--{{ $part['mark'] }}">{{ $part['text'] }}</span>
                            @else
                              {{ $part['text'] }}
                            @endif
                          @endforeach
                        </span>
                      </span>
                    </button>
                  </li>
                @endforeach
              </ol>
            </div>
          </div>
        </div>
      </section>
