@php
  $headingTitle = trim((string) ($title ?? ''));
  $headingDescription = trim((string) ($description ?? ''));
  $headingDomId = trim((string) ($headingId ?? 'section-heading'));
  $headingExtraClass = trim((string) ($className ?? ''));
  $headingLineOne = trim((string) ($lineOne ?? ''));
  $headingLineTwo = trim((string) ($lineTwo ?? ''));
  $headingLineThree = trim((string) ($lineThree ?? ''));

  if ($headingLineOne === '' && $headingTitle !== '') {
      $headingWords = preg_split('/\s+/u', $headingTitle, -1, PREG_SPLIT_NO_EMPTY) ?: [];
      $headingSplit = max(1, (int) ceil(count($headingWords) / 2));
      $headingLineOne = implode(' ', array_slice($headingWords, 0, $headingSplit));
      $headingLineTwo = implode(' ', array_slice($headingWords, $headingSplit));
  }

  $descriptionWords = preg_split('/\s+/u', $headingDescription, -1, PREG_SPLIT_NO_EMPTY) ?: [];
  $descriptionSplit = max(1, (int) ceil(count($descriptionWords) / 3));
  $descriptionLines = array_values(array_filter(array_map(
      static fn (array $words): string => implode(' ', $words),
      array_chunk($descriptionWords, $descriptionSplit)
  )));
@endphp

<header class="section-head welcome-editorial-heading {{ $headingExtraClass }}" data-editorial-heading>
  <div class="welcome-editorial-heading__row">
    <h2 class="welcome-editorial-heading__heading" id="{{ $headingDomId }}" aria-label="{{ $headingTitle }}">
      <span class="welcome-editorial-heading__clip" aria-hidden="true">
        <span class="welcome-editorial-heading__line welcome-editorial-heading__line--top">
          {{ $headingLineOne }}
        </span>
      </span>
    </h2>

    @if ($headingDescription !== '')
      <p class="welcome-editorial-heading__description" aria-label="{{ $headingDescription }}">
        @foreach ($descriptionLines as $descriptionLine)
          <span class="welcome-editorial-heading__description-clip" aria-hidden="true">
            <span class="welcome-editorial-heading__description-line">{{ $descriptionLine }}</span>
          </span>
        @endforeach
      </p>
    @endif
  </div>

  @if ($headingLineTwo !== '')
    <span class="welcome-editorial-heading__clip welcome-editorial-heading__clip--bottom{{ $headingLineThree !== '' ? ' welcome-editorial-heading__clip--middle' : '' }}" aria-hidden="true">
      <span class="welcome-editorial-heading__line welcome-editorial-heading__line--bottom">
        {{ $headingLineTwo }}
      </span>
    </span>
  @endif

  @if ($headingLineThree !== '')
    <span class="welcome-editorial-heading__clip welcome-editorial-heading__clip--bottom welcome-editorial-heading__clip--third" aria-hidden="true">
      <span class="welcome-editorial-heading__line welcome-editorial-heading__line--third">
        {{ $headingLineThree }}
      </span>
    </span>
  @endif
</header>
