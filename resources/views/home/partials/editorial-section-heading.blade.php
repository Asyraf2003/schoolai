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
      <p class="welcome-editorial-heading__description">
        <span class="sr-only">{{ $headingDescription }}</span>
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
