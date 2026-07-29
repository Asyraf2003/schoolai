      <!-- ======================= GALERI ======================= -->
      @php
        $galleryHeadingCopy = match (app()->getLocale()) {
            'en' => ['top' => 'SPACE FOR', 'bottom' => 'GALLERY'],
            'ar' => ['top' => 'مساحة', 'bottom' => 'للمعرض'],
            default => ['top' => 'AREA UNTUK', 'bottom' => 'GALERI'],
        };
        $galleryHeadingLabel = trim($galleryHeadingCopy['top'].' '.$galleryHeadingCopy['bottom']);
      @endphp

      <section class="galeri-section section" id="galeri" aria-labelledby="homepage-gallery-heading">
        <div class="container">
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

              @if (! empty($gallerySection['section_subtitle']))
                <p class="gallery-heading-motion__description">
                  {{ $gallerySection['section_subtitle'] }}
                </p>
              @elseif (! empty($gallerySection['subtitle']))
                <p class="gallery-heading-motion__description">
                  {{ $gallerySection['subtitle'] }}
                </p>
              @endif
            </div>

            <div class="gallery-heading-motion__clip gallery-heading-motion__clip--bottom" aria-hidden="true">
              <span class="gallery-heading-motion__line gallery-heading-motion__line--bottom">
                {{ $galleryHeadingCopy['bottom'] }}
              </span>
            </div>
          </header>


          @include('home.sections.gallery-story')


          @if (! empty($gallerySection['cta']['href']) && ! empty($gallerySection['cta']['label']))
            <div class="galeri-section__action">
              <a href="{{ $gallerySection['cta']['href'] }}" class="btn btn--white">
                {{ $gallerySection['cta']['label'] }}
              </a>
            </div>
          @endif
        </div>
      </section>
