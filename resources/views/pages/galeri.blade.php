@php
  $page = $page ?? __('pages.galeri');
  $dbItems = $galleryItems ?? [];
  $items = ! empty($dbItems) ? $dbItems : ($page['items'] ?? []);
  $sections = $gallerySections ?? [];
@endphp

@extends('layouts.public', ['title' => $page['title'] ?? __('pages.galeri.title'), 'description' => $page['description'] ?? __('pages.galeri.description')])

@section('content')
  <style nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
    .gallery-wall-subsection__head {
      display: grid !important;
      grid-template-columns: 1fr !important;
      justify-items: start !important;
      align-items: start !important;
      gap: 10px !important;
      text-align: start !important;
    }

    .gallery-wall-subsection__head > div,
    .gallery-wall-subsection__head h2,
    .gallery-wall-subsection__head p {
      width: 100%;
      max-width: 760px;
      margin-inline: 0 !important;
      text-align: start !important;
      justify-self: start !important;
    }

    .gallery-wall-subsection__head p {
      margin-top: 0 !important;
    }
  </style>

  <section class="gallery-wall-hero" aria-labelledby="galeri-title">
    <div class="gallery-wall-bg" aria-hidden="true"></div>

    <div class="container gallery-wall-hero__inner">
      <article class="gallery-wall-hero-card reveal">
        <h1 id="galeri-title">{{ $page['hero']['heading'] ?? '' }}</h1>

        @if(! empty($page['hero']['subtitle']))
          <p>{{ $page['hero']['subtitle'] }}</p>
        @endif
      </article>

      <div class="gallery-wall-flash reveal reveal--delay-1" aria-hidden="true">
        @foreach(($page['hero']['cards'] ?? []) as $card)
          <span style="--flash-g1: {{ $card['gradient'][0] ?? '#DCF1F7' }}; --flash-g2: {{ $card['gradient'][1] ?? '#FFC93C' }};">
            <strong>{{ $card['emoji'] ?? '📸' }}</strong>
            <small>{{ $card['label'] ?? '' }}</small>
          </span>
        @endforeach
      </div>
    </div>
  </section>

  <section class="gallery-wall-section" aria-labelledby="gallery-wall-title">
    <div class="container">
      <header class="gallery-wall-head reveal">
        <h2 id="gallery-wall-title">{{ $page['wall']['title'] ?? '' }}</h2>
      </header>

      <div class="gallery-wall-grid" data-gallery-wall>
        @foreach($items as $item)
          @include('pages.partials.gallery-wall-card', ['item' => $item])
        @endforeach
      </div>

      @foreach($sections as $section)
        <section class="gallery-wall-subsection reveal" aria-labelledby="gallery-section-{{ $loop->index }}">
          <header class="gallery-wall-subsection__head">
            <div>
              <h2 id="gallery-section-{{ $loop->index }}">{{ $section['title'] }}</h2>
            </div>

            @if(! empty($section['description']))
              <p>{{ $section['description'] }}</p>
            @endif
          </header>

          <div class="gallery-wall-grid gallery-wall-grid--subsection">
            @foreach($section['items'] as $item)
              @include('pages.partials.gallery-wall-card', ['item' => $item])
            @endforeach
          </div>
        </section>
      @endforeach
    </div>
  </section>

  <div class="gallery-wall-lightbox" data-gallery-wall-lightbox data-gallery-wall-video-title="{{ __('pages.common.gallery_video_title') }}" hidden role="dialog" aria-modal="true" aria-label="{{ __('pages.common.view_gallery') }}">
    <button type="button" class="gallery-wall-lightbox__backdrop" data-gallery-wall-lightbox-close aria-label="{{ __('pages.common.close') }}"></button>

    <article class="gallery-wall-lightbox__panel">
      <button type="button" class="gallery-wall-lightbox__close" data-gallery-wall-lightbox-close>{{ __('pages.common.close') }}</button>
      <div class="gallery-wall-lightbox__media" data-gallery-wall-lightbox-media></div>
    </article>
  </div>
@endsection
