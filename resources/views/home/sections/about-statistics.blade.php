@php
  $aboutLocale = app()->getLocale();
  $aboutTitle = trim(
      __('home.about_stats_story.headline_line_one').' '.__('home.about_stats_story.headline_line_two')
  );
  $aboutLayers = [9, 10, 11, 12];
@endphp

<section
  class="home-about-scroll"
  id="tentang"
  aria-labelledby="about-scroll-title"
  data-story-root
  data-story-kind="about"
  data-story-locale="{{ $aboutLocale }}"
>
  <div class="home-about-scroll__stage" data-story-stage>
    <div class="home-about-scroll__layers" aria-hidden="true">
      @foreach ($aboutLayers as $asset)
        <img
          class="home-about-scroll__layer home-about-scroll__layer--{{ $loop->iteration }}"
          data-story-layer
          data-story-depth="{{ $loop->iteration }}"
          src="{{ asset('media/home/'.$asset.'.png') }}"
          alt=""
          width="1600"
          height="2000"
          loading="lazy"
          decoding="async"
        />
      @endforeach
    </div>

    <div class="home-about-scroll__content">
      <p class="home-about-scroll__eyebrow">{{ __('home.about_stats_story.board_title') }}</p>

      <h2
        class="home-about-scroll__title"
        id="about-scroll-title"
        data-story-text
        data-story-effect="stretch"
      >{{ $aboutTitle }}</h2>

      <p class="home-about-scroll__description">
        {{ __('home.about_stats_story.description') }}
      </p>
    </div>

    <p class="home-about-scroll__scroll-note" aria-hidden="true">Scroll</p>
  </div>
</section>
