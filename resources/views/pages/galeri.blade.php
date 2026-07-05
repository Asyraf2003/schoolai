{{-- PUBLIC_GALERI_HUB_DUMMY_FINAL --}}
@extends('layouts.public', ['title' => __('pages.galeri.title'), 'description' => __('pages.galeri.description')])

@php
  $page = __('pages.galeri');
  $filters = $page['filters'] ?? [];
  $sections = $page['sections'] ?? [];
@endphp

@section('content')
  <section class="public-hero public-hero--galeri gallery-hub-hero" aria-labelledby="galeri-title">
    <div class="container gallery-hub-hero__grid">
      <div class="gallery-hub-hero__copy reveal">
        <a href="{{ route('home') }}" class="link-arrow">{{ __('pages.common.back_home') }}</a>
        <span class="public-eyebrow">{{ $page['hero']['eyebrow'] ?? '' }}</span>
        <h1 id="galeri-title" class="public-hero__title">{{ $page['hero']['heading'] ?? '' }}</h1>
        <p class="public-hero__subtitle">{{ $page['hero']['subtitle'] ?? '' }}</p>

        <div class="gallery-hub-hero__actions">
          <a href="https://instagram.com/" class="btn btn--primary">{{ $page['hero']['instagram_cta'] ?? 'Instagram' }}</a>
          <a href="#galeri-hub-content" class="btn btn--ghost">{{ $page['hero']['explore_cta'] ?? 'Lihat Galeri' }}</a>
        </div>
      </div>

      <div class="gallery-hub-orbit reveal reveal--delay-1" aria-hidden="true">
        @foreach(($page['hero']['bubbles'] ?? []) as $bubble)
          <span class="gallery-hub-orbit__item" style="--orbit-g1: {{ $bubble['gradient'][0] ?? '#DCF1F7' }}; --orbit-g2: {{ $bubble['gradient'][1] ?? '#FFC93C' }};">
            <strong>{{ $bubble['emoji'] ?? '📸' }}</strong>
            <small>{{ $bubble['label'] ?? '' }}</small>
          </span>
        @endforeach
      </div>
    </div>
  </section>

  <section class="gallery-hub-page" id="galeri-hub-content" data-gallery-hub>
    <div class="container">
      <div class="gallery-hub-toolbar reveal">
        <div>
          <span class="public-eyebrow">{{ $page['toolbar']['eyebrow'] ?? '' }}</span>
          <h2>{{ $page['toolbar']['title'] ?? '' }}</h2>
        </div>

        <div class="public-filter-row gallery-hub-filter-row" aria-label="{{ $page['toolbar']['filter_label'] ?? 'Filter galeri' }}">
          @foreach($filters as $filter)
            <button
              type="button"
              class="public-filter-chip {{ ($filter['slug'] ?? '') === 'all' ? 'is-active' : '' }}"
              data-gallery-hub-filter="{{ $filter['slug'] ?? 'all' }}"
              aria-pressed="{{ ($filter['slug'] ?? '') === 'all' ? 'true' : 'false' }}"
            >
              {{ $filter['label'] ?? '' }}
            </button>
          @endforeach
        </div>
      </div>

      <div class="gallery-hub-sections">
        @foreach($sections as $section)
          @php
            $sectionSlug = $section['slug'] ?? 'section';
            $layout = $section['layout'] ?? 'cards';
            $items = array_slice($section['items'] ?? [], 0, 10);
          @endphp

          <section
            class="gallery-hub-section gallery-hub-section--{{ $layout }} reveal"
            data-gallery-hub-section="{{ $sectionSlug }}"
            aria-labelledby="gallery-section-{{ $sectionSlug }}"
          >
            <div class="gallery-hub-section__head">
              <div>
                <span class="gallery-hub-section__kicker">{{ $section['kicker'] ?? '' }}</span>
                <h3 id="gallery-section-{{ $sectionSlug }}">{{ $section['title'] ?? '' }}</h3>
              </div>

              @if(! empty($section['description']))
                <p>{{ $section['description'] }}</p>
              @endif
            </div>

            <div class="gallery-hub-track gallery-hub-track--{{ $layout }}">
              @foreach($items as $item)
                @php
                  $type = $item['type'] ?? 'photo';
                  $mediaUrl = $item['media_url'] ?? null;
                  $isVideo = $type === 'video';
                  $title = $item['title'] ?? '';
                  $caption = $item['caption'] ?? null;
                  $emoji = $item['emoji'] ?? '📸';
                  $badge = $item['badge'] ?? ($isVideo ? 'Video' : 'Foto');
                  $meta = $item['meta'] ?? null;
                  $gradient = $item['gradient'] ?? ['#DCF1F7', '#FFC93C'];
                @endphp

                <button
                  type="button"
                  class="gallery-hub-card gallery-hub-card--{{ $layout }}"
                  data-gallery-hub-card
                  data-gallery-section="{{ $sectionSlug }}"
                  data-gallery-type="{{ $type }}"
                  data-gallery-title="{{ $title }}"
                  data-gallery-caption="{{ $caption ?? '' }}"
                  data-gallery-emoji="{{ $emoji }}"
                  data-gallery-badge="{{ $badge }}"
                  data-gallery-media-url="{{ $mediaUrl ?? '' }}"
                  data-gallery-is-video="{{ $isVideo ? '1' : '0' }}"
                  style="--gallery-g1: {{ $gradient[0] ?? '#DCF1F7' }}; --gallery-g2: {{ $gradient[1] ?? '#FFC93C' }};"
                >
                  <span class="gallery-hub-card__media">
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
                      <span class="gallery-hub-card__emoji" aria-hidden="true">{{ $emoji }}</span>
                    @endif
                  </span>

                  <span class="gallery-hub-card__body">
                    <span class="gallery-hub-card__topline">
                      <span class="gallery-hub-card__badge">{{ $badge }}</span>
                      @if($meta)
                        <span class="gallery-hub-card__meta">{{ $meta }}</span>
                      @endif
                    </span>

                    @if($title)
                      <strong>{{ $title }}</strong>
                    @endif

                    @if($caption)
                      <small>{{ $caption }}</small>
                    @endif
                  </span>
                </button>
              @endforeach
            </div>
          </section>
        @endforeach
      </div>
    </div>
  </section>

  <div class="gallery-hub-lightbox" data-gallery-hub-lightbox hidden role="dialog" aria-modal="true" aria-label="{{ __('pages.common.view_gallery') }}">
    <button type="button" class="gallery-hub-lightbox__backdrop" data-gallery-hub-lightbox-close aria-label="{{ __('pages.common.close') }}"></button>

    <article class="gallery-hub-lightbox__panel">
      <button type="button" class="gallery-hub-lightbox__close" data-gallery-hub-lightbox-close>{{ __('pages.common.close') }}</button>
      <div class="gallery-hub-lightbox__media" data-gallery-hub-lightbox-media></div>
      <span class="gallery-hub-lightbox__badge" data-gallery-hub-lightbox-badge></span>
      <h2 data-gallery-hub-lightbox-title></h2>
      <p data-gallery-hub-lightbox-caption></p>
    </article>
  </div>
@endsection
