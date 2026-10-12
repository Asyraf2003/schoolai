<section class="testimonials" id="testimoni" data-testimonials aria-labelledby="testimonials-title">
    <header class="testimonials__intro" data-testimonials-intro>
        <div class="testimonials__intro-stage">
            <div class="testimonials__intro-copy" data-testimonials-copy>
                <h2 id="testimonials-title" aria-label="{{ $testimonials['title'] }}">
                    @foreach (explode(' ', $testimonials['title']) as $word)<span data-testimonials-word aria-hidden="true">{{ $word }}</span>{{ $loop->last ? '' : ' ' }}@endforeach
                </h2>
            </div>
        </div>
    </header>
    <p class="sr-only">{{ $testimonials['description'] }}</p>
    <p class="sr-only" id="testimonials-browse">{{ $testimonials['browse_instruction'] }}</p>
    <div class="testimonials__rows" data-testimonials-rows>
        @foreach ($testimonials['rows'] as $row)
            <div class="testimonials__row" data-testimonials-row tabindex="0" role="region"
                 aria-label="{{ $testimonials['eyebrow'] }} {{ $loop->iteration }}" aria-describedby="testimonials-browse">
                <div class="testimonials__track" data-testimonials-track>
                    @foreach ($row['cards'] as $card)
                        <article class="testimonials__card">
                            @if ($card['background_url'])
                                <img src="{{ $card['background_url'] }}" alt="" loading="lazy" decoding="async"
                                     fetchpriority="low" width="960" height="480">
                            @endif
                            <div class="testimonials__content">
                                <blockquote><p>{{ $card['quote'] }}</p></blockquote>
                                <footer><cite>{{ $card['name'] ?? $card['role'] }}</cite></footer>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</section>
