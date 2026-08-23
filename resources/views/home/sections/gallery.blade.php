<section class="galeri-section section" id="galeri" aria-labelledby="homepage-gallery-heading">
  <div class="gallery-mask-handoff" data-gallery-mask-handoff aria-hidden="true">
    <svg
      class="gallery-mask-handoff__layer"
      data-gallery-mask-layer="values"
      viewBox="0 0 100 100"
      preserveAspectRatio="none"
    >
      <defs>
        <mask id="gallery-handoff-mask-values" maskUnits="userSpaceOnUse">
          <rect data-gallery-mask-base x="0" y="0" width="100" height="100" fill="white" />
          <g data-gallery-mask-blinds="values"></g>
        </mask>
      </defs>
      <rect
        data-gallery-mask-fill
        x="0"
        y="0"
        width="100"
        height="100"
        fill="#2038ff"
        mask="url(#gallery-handoff-mask-values)"
      />
    </svg>
  </div>

  <div class="container galeri-section__heading-shell">
    <header class="galeri-section__head gallery-heading-motion" data-gallery-heading>
      <div class="gallery-heading-motion__row gallery-heading-motion__row--top">
        <h2
          class="gallery-heading-motion__heading"
          id="homepage-gallery-heading"
        >
          <span class="gallery-heading-motion__clip" aria-hidden="true">
            <span class="gallery-heading-motion__line gallery-heading-motion__line--single">
              {{ $galleryHeading }}
            </span>
          </span>
          <span class="sr-only">{{ $galleryHeading }}</span>
        </h2>
      </div>
    </header>
  </div>

  @include('home.sections.gallery-depth')
</section>
