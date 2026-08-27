<{{ $headingTag }}
  class="hero-cinema__title"
  data-text-role="display"
>
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
