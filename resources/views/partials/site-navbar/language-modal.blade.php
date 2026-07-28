<div class="nav-overlay" id="navOverlay"></div>

<div
  class="language-modal"
  id="languageModal"
  data-language-modal
  aria-hidden="true"
>
  <button
    type="button"
    class="language-modal__backdrop"
    data-language-modal-close
    aria-label="{{ $languageModalClose }}"
  ></button>

  <div
    class="language-modal__dialog"
    role="dialog"
    aria-modal="true"
    aria-labelledby="languageModalTitle"
    tabindex="-1"
  >
    <button
      type="button"
      class="language-modal__close"
      data-language-modal-close
      aria-label="{{ $languageModalClose }}"
    >
      ×
    </button>

    <h2 class="language-modal__title" id="languageModalTitle" data-text-role="component-title">{{ $languageModalTitle }}</h2>

    <div class="language-modal__options">
      @foreach ($languageItem['options'] ?? [] as $option)
        <form method="POST" action="{{ route('language.switch', $option['locale']) }}" class="language-modal__form">
          @csrf
          <button
            type="submit"
            class="language-modal__option {{ $currentLocale === $option['locale'] ? 'is-active' : '' }}"
            lang="{{ $option['locale'] }}"
            aria-label="{{ $option['label'] }}"
            @if ($currentLocale === $option['locale']) aria-current="true" @endif
          >
            <span class="language-modal__flag">
              @include('partials.language-flag', ['locale' => $option['locale']])
            </span>
            <span class="language-modal__label" data-text-role="action">{{ $option['label'] }}</span>
          </button>
        </form>
      @endforeach
    </div>
  </div>
</div>
