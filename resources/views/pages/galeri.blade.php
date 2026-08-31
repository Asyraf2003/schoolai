@extends('layouts.public', ['title' => $page['title'] ?? __('pages.galeri.title'), 'description' => $page['description'] ?? __('pages.galeri.description')])

@php
  $galleryCategories = [];
  $primaryItems = is_array($items ?? null) ? $items : [];

  $galleryCategories[] = [
      'anchor' => 'gallery-main',
      'title' => (string) ($page['wall']['title'] ?? $page['title'] ?? 'Index'),
      'description' => (string) ($page['hero']['subtitle'] ?? ''),
      'items' => $primaryItems,
  ];

  foreach ((is_array($sections ?? null) ? $sections : []) as $section) {
      $sectionItems = is_array($section['items'] ?? null) ? $section['items'] : [];

      if ($sectionItems === []) {
          continue;
      }

      $galleryCategories[] = [
          'anchor' => (string) ($section['anchor'] ?? ('gallery-section-'.($section['id'] ?? count($galleryCategories)))),
          'title' => trim((string) ($section['title'] ?? '')) !== '' ? (string) $section['title'] : 'Index',
          'description' => (string) ($section['description'] ?? ''),
          'items' => $sectionItems,
      ];
  }
@endphp

@section('content')
  <div class="gallery-grid-demo" data-gallery-grid-demo>
    <header class="gallery-grid-demo__header">
      <h1 id="galeri-title">
        {{ $page['title'] ?? __('pages.galeri.title') }}
        <span data-gallery-active-title>{{ $galleryCategories[0]['title'] ?? 'Index' }}</span>
      </h1>

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
              @php
                $type = (string) ($item['type'] ?? 'photo');
                $isVideo = $type === 'video';
                $isDirectVideo = $isVideo && (bool) ($item['is_direct_video'] ?? false);
                $title = trim((string) ($item['title'] ?? $item['label'] ?? ''));
                $mediaUrl = (string) ($item['media_url'] ?? '');
                $thumbnailUrl = (string) ($item['thumbnail_url'] ?? '');
                $displayUrl = $thumbnailUrl !== '' ? $thumbnailUrl : $mediaUrl;

                if (!$isVideo && $displayUrl === '') {
                    $displayUrl = (string) config('media.static.hero_school');
                }
              @endphp

              <li>
                <button
                  type="button"
                  class="gallery-grid__media"
                  data-gallery-modal-open
                  data-gallery-title="{{ $title }}"
                  data-gallery-media-url="{{ $mediaUrl }}"
                  data-gallery-thumbnail-url="{{ $displayUrl }}"
                  data-gallery-is-video="{{ $isVideo ? '1' : '0' }}"
                  data-gallery-is-direct-video="{{ $isDirectVideo ? '1' : '0' }}"
                  aria-label="{{ $title }}"
                >
                  <?php if ($isDirectVideo && $mediaUrl !== ''): ?>
                    <video src="{{ $mediaUrl }}" muted playsinline webkit-playsinline preload="metadata" aria-hidden="true"></video>
                    <span class="gallery-grid__play" aria-hidden="true">▶</span>
                  <?php elseif ($displayUrl !== ''): ?>
                    <img src="{{ $displayUrl }}" alt="{{ $title }}" decoding="async">
                    <?php if ($isVideo): ?>
                      <span class="gallery-grid__play" aria-hidden="true">▶</span>
                    <?php endif; ?>
                  <?php else: ?>
                    <span class="gallery-grid__fallback" aria-hidden="true">{{ $item['emoji'] ?? '▶' }}</span>
                    <span class="gallery-grid__play" aria-hidden="true">▶</span>
                  <?php endif; ?>
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
