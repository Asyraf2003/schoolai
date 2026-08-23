<section
  class="values-story"
  id="nilai"
  aria-labelledby="values-story-heading"
  data-values-story
>
  <div class="values-story__entry" aria-hidden="true"></div>

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

  <div class="values-story__timeline" data-values-timeline>
    <div class="values-story__clip" data-values-stage>
      <div class="values-story__spatial" data-values-spatial aria-hidden="true">
        <span class="values-story__spatial-fallback values-story__spatial-fallback--one"></span>
        <span class="values-story__spatial-fallback values-story__spatial-fallback--two"></span>
        <span class="values-story__spatial-fallback values-story__spatial-fallback--three"></span>
      </div>

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
                      <span class="values-card__index" data-text-role="meta">
                        {{ $value['display_index'] }}
                      </span>

                      <div class="values-card__copy">
                        <p class="values-card__summary" data-text-role="subtitle">
                          {{ $value['summary'] }}
                        </p>
                        <h3 class="values-card__title" data-text-role="component-title">
                          {{ $value['title'] }}
                        </h3>
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
                    </div>

                    <div class="values-card__face values-card__back" aria-hidden="true">
                      <span class="values-card__back-brand">AL MUSTAQBAL</span>
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
