<section class="program-kinetic" id="program" aria-labelledby="program-kinetic-title" data-program-kinetic>
  <div class="program-kinetic__type" data-program-type aria-hidden="true">
    @foreach ($programKineticLines as $lineWords)
      <div class="program-kinetic__kinetic-line program-kinetic__type-line" data-program-type-line>
        {{ $lineWords }}
      </div>
    @endforeach
  </div>

  <div class="program-kinetic__handoff" data-program-handoff aria-hidden="true">
    @for ($handoffStep = 1; $handoffStep <= 11; $handoffStep++)
      <span class="program-kinetic__handoff-step" data-program-handoff-step="{{ $handoffStep }}"></span>
    @endfor
  </div>

  <header class="program-kinetic__header home-section-display__header">
    <h2 class="program-kinetic__title home-section-display__title" id="program-kinetic-title" data-text-role="display" data-program-heading
      aria-label="{{ $programContent['section_label'] ?? '' }}">
      @foreach ($programHeadingLines as $line)
        <span class="program-kinetic__title-line home-section-display__line program-kinetic__title-line--{{ $loop->iteration }}">
          <span class="program-kinetic__title-text" data-program-heading-line aria-hidden="true">{{ $line }}</span>
        </span>
      @endforeach
    </h2>
  </header>

  <div class="program-kinetic__cards" data-program-cards>
    @foreach ($programItems as $program)
      <article class="program-kinetic__card" data-program-card>
        <button class="program-kinetic__trigger" type="button" data-program-open
          data-program-index="{{ $loop->index }}"
          aria-controls="{{ $program['detail_id'] }}"
          aria-haspopup="dialog"
          aria-label="{{ __('home_program.open_item', ['program' => $program['title']]) }}">
          <span class="program-kinetic__image-wrap">
            <img src="{{ $program['media']['url'] }}" alt="{{ $program['title'] }}" width="1800" height="1200"
              loading="lazy" decoding="async" referrerpolicy="strict-origin-when-cross-origin"
              style="object-position: {{ $program['media']['position'] }}" />
          </span>
          <span class="program-kinetic__caption">
            @if (! empty($program['eyebrow']))
              <span class="program-kinetic__eyebrow">{{ $program['eyebrow'] }}</span>
            @endif
            <strong class="program-kinetic__name">{{ $program['title'] }}</strong>
            <span class="program-kinetic__summary">{{ $program['summary'] }}</span>
          </span>
        </button>
      </article>
    @endforeach
  </div>

  <div class="program-kinetic__detail-layer" data-program-detail-layer role="dialog"
    aria-modal="true" aria-label="{{ $programContent['section_label'] ?? '' }}" hidden>
    <div class="program-kinetic__details">
      @foreach ($programItems as $program)
        <article class="program-kinetic__detail" id="{{ $program['detail_id'] }}"
          data-program-detail data-program-index="{{ $loop->index }}" hidden>
          <div class="program-kinetic__detail-copy">
            <button class="program-kinetic__back" type="button" data-program-back>
              <span class="program-kinetic__back-mark" aria-hidden="true">&lt;&lt;&lt;</span>
              <span class="program-kinetic__back-label">{{ $programContent['back'] }}</span>
            </button>
            <h3 data-title-scale="{{ $program['title_scale'] }}">{{ $program['title'] }}</h3>
            <p class="program-kinetic__detail-description">{{ $program['description'] }}</p>
          </div>
          <div class="program-kinetic__detail-media">
            <div class="program-kinetic__detail-image-wrap" data-program-detail-image-wrap>
              <img src="{{ $program['media']['url'] }}" alt="" width="1800" height="1200" loading="lazy" decoding="async"
                referrerpolicy="strict-origin-when-cross-origin" style="object-position: {{ $program['media']['position'] }}"
                data-program-detail-image />
            </div>
          </div>
        </article>
      @endforeach
    </div>
  </div>
</section>
