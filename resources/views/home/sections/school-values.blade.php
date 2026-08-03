@php
  $valuesHeading = $schoolValues['heading'] ?? '';
  $valuesHeadingLines = $schoolValues['heading_lines'] ?? [$valuesHeading];
@endphp

<section
  class="values-story"
  id="nilai"
  aria-labelledby="values-story-heading"
  data-values-story
>
  <div class="values-story__entry" aria-hidden="true"></div>

  <div class="values-story__timeline" data-values-timeline>
    <div class="values-story__clip" data-values-stage>
      <header class="values-story__headline" data-values-heading>
        <h2
          class="values-story__title"
          id="values-story-heading"
          data-text-role="display"
          aria-label="{{ $valuesHeading }}"
        >
          @foreach ($valuesHeadingLines as $line)
            <span class="values-story__title-line values-story__title-line--{{ $loop->first ? 'one' : 'two' }}">
              <span class="values-story__title-text" aria-hidden="true">
                {{ $line }}
              </span>
            </span>
          @endforeach
        </h2>

        <p class="values-story__description" data-text-role="description">
          {{ $schoolValues['subtitle'] }}
        </p>
      </header>

      <svg
        class="values-story__trail"
        viewBox="0 0 1600 900"
        preserveAspectRatio="none"
        aria-hidden="true"
      >
        <path
          class="values-story__trail-line"
          data-values-trail-path
          pathLength="1"
          d="M-120 770C220 980 500 930 650 650C810 350 760 120 620-80C540-200 760-180 900 40C1080 330 880 610 1070 760C1240 900 1450 570 1720 650"
        />
        <circle
          class="values-story__trail-head"
          data-values-trail-head
          cx="-120"
          cy="770"
          r="13"
        />
      </svg>

      <div class="values-story__perspective" data-values-perspective>
        <div
          class="values-story__grid"
          role="list"
          aria-label="{{ $schoolValues['aria_label'] ?? __('home.nilai_sekolah.aria_label') }}"
          data-values-cards
        >
          @foreach ($schoolValues['items'] as $value)
            <article
              class="values-card"
              role="listitem"
              data-values-card
              style="--values-accent: {{ $value['accent'] ?? '#7c3aed' }}"
            >
              <div class="values-card__pose" data-values-card-pose>
                <div class="values-card__float">
                  <div class="values-card__inner" data-values-card-inner>
                    <div class="values-card__face values-card__front">
                      <div class="values-card__top">
                        <span class="values-card__index" data-text-role="meta">
                          {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                        </span>
                        <strong class="values-card__code" aria-hidden="true">
                          {{ $value['code'] }}
                        </strong>
                      </div>

                      <div class="values-card__copy">
                        <p class="values-card__summary" data-text-role="subtitle">
                          {{ $value['summary'] }}
                        </p>
                        <h3 class="values-card__title" data-text-role="component-title">
                          {{ $value['title'] }}
                        </h3>
                        <span class="values-card__rule" aria-hidden="true"></span>
                        <p class="values-card__body" data-text-role="description">
                          @foreach ($value['text_parts'] as $part)
                            @if (! empty($part['mark']))
                              <strong>{{ $part['text'] }}</strong>
                            @else
                              {{ $part['text'] }}
                            @endif
                          @endforeach
                        </p>
                      </div>

                      <div class="values-card__footer" aria-hidden="true">
                        <strong>{{ $value['code'] }}</strong>
                        <span>{{ $value['title'] }}</span>
                      </div>
                    </div>

                    <div class="values-card__face values-card__back" aria-hidden="true">
                      <span class="values-card__back-frame"></span>
                      <span class="values-card__back-orbit"></span>
                      <strong class="values-card__back-code">{{ $value['code'] }}</strong>
                      <span class="values-card__back-title">{{ $value['title'] }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </article>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  <div class="values-story__exit" aria-hidden="true"></div>
</section>
