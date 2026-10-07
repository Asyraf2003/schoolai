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
    @if (! empty($slide['campaign_link_label']) && ! empty($slide['title_href']))
        <a class="hero__copy-action" href="{{ $slide['title_href'] }}" aria-label="{{ $slide['campaign_link_label'] }}">
    @else
        <div class="hero__copy-action">
    @endif
    @if ($primary)<h1 class="hero__title">@else<h2 class="hero__title">@endif
        @if (! empty($slide['title_href']) && empty($slide['campaign_link_label']))
            <a href="{{ $slide['title_href'] }}">{{ $slide['title'] }}</a>
        @else
            {{ $slide['title'] }}
        @endif
    @if ($primary)</h1>@else</h2>@endif
    @if (! empty($slide['description']))
        <p class="hero__description">
            @if (! empty($slide['description_href']) && empty($slide['campaign_link_label']))
                <a href="{{ $slide['description_href'] }}">{{ $slide['description'] }}</a>
            @else
                {{ $slide['description'] }}
            @endif
        </p>
    @endif
    @if (! empty($slide['cta']['href']) && ! empty($slide['cta']['label']))
        @if (! empty($slide['campaign_link_label']))
            <span class="hero__cta">
        @else
            <a class="hero__cta" href="{{ $slide['cta']['href'] }}">
        @endif
            {{ $slide['cta']['label'] }} <span aria-hidden="true">→</span>
        @if (! empty($slide['campaign_link_label']))</span>@else</a>@endif
    @endif
    @if (! empty($slide['campaign_link_label']) && ! empty($slide['title_href']))</a>@else</div>@endif
</div>
