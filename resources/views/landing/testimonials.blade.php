<section class="testimonials" id="testimoni" data-testimonials aria-labelledby="testimonials-title">
    <header class="testimonials__header">
        <h2 id="testimonials-title">{{ $testimonials['title'] }}</h2>
        <p>{{ $testimonials['description'] }}</p>
    </header>
    @foreach ($testimonials['rows'] as $row)
        <div class="testimonials__row" tabindex="0" role="region" aria-label="{{ $testimonials['title'] }} {{ $loop->iteration }}">
            @foreach ($row['cards'] as $card)
                <article class="testimonials__card">
                    @if ($card['background_url'])
                        <img src="{{ $card['background_url'] }}" alt="" loading="lazy" decoding="async" width="960" height="480">
                    @endif
                    <blockquote><p>{{ $card['quote'] }}</p></blockquote>
                    <cite>{{ $card['name'] ?? $card['role'] }}</cite>
                </article>
            @endforeach
        </div>
    @endforeach
</section>
