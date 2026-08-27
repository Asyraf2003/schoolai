@if (! empty($slide['eyebrow']))
  <p class="hero-cinema__eyebrow">{{ $slide['eyebrow'] }}</p>
@endif

<{{ $headingTag }} class="hero-cinema__title" data-text-role="display">
  @if (! empty($slide['title_href']))
    <a
      href="{{ $slide['title_href'] }}"
      class="hero-cinema__title-link"
      @if (! empty($slide['campaign_link_label'])) aria-label="{{ $slide['campaign_link_label'] }}" @endif
    >{{ $slide['title'] }}</a>
  @else
    {{ $slide['title'] }}
  @endif
</{{ $headingTag }}>

@if (! empty($slide['description']))
  <p class="hero-cinema__description">{{ $slide['description'] }}</p>
@endif

@if (! empty(data_get($slide, 'cta.href')) && ! empty(data_get($slide, 'cta.label')))
  <a class="hero-cinema__cta" href="{{ data_get($slide, 'cta.href') }}">
    <span>{{ data_get($slide, 'cta.label') }}</span>
    <svg viewBox="0 0 24 24" aria-hidden="true">
      <path d="M5 12h14M13 6l6 6-6 6" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
    </svg>
  </a>
@endif
