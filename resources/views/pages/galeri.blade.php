{{-- PUBLIC_GALERI_WALL_FINAL --}}
@php
  $page = $page ?? __('pages.galeri');
  $dbItems = $galleryItems ?? [];
  $items = ! empty($dbItems) ? $dbItems : ($page['items'] ?? []);
@endphp

@extends('layouts.public', ['title' => $page['title'] ?? __('pages.galeri.title'), 'description' => $page['description'] ?? __('pages.galeri.description')])

@section('content')
  <section class="gallery-wall-hero" aria-labelledby="galeri-title">
    <div class="gallery-wall-bg" aria-hidden="true"></div>

    <div class="container gallery-wall-hero__inner">
      <article class="gallery-wall-hero-card reveal">
        <a href="{{ route('home') }}" class="link-arrow">{{ __('pages.common.back_home') }}</a>
        <span class="public-eyebrow">{{ $page['hero']['eyebrow'] ?? '' }}</span>
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
        <span class="public-eyebrow">{{ $page['wall']['eyebrow'] ?? '' }}</span>
        <h2 id="gallery-wall-title">{{ $page['wall']['title'] ?? '' }}</h2>
      </header>

      <div class="gallery-wall-grid" data-gallery-wall>
        @foreach($items as $item)
          @php
            $type = $item['type'] ?? 'photo';
            $isVideo = $type === 'video';
            $title = $item['title'] ?? '';
            $mediaUrl = $item['media_url'] ?? null;
            $emoji = $item['emoji'] ?? ($isVideo ? '▶️' : '📸');
            $badge = $item['badge'] ?? ($isVideo ? 'Video' : 'Foto');
            $gradient = $item['gradient'] ?? ['#DCF1F7', '#FFC93C'];
          @endphp

          <article
            class="gallery-wall-card reveal"
            tabindex="0"
            role="button"
            data-gallery-wall-card
            data-gallery-title="{{ $title }}"
            data-gallery-media-url="{{ $mediaUrl ?? '' }}"
            data-gallery-is-video="{{ $isVideo ? '1' : '0' }}"
            data-gallery-emoji="{{ $emoji }}"
            data-gallery-badge="{{ $badge }}"
            style="--gallery-g1: {{ $gradient[0] ?? '#DCF1F7' }}; --gallery-g2: {{ $gradient[1] ?? '#FFC93C' }};"
          >
            <div class="gallery-wall-card__media">
              @if($mediaUrl && $isVideo)
                <iframe
                  src="{{ $mediaUrl }}"
                  title="{{ $title }}"
                  loading="lazy"
                  allow="fullscreen; picture-in-picture"
                  allowfullscreen
                  referrerpolicy="strict-origin-when-cross-origin"
                ></iframe>
              @elseif($mediaUrl)
                <img src="{{ $mediaUrl }}" alt="{{ $title }}" loading="lazy">
              @else
                <span aria-hidden="true">{{ $emoji }}</span>
              @endif
            </div>

            <div class="gallery-wall-card__caption">
              <span>{{ $badge }}</span>
              <h3>{{ $title }}</h3>
            </div>
          </article>
        @endforeach
      </div>
    </div>
  </section>

  <div class="gallery-wall-lightbox" data-gallery-wall-lightbox hidden role="dialog" aria-modal="true" aria-label="{{ __('pages.common.view_gallery') }}">
    <button type="button" class="gallery-wall-lightbox__backdrop" data-gallery-wall-lightbox-close aria-label="{{ __('pages.common.close') }}"></button>

    <article class="gallery-wall-lightbox__panel">
      <button type="button" class="gallery-wall-lightbox__close" data-gallery-wall-lightbox-close>{{ __('pages.common.close') }}</button>
      <div class="gallery-wall-lightbox__media" data-gallery-wall-lightbox-media></div>
      <span class="gallery-wall-lightbox__badge" data-gallery-wall-lightbox-badge></span>
      <h2 data-gallery-wall-lightbox-title></h2>
    </article>
  </div>
@endsection
