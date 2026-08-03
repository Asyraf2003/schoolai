@php
  $valuesStoryHeading = match (app()->getLocale()) {
    'en' => ['line_one' => 'School', 'line_two' => 'Values'],
    'ar' => ['line_one' => 'قيم', 'line_two' => 'المدرسة'],
    default => ['line_one' => 'Nilai-Nilai', 'line_two' => 'Sekolah'],
  };
@endphp

<section
  class="values-story"
  id="nilai"
  aria-labelledby="values-story-heading"
  data-values-story
>
  <div class="values-story__stage" data-values-stage>
    <span class="values-story__curve values-story__curve--one" aria-hidden="true"></span>
    <span class="values-story__curve values-story__curve--two" aria-hidden="true"></span>

    <header class="values-story__headline">
      <p class="values-story__eyebrow" data-text-role="label">
        {{ $schoolValues['title'] }}
      </p>
      <h2
        class="values-story__title"
        id="values-story-heading"
        data-text-role="display"
      >
        <span>{{ $valuesStoryHeading['line_one'] }}</span>
        <span>{{ $valuesStoryHeading['line_two'] }}</span>
      </h2>
      <p class="values-story__description" data-text-role="description">
        {{ $schoolValues['subtitle'] }}
      </p>
    </header>

    <div
      class="values-story__cards"
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
                <h3 class="values-card__title" data-text-role="component-title">
                  {{ $value['title'] }}
                </h3>
                <p class="values-card__summary" data-text-role="subtitle">
                  {{ $value['summary'] }}
                </p>
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
        </article>
      @endforeach
    </div>
  </div>
</section>
