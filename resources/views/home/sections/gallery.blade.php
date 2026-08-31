<section class="home-gallery-links section" id="galeri" aria-labelledby="homepage-gallery-heading">
  <div class="container home-gallery-links__inner">
    <header class="home-gallery-links__header">
      <h2 id="homepage-gallery-heading" data-text-role="display">{{ $galleryHeading }}</h2>
    </header>

    <div class="home-gallery-links__list">
      @foreach($galleryTeasers as $teaser)
        <a class="home-gallery-links__item" href="{{ $teaser['href'] }}">
          <span>{{ $teaser['index'] }}</span>
          <strong>{{ $teaser['title'] }}</strong>
          <i aria-hidden="true">↗</i>
        </a>
      @endforeach
    </div>
  </div>
</section>
