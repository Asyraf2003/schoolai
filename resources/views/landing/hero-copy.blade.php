<div class="hero__copy">
    @if (! empty($slide['eyebrow']))
        <p class="hero__eyebrow">
            @if (! empty($slide['eyebrow_href']))
                <a href="{{ $slide['eyebrow_href'] }}">{{ $slide['eyebrow'] }}</a>
            @else
                {{ $slide['eyebrow'] }}
            @endif
        </p>
    @endif
    @if ($primary)<h1 class="hero__title">@else<h2 class="hero__title">@endif
        @if (! empty($slide['title_href']))
            <a href="{{ $slide['title_href'] }}" @if (! empty($slide['campaign_link_label'])) aria-label="{{ $slide['campaign_link_label'] }}" @endif>{{ $slide['title'] }}</a>
        @else
            {{ $slide['title'] }}
        @endif
    @if ($primary)</h1>@else</h2>@endif
    @if (! empty($slide['description']))
        <p class="hero__description">
            @if (! empty($slide['description_href']))
                <a href="{{ $slide['description_href'] }}">{{ $slide['description'] }}</a>
            @else
                {{ $slide['description'] }}
            @endif
        </p>
    @endif
    @if (! empty($slide['cta']['href']) && ! empty($slide['cta']['label']))
        <a class="hero__cta" href="{{ $slide['cta']['href'] }}">{{ $slide['cta']['label'] }} <span aria-hidden="true">→</span></a>
    @endif
</div>
