@extends('layouts.public', ['title' => $page['title'] ?? __('pages.galeri.title'), 'description' => $page['description'] ?? __('pages.galeri.description')])

@push('head')
  @vite('resources/css/surfaces/public/gallery.css')
@endpush

@section('content')
  <div class="gallery-grid-demo" data-gallery-grid-demo>
    <header class="gallery-grid-demo__header">
      <nav class="gallery-grid-demo__demos" aria-label="{{ $page['wall']['title'] ?? __('pages.galeri.title') }}">
        @foreach($galleryCategories as $categoryIndex => $category)
          <a
            href="#{{ $category['anchor'] }}"
            class="{{ $categoryIndex === 0 ? 'current-demo' : '' }}"
            data-gallery-category-target="{{ $category['anchor'] }}"
          >
            {{ $category['title'] !== '' ? $category['title'] : 'Index' }}
          </a>
        @endforeach
      </nav>
    </header>

    <div class="gallery-grid-demo__panels">
      @foreach($galleryCategories as $categoryIndex => $category)
        <section
          id="{{ $category['anchor'] }}"
          class="gallery-grid-demo__panel{{ $categoryIndex === 0 ? ' is-active' : '' }}"
          data-gallery-category-panel
          data-gallery-title="{{ $category['title'] }}"
          {{ $categoryIndex !== 0 ? 'hidden' : '' }}
        >
          <ul
            id="gallery-grid-{{ $categoryIndex }}"
            class="gallery-grid effect-{{ ($categoryIndex % 8) + 1 }}"
            data-gallery-grid
          >
            @foreach($category['items'] as $item)
              <li>
                <button
                  type="button"
                  class="gallery-grid__media"
                  data-gallery-modal-open
                  data-gallery-title="{{ $item['title'] }}"
                  data-gallery-media-url="{{ $item['mediaUrl'] }}"
                  data-gallery-thumbnail-url="{{ $item['thumbnailUrl'] }}"
                  data-gallery-is-video="{{ $item['isVideo'] ? '1' : '0' }}"
                  data-gallery-is-direct-video="{{ $item['isDirectVideo'] ? '1' : '0' }}"
                  aria-label="{{ $item['title'] }}"
                >
                  @if($item['isDirectVideo'] && $item['mediaUrl'] !== '')
                    <video src="{{ $item['mediaUrl'] }}" muted playsinline webkit-playsinline preload="metadata" aria-hidden="true"></video>
                    <span class="gallery-grid__play" aria-hidden="true">▶</span>
                  @endif

                  @if(! $item['isDirectVideo'] && $item['thumbnailUrl'] !== '')
                    <img src="{{ $item['thumbnailUrl'] }}" alt="{{ $item['title'] }}" decoding="async">
                    @if($item['isVideo'])
                      <span class="gallery-grid__play" aria-hidden="true">▶</span>
                    @endif
                  @endif

                  @if(! $item['isDirectVideo'] && $item['thumbnailUrl'] === '')
                    <span class="gallery-grid__fallback" aria-hidden="true">{{ $item['emoji'] }}</span>
                    <span class="gallery-grid__play" aria-hidden="true">▶</span>
                  @endif
                </button>
              </li>
            @endforeach
          </ul>
        </section>
      @endforeach
    </div>
  </div>

  <div class="gallery-grid-modal" data-gallery-modal hidden role="dialog" aria-modal="true" aria-labelledby="gallery-grid-modal-title">
    <button class="gallery-grid-modal__backdrop" type="button" data-gallery-modal-close aria-label="{{ __('pages.common.close') }}"></button>
    <div class="gallery-grid-modal__frame">
      <h2 id="gallery-grid-modal-title" data-gallery-modal-title></h2>
      <button class="gallery-grid-modal__close" type="button" data-gallery-modal-close aria-label="{{ __('pages.common.close') }}">×</button>
      <div class="gallery-grid-modal__media" data-gallery-modal-media></div>
    </div>
  </div>
@endsection
