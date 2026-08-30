@extends('layouts.public', ['title' => $page['title'] ?? __('pages.galeri.title'), 'description' => $page['description'] ?? __('pages.galeri.description')])

@section('content')
  @php
    $galleryCategories = [];
    $primaryItems = is_array($items ?? null) ? $items : [];

    if ($primaryItems !== []) {
        $galleryCategories[] = [
            'title' => (string) ($page['wall']['title'] ?? $page['hero']['heading'] ?? __('pages.galeri.title')),
            'description' => (string) ($page['hero']['subtitle'] ?? ''),
            'items' => $primaryItems,
        ];
    }

    foreach ((is_array($sections ?? null) ? $sections : []) as $section) {
        $sectionItems = is_array($section['items'] ?? null) ? $section['items'] : [];

        if ($sectionItems === []) {
            continue;
        }

        $galleryCategories[] = [
            'title' => (string) ($section['title'] ?? __('pages.galeri.title')),
            'description' => (string) ($section['description'] ?? ''),
            'items' => $sectionItems,
        ];
    }
  @endphp

  <div class="gallery-perspective" data-gallery-perspective>
    <nav class="gallery-perspective__nav" data-gallery-category-nav aria-label="{{ $page['wall']['title'] ?? __('pages.galeri.title') }}">
      <div class="gallery-perspective__nav-inner">
        <p class="gallery-perspective__nav-kicker">{{ $page['hero']['heading'] ?? __('pages.galeri.title') }}</p>
        <div class="gallery-perspective__nav-list">
          @foreach($galleryCategories as $categoryIndex => $category)
            <button
              type="button"
              class="gallery-perspective__nav-item{{ $categoryIndex === 0 ? ' is-active' : '' }}"
              data-gallery-category-target="gallery-category-{{ $categoryIndex }}"
              aria-controls="gallery-category-{{ $categoryIndex }}"
              aria-pressed="{{ $categoryIndex === 0 ? 'true' : 'false' }}"
              style="--gallery-nav-order: {{ $categoryIndex }}"
            >
              <span>{{ str_pad((string) ($categoryIndex + 1), 2, '0', STR_PAD_LEFT) }}</span>
              <strong>{{ $category['title'] }}</strong>
            </button>
          @endforeach
        </div>
      </div>
    </nav>

    <div class="gallery-perspective__stage" data-gallery-perspective-stage>
      <section class="gallery-codrops" aria-labelledby="galeri-title">
        <header class="gallery-codrops__hero">
          <div class="gallery-codrops__hero-copy">
            <p class="gallery-codrops__eyebrow">{{ $page['title'] ?? __('pages.galeri.title') }}</p>
            <h1 id="galeri-title">{{ $page['hero']['heading'] ?? '' }}</h1>

            @if(! empty($page['hero']['subtitle']))
              <p>{{ $page['hero']['subtitle'] }}</p>
            @endif
          </div>

          @if($galleryCategories !== [])
            <button
              type="button"
              class="gallery-codrops__menu-trigger"
              data-gallery-menu-trigger
              aria-expanded="false"
              aria-controls="gallery-category-navigation"
            >
              <span class="gallery-codrops__menu-icon" aria-hidden="true"><i></i><i></i><i></i></span>
              <span class="gallery-codrops__menu-copy">
                <small>{{ count($galleryCategories) }} kategori</small>
                <strong data-gallery-active-category>{{ $galleryCategories[0]['title'] }}</strong>
              </span>
            </button>
          @endif
        </header>

        <div id="gallery-category-navigation" class="gallery-codrops__panels" data-gallery-category-panels>
          @forelse($galleryCategories as $categoryIndex => $category)
            @php
              $effectNumber = ($categoryIndex % 8) + 1;
              $panelId = 'gallery-category-'.$categoryIndex;
              $ratios = ['wide', 'portrait', 'square', 'landscape', 'tall', 'cinema'];
            @endphp

            <section
              id="{{ $panelId }}"
              class="gallery-codrops__panel{{ $categoryIndex === 0 ? ' is-active' : '' }}"
              data-gallery-category-panel
              data-gallery-effect="{{ $effectNumber }}"
              @if($categoryIndex !== 0) hidden @endif
              aria-labelledby="{{ $panelId }}-title"
            >
              <header class="gallery-codrops__panel-head">
                <div>
                  <span>{{ str_pad((string) ($categoryIndex + 1), 2, '0', STR_PAD_LEFT) }}</span>
                  <h2 id="{{ $panelId }}-title">{{ $category['title'] }}</h2>
                </div>

                @if($category['description'] !== '')
                  <p>{{ $category['description'] }}</p>
                @endif
              </header>

              <div class="gallery-codrops-grid effect-{{ $effectNumber }}" data-gallery-grid>
                @foreach($category['items'] as $item)
                  @php($ratio = $ratios[$loop->index % count($ratios)])
                  <div
                    class="gallery-codrops-grid__item gallery-codrops-grid__item--{{ $ratio }}"
                    data-gallery-load-item
                    style="--gallery-item-order: {{ $loop->index }}"
                  >
                    @include('pages.partials.gallery-wall-card', ['item' => $item])
                  </div>
                @endforeach
              </div>
            </section>
          @empty
            <section class="gallery-codrops__empty">
              <h2>{{ $page['wall']['title'] ?? __('pages.galeri.title') }}</h2>
              <p>{{ $page['description'] ?? '' }}</p>
            </section>
          @endforelse
        </div>
      </section>
    </div>
  </div>

  <div class="gallery-wall-lightbox" data-gallery-wall-lightbox data-gallery-wall-video-title="{{ __('pages.common.gallery_video_title') }}" hidden role="dialog" aria-modal="true" aria-label="{{ __('pages.common.view_gallery') }}">
    <button type="button" class="gallery-wall-lightbox__backdrop" data-gallery-wall-lightbox-close aria-label="{{ __('pages.common.close') }}"></button>

    <article class="gallery-wall-lightbox__panel">
      <button type="button" class="gallery-wall-lightbox__close" data-gallery-wall-lightbox-close>{{ __('pages.common.close') }}</button>
      <div class="gallery-wall-lightbox__media" data-gallery-wall-lightbox-media></div>
    </article>
  </div>
@endsection
