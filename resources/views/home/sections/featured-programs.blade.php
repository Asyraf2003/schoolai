@php
  $programContent = trans('home_program');
  $programItems = collect($programContent['items'] ?? [])->values();
  $programHeadingLines = $programContent['heading_lines'] ?? [$programContent['section_label'] ?? ''];
  $programKineticWords = collect($programContent['kinetic_words'] ?? [])
    ->filter(fn ($word) => is_string($word) && trim($word) !== '')
    ->values();
  $programKineticLineCount = 20;
  $programMedia = [
    ['url' => 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=1800&q=82', 'position' => 'center 42%'],
    ['url' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1800&q=82', 'position' => 'center 46%'],
    ['url' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1800&q=82', 'position' => 'center 40%'],
    ['url' => 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&w=1800&q=82', 'position' => 'center 48%'],
    ['url' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1800&q=82', 'position' => 'center 44%'],
    ['url' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?auto=format&fit=crop&w=1800&q=82', 'position' => 'center 50%'],
  ];
@endphp

<section class="program-kinetic" id="program" aria-labelledby="program-kinetic-title" data-program-kinetic>
  <div class="program-kinetic__type" data-program-type aria-hidden="true">
    @for ($line = 0; $line < $programKineticLineCount; $line++)
      @php
        $wordCount = max(1, $programKineticWords->count());
        $lineWords = collect(range(0, 7))
          ->map(fn ($offset) => $programKineticWords[($line * 3 + $offset) % $wordCount] ?? '')
          ->filter()
          ->implode(' ');
      @endphp
      <div class="program-kinetic__kinetic-line program-kinetic__type-line" data-program-type-line>
        {{ $lineWords }} {{ $lineWords }}
      </div>
    @endfor
  </div>

  <div class="program-kinetic__handoff" data-program-handoff aria-hidden="true">
    @for ($handoffStep = 1; $handoffStep <= 11; $handoffStep++)
      <span class="program-kinetic__handoff-step" data-program-handoff-step="{{ $handoffStep }}"></span>
    @endfor
  </div>

  <header class="program-kinetic__header">
    <h2 class="program-kinetic__title" id="program-kinetic-title" data-text-role="display" data-program-heading
      aria-label="{{ $programContent['section_label'] ?? '' }}">
      @foreach ($programHeadingLines as $line)
        <span class="program-kinetic__title-line program-kinetic__title-line--{{ $loop->iteration }}">
          <span class="program-kinetic__title-text" data-program-heading-line aria-hidden="true">{{ $line }}</span>
        </span>
      @endforeach
    </h2>
  </header>

  <div class="program-kinetic__cards" data-program-cards>
    @foreach ($programItems as $program)
      @php($media = $programMedia[$loop->index] ?? $programMedia[0])
      <article class="program-kinetic__card" data-program-card>
        <button class="program-kinetic__trigger" type="button" data-program-open
          data-program-index="{{ $loop->index }}"
          aria-controls="program-detail-{{ strtolower($program['code']) }}"
          aria-haspopup="dialog"
          aria-label="{{ __('home_program.open_item', ['program' => $program['title']]) }}">
          <span class="program-kinetic__image-wrap">
            <img src="{{ $media['url'] }}" alt="{{ $program['title'] }}" width="1800" height="1200"
              loading="lazy" decoding="async" referrerpolicy="strict-origin-when-cross-origin"
              style="object-position: {{ $media['position'] }}" />
          </span>
          <span class="program-kinetic__caption">
            <strong class="program-kinetic__name">{{ $program['title'] }}</strong>
            <span class="program-kinetic__summary">{{ $program['description'] }}</span>
          </span>
        </button>
      </article>
    @endforeach
  </div>

  <div class="program-kinetic__detail-layer" data-program-detail-layer role="dialog"
    aria-modal="true" aria-label="{{ $programContent['section_label'] ?? '' }}" hidden>
    <button class="program-kinetic__back" type="button" data-program-back>
      <span class="program-kinetic__back-mark" aria-hidden="true">&lt;&lt;&lt;</span>
      <span>{{ $programContent['back'] }}</span>
    </button>
    <div class="program-kinetic__details">
      @foreach ($programItems as $program)
        @php($media = $programMedia[$loop->index] ?? $programMedia[0])
        <article class="program-kinetic__detail" id="program-detail-{{ strtolower($program['code']) }}"
          data-program-detail data-program-index="{{ $loop->index }}" hidden>
          <div class="program-kinetic__detail-copy">
            <h3>{{ $program['title'] }}</h3>
            <p class="program-kinetic__detail-description">{{ $program['description'] }}</p>
          </div>
          <div class="program-kinetic__detail-image-wrap" data-program-detail-image-wrap>
            <img src="{{ $media['url'] }}" alt="" width="1800" height="1200" loading="lazy" decoding="async"
              referrerpolicy="strict-origin-when-cross-origin" style="object-position: {{ $media['position'] }}"
              data-program-detail-image />
          </div>
        </article>
      @endforeach
    </div>
  </div>
</section>
