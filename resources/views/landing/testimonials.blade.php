<section class="testimonials" id="testimoni" data-testimonials aria-labelledby="testimonials-title">
    <div class="testimonials__inner">
        <header class="testimonials__intro">
            <h2 id="testimonials-title">{{ $testimonials['title'] }}</h2>
            <p>{{ $testimonials['description'] }}</p>
        </header>
        <div class="testimonials__wall" data-testimonials-wall>
            @foreach ($testimonials['cards'] as $card)
                <article class="testimonials__card" data-testimonial-card>
                    <div class="testimonials__lighting" aria-hidden="true"></div>
                    <header class="testimonials__person">
                        <span class="testimonials__avatar" aria-hidden="true">
                            @if ($card['avatar_url'])
                                <img src="{{ $card['avatar_url'] }}" alt="" loading="lazy" decoding="async"
                                     fetchpriority="low" width="40" height="40">
                            @endif
                        </span>
                        <div class="testimonials__identity">
                            <p class="testimonials__name"><bdi>{{ $card['name'] ?? $card['role'] }}</bdi></p>
                            <p class="testimonials__source">{{ $card['source_label'] }}</p>
                        </div>
                        @if ($card['source_url'])
                            <a class="testimonials__link" data-testimonial-link href="{{ $card['source_url'] }}"
                               aria-label="{{ __('testimonials.open_source', ['name' => $card['name'] ?? $card['role']]) }}">
                        @else
                            <span class="testimonials__source-icon" aria-hidden="true">
                        @endif
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                <path d="M13 5h6v6M19 5l-9 9M10 5H7a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3"/>
                            </svg>
                        @if ($card['source_url'])
                            </a>
                        @else
                            </span>
                        @endif
                    </header>
                    <blockquote><p>{{ $card['quote'] }}</p></blockquote>
                </article>
            @endforeach
        </div>
    </div>
</section>
