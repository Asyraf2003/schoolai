<{{ $headingTag }}
  class="hero-cinema__title"
  data-hero-title-glow
  data-text-role="display"
>
  @if (! empty($slide['title_href']))
    <a href="{{ $slide['title_href'] }}" class="hero-cinema__title-link">
      <span data-hero-title-base>{{ $slide['title'] }}</span>
    </a>
  @else
    <span data-hero-title-base>{{ $slide['title'] }}</span>
  @endif
</{{ $headingTag }}>
