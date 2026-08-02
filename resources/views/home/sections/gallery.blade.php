@php
  $galleryHeadingCopy = match (app()->getLocale()) {
      'en' => ['top' => 'SPACE FOR', 'bottom' => 'GALLERY'],
      'ar' => ['top' => 'مساحة', 'bottom' => 'للمعرض'],
      default => ['top' => 'AREA UNTUK', 'bottom' => 'GALERI'],
  };
  $galleryHeadingLabel = trim($galleryHeadingCopy['top'].' '.$galleryHeadingCopy['bottom']);
  $galleryDescription = trim((string) (
      $gallerySection['section_subtitle']
      ?? $gallerySection['subtitle']
      ?? ''
  ));
  $galleryDescriptionWords = preg_split('/\s+/u', $galleryDescription, -1, PREG_SPLIT_NO_EMPTY) ?: [];
  $galleryDescriptionChunkSize = max(1, (int) ceil(count($galleryDescriptionWords) / 3));
  $galleryDescriptionLines = array_values(array_filter(array_map(
      static fn (array $words): string => implode(' ', $words),
      array_chunk($galleryDescriptionWords, $galleryDescriptionChunkSize)
  )));
@endphp

<section class="galeri-section section" id="galeri" aria-labelledby="homepage-gallery-heading">
  <div class="container galeri-section__heading-shell">
    <header class="galeri-section__head gallery-heading-motion" data-gallery-heading>
      <div class="gallery-heading-motion__row gallery-heading-motion__row--top">
        <h2
          class="gallery-heading-motion__heading"
          id="homepage-gallery-heading"
          aria-label="{{ $galleryHeadingLabel }}"
        >
          <span class="gallery-heading-motion__clip" aria-hidden="true">
            <span class="gallery-heading-motion__line gallery-heading-motion__line--top">
              {{ $galleryHeadingCopy['top'] }}
            </span>
          </span>
        </h2>

        @if ($galleryDescription !== '')
          <p class="gallery-heading-motion__description" aria-label="{{ $galleryDescription }}">
            @foreach ($galleryDescriptionLines as $galleryDescriptionLine)
              <span class="gallery-heading-motion__description-clip" aria-hidden="true">
                <span class="gallery-heading-motion__description-line">
                  {{ $galleryDescriptionLine }}
                </span>
              </span>
            @endforeach
          </p>
        @endif
      </div>

      <div class="gallery-heading-motion__clip gallery-heading-motion__clip--bottom" aria-hidden="true">
        <span class="gallery-heading-motion__line gallery-heading-motion__line--bottom">
          {{ $galleryHeadingCopy['bottom'] }}
        </span>
      </div>
    </header>
  </div>

  @include('home.sections.gallery-depth')

  @if (! empty($gallerySection['cta']['href']) && ! empty($gallerySection['cta']['label']))
    <div class="container galeri-section__action">
      <a href="{{ $gallerySection['cta']['href'] }}" class="btn btn--white">
        {{ $gallerySection['cta']['label'] }}
      </a>
    </div>
  @endif
</section>
