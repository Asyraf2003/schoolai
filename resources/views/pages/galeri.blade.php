{{-- PUBLIC_GALERI_DUMMY_FINAL --}}
@extends('layouts.public', ['title' => __('pages.galeri.title'), 'description' => __('pages.galeri.description')])

@php($page = __('pages.galeri'))

@section('content')
  <section class="public-hero public-hero--galeri" aria-labelledby="galeri-title">
    <div class="container public-hero__grid">
      <div class="public-hero__copy reveal">
        <a href="{{ route('home') }}" class="link-arrow">{{ __('pages.common.back_home') }}</a>
        <span class="public-eyebrow">{{ $page['hero']['eyebrow'] }}</span>
        <h1 id="galeri-title" class="public-hero__title">{{ $page['hero']['heading'] }}</h1>
        <p class="public-hero__subtitle">{{ $page['hero']['subtitle'] }}</p>
        <a href="https://instagram.com/" class="btn btn--primary">{{ $page['hero']['instagram_cta'] }}</a>
      </div>

      <div class="gallery-hero-stack reveal" aria-hidden="true">
        <span>📸</span>
        <span>🎨</span>
        <span>📖</span>
      </div>
    </div>
  </section>

  <section class="public-section">
    <div class="container">
      <div class="public-filter-row public-filter-row--center">
        @foreach($page['filters'] as $filter)
          <button type="button" class="public-filter-chip {{ $filter['slug'] === 'all' ? 'is-active' : '' }}" data-public-gallery-filter="{{ $filter['slug'] }}">
            {{ $filter['label'] }}
          </button>
        @endforeach
      </div>

      <div class="public-gallery-grid">
        @foreach($page['items'] as $item)
          <button
            type="button"
            class="public-gallery-card reveal"
            data-public-gallery-card
            data-type="{{ $item['type'] }}"
            data-category="{{ $item['category'] }}"
            data-title="{{ $item['title'] }}"
            data-caption="{{ $item['caption'] }}"
            data-emoji="{{ $item['emoji'] }}"
            style="--gallery-g1: {{ $item['gradient'][0] }}; --gallery-g2: {{ $item['gradient'][1] }};"
          >
            <span class="public-gallery-card__badge">{{ $item['badge'] }}</span>
            <span class="public-gallery-card__emoji" aria-hidden="true">{{ $item['emoji'] }}</span>
            <span class="public-gallery-card__title">{{ $item['title'] }}</span>
            <span class="public-gallery-card__caption">{{ $item['caption'] }}</span>
          </button>
        @endforeach
      </div>
    </div>
  </section>

  <div class="public-lightbox" data-public-lightbox hidden role="dialog" aria-modal="true" aria-label="{{ __('pages.common.view_gallery') }}">
    <button type="button" class="public-lightbox__backdrop" data-public-lightbox-close aria-label="{{ __('pages.common.close') }}"></button>
    <div class="public-lightbox__panel">
      <button type="button" class="public-lightbox__close" data-public-lightbox-close>{{ __('pages.common.close') }}</button>
      <div class="public-lightbox__visual" data-public-lightbox-visual aria-hidden="true"></div>
      <h2 data-public-lightbox-title></h2>
      <p data-public-lightbox-caption></p>
    </div>
  </div>
@endsection
