<section
  class="testimonial-wall section"
  id="testimoni"
  data-testimonial-wall
  aria-labelledby="homepage-testimonial-heading"
>
  <header class="testimonial-wall__intro">
    <h2
      class="testimonial-wall__title"
      id="homepage-testimonial-heading"
      data-text-role="display"
    >{{ $testimonialSection['title'] ?? '' }}</h2>

    @if (! empty($testimonialSection['description']))
      <p class="testimonial-wall__description" data-text-role="description">
        {{ $testimonialSection['description'] }}
      </p>
    @endif
  </header>

  <div class="testimonial-wall__rows" data-testimonial-rows>
    @foreach ($testimonialSection['rows'] ?? [] as $row)
      <div class="testimonial-wall__viewport" data-testimonial-viewport>
        <div class="testimonial-wall__track" data-testimonial-track>
          @foreach ($row['cards'] ?? [] as $card)
            <article class="testimonial-wall__card" data-testimonial-card>
              @if (! empty($card['background_url']))
                <img
                  class="testimonial-wall__media"
                  src="{{ $card['background_url'] }}"
                  alt=""
                  loading="lazy"
                  decoding="async"
                  fetchpriority="low"
                />
              @endif

              <div class="testimonial-wall__content">
                <blockquote class="testimonial-wall__quote" data-text-role="component-title">
                  <p>{{ $card['quote'] ?? '' }}</p>
                </blockquote>

                <footer class="testimonial-wall__meta">
                  <cite class="testimonial-wall__name" data-text-role="meta">{{ $card['name'] ?? '' }}</cite>
                  <span class="testimonial-wall__role" data-text-role="label">{{ $card['role'] ?? '' }}</span>
                </footer>
              </div>
            </article>
          @endforeach
        </div>
      </div>
    @endforeach
  </div>
</section>
