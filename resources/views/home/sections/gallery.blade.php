      <!-- ======================= GALERI ======================= -->
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
        $galleryDescriptionWords = preg_split(
            '/\s+/u',
            $galleryDescription,
            -1,
            PREG_SPLIT_NO_EMPTY
        ) ?: [];
        $galleryDescriptionChunkSize = max(
            1,
            (int) ceil(count($galleryDescriptionWords) / 3)
        );
        $galleryDescriptionLines = array_values(array_filter(array_map(
            static fn (array $words): string => implode(' ', $words),
            array_chunk($galleryDescriptionWords, $galleryDescriptionChunkSize)
        )));
      @endphp

      <section class="galeri-section section" id="galeri" aria-labelledby="homepage-gallery-heading">
        <div class="gallery-backdrop" aria-hidden="true">
          @foreach ([9, 10, 11, 12] as $galleryBackdropImage)
            <img
              class="gallery-backdrop__image gallery-backdrop__image--{{ $loop->iteration }}"
              src="{{ asset('media/home/'.$galleryBackdropImage.'.png') }}"
              alt=""
              loading="lazy"
              decoding="async"
            />
          @endforeach
        </div>

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

              @if ($galleryDescription !== '')
                <p
                  class="gallery-heading-motion__description"
                  aria-label="{{ $galleryDescription }}"
                >
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
