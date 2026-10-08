<section class="values" id="nilai" data-values aria-labelledby="values-title">
    @include('landing.values-line')
    <header class="values__header">
        <h2 class="values__heading" id="values-title" data-values-heading aria-label="{{ $values['heading'] }}">
            @foreach ($values['heading_lines'] as $line)
                <span class="values__heading-clip"><span data-values-heading-line aria-hidden="true">{{ $line }}</span></span>
            @endforeach
        </h2>
        <p class="values__intro">{{ $values['subtitle'] }}</p>
    </header>
    <div class="values__cards-track" data-values-cards-track style="--values-pattern: url('{{ config('media.static.ornaments.geometry_32') }}')">
        <div class="values__cards-stage" data-values-cards-stage aria-label="{{ $values['aria_label'] }}">
            @foreach ($values['items'] as $item)
                <article class="values__card" data-values-card
                    aria-labelledby="value-card-{{ $loop->iteration }}-title"
                    style="--values-card-accent: {{ $item['accent'] }}">
                    <div class="values__card-pose" data-values-card-pose>
                        <div class="values__card-inner" data-values-card-inner>
                            <div class="values__card-face values__card-front">
                                <span class="values__card-index">{{ $item['display_index'] }}</span>
                                <div class="values__card-copy">
                                    <p class="values__card-summary">{{ $item['summary'] }}</p>
                                    <h3 class="values__card-title" id="value-card-{{ $loop->iteration }}-title">{{ $item['title'] }}</h3>
                                    <p class="values__card-body">@foreach ($item['text_parts'] as $part)@if (! empty($part['mark']))<strong>{{ $part['text'] }}</strong>@else{{ $part['text'] }}@endif @endforeach</p>
                                </div>
                            </div>
                            <div class="values__card-face values__card-back" aria-hidden="true">
                                <span class="values__card-brand">AL MUSTAQBAL</span>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
