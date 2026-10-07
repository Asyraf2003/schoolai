<section class="program" id="program" data-program aria-labelledby="program-title" data-entry-texture="{{ $program['entry_texture'] }}">
    <div class="program__entry" data-program-story-start aria-hidden="true"></div>
    <div class="program__type-home" data-program-type-home aria-hidden="true">
        <div class="program__type-viewport" data-program-type-viewport aria-hidden="true">
            <div class="program__type" data-program-type>
                @foreach ($program['kinetic_lines'] as $line)<div class="program__type-line" data-program-type-line>{{ $line }}</div>@endforeach
            </div>
        </div>
    </div>
    <header class="program__header" data-program-header>
        <h2 class="program__heading" id="program-title" data-program-heading aria-label="{{ $program['section_label'] }}">
            @foreach ($program['heading_lines'] as $line)
                <span class="program__heading-clip"><span data-program-heading-line aria-hidden="true">{{ $line }}</span></span>
            @endforeach
        </h2>
    </header>
    <div class="program__cards" data-program-cards>
        @foreach ($program['items'] as $item)
            <details class="program__card" id="{{ $item['id'] }}" data-program-card>
                <summary class="program__trigger" data-program-open aria-controls="{{ $item['id'] }}-detail" aria-label="{{ __('home_program.open_item', ['program' => $item['title']]) }}">
                    <span class="program__card-reveal">
                        <span class="program__image-wrap">
                            <img src="{{ $item['media']['url'] }}" alt="" width="1800" height="1200" loading="lazy" decoding="async" style="object-position: {{ $item['media']['position'] }}">
                        </span>
                        <span class="program__caption">
                            <span class="program__eyebrow">{{ $item['eyebrow'] }}</span>
                            <strong class="program__name">{{ $item['title'] }}</strong>
                            <span class="program__summary">{{ $item['summary'] }}</span>
                        </span>
                    </span>
                </summary>
                <article class="program__detail" id="{{ $item['id'] }}-detail" data-program-detail aria-labelledby="{{ $item['id'] }}-title">
                    <div class="program__detail-copy">
                        <button class="program__back" type="button" data-program-back hidden>
                            <span class="program__back-mark" aria-hidden="true">&lt;&lt;&lt;</span><span>{{ $program['back'] }}</span>
                        </button>
                        <h3 class="program__detail-title" id="{{ $item['id'] }}-title" data-title-scale="{{ $item['title_scale'] }}">{{ $item['title'] }}</h3>
                        <p class="program__description">{{ $item['description'] }}</p>
                    </div>
                    <div class="program__detail-media">
                        <div class="program__detail-image-wrap" data-program-image-wrap>
                            <img data-program-image data-src="{{ $item['media']['url'] }}" alt="" width="1800" height="1200" decoding="async" style="object-position: {{ $item['media']['position'] }}">
                        </div>
                    </div>
                </article>
            </details>
        @endforeach
    </div>
    <dialog class="program__dialog" data-program-dialog aria-label="{{ $program['section_label'] }}" tabindex="-1">
        <div class="program__detail-stage" data-program-detail-stage></div>
    </dialog>
    <div class="program__end" data-program-story-end aria-hidden="true"></div>
    <div class="program__values-seam" data-program-values-seam aria-hidden="true">
        <span data-values-entry-anchor></span>
    </div>
</section>
