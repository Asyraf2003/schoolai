      <!-- ======================= GALERI ======================= -->
      <section class="galeri-section section" id="galeri" aria-labelledby="homepage-gallery-heading">
        <div class="container">
          <header class="section-head section-head--center galeri-section__head reveal">
            <h2 class="section-title section-title--white" id="homepage-gallery-heading">
              {{ $gallerySection['section_title'] ?? $gallerySection['title'] }}
            </h2>
            @if (! empty($gallerySection['section_subtitle']))
              <p class="section-subtitle section-subtitle--white">
                {{ $gallerySection['section_subtitle'] }}
              </p>
            @elseif (! empty($gallerySection['subtitle']))
              <p class="section-subtitle section-subtitle--white">
                {{ $gallerySection['subtitle'] }}
              </p>
            @endif
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
