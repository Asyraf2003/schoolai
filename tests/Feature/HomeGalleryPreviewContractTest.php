<?php

use App\Models\GalleryItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('keeps homepage Gallery motion previews direct, lazy, muted, and iframe free', function (): void {
    $homeQuery = file_get_contents(app_path(
        'Http/Controllers/Concerns/BuildsHomeArticlesAndGallery.php'
    ));
    $normalizer = file_get_contents(app_path(
        'Http/Controllers/Concerns/NormalizesHomeGallery.php'
    ));
    $storyBlade = file_get_contents(resource_path(
        'views/home/sections/gallery-depth.blade.php'
    ));
    $controller = file_get_contents(resource_path(
        'js/pages/welcome-depth-gallery.js'
    ));
    $galleryPage = file_get_contents(resource_path(
        'views/pages/galeri.blade.php'
    ));
    $galleryWall = file_get_contents(resource_path(
        'js/pages/welcome/gallery-wall.js'
    ));
    $seeder = file_get_contents(database_path(
        'seeders/Concerns/SeedsGalleryItems.php'
    ));

    expect($homeQuery)
        ->toContain("'/gallery/media/%'")
        ->toContain("->where('type', 'video')")
        ->toContain("->where('type', 'photo')")
        ->toContain("$normalized['is_direct_video']")
        ->and($normalizer)
        ->toContain('trustedGalleryDirectVideoUrl')
        ->toContain("str_starts_with($key, 'gallery/media/')")
        ->toContain("str_ends_with(strtolower($key), '.mp4')")
        ->toContain('is_direct_video')
        ->toContain('isDummyGalleryCaption')
        ->toContain("__('home.galeri.section_subtitle')")
        ->and($storyBlade)
        ->toContain('data-depth-gallery-end-link')
        ->toContain('data-gallery-story-item')
        ->toContain('data-gallery-video-preview')
        ->toContain('data-gallery-video-src')
        ->toContain('muted')
        ->toContain('loop')
        ->toContain('playsinline')
        ->toContain('preload="none"')
        ->not->toContain('<iframe')
        ->not->toContain('gallery-story__play')
        ->and($controller)
        ->toContain("querySelectorAll('[data-gallery-video-preview]')")
        ->toContain("rootMargin: '180px 0px'")
        ->toContain('preview.src = source')
        ->toContain('preview.pause()')
        ->toContain('paintItems')
        ->not->toContain('createGalleryStoryLightbox')
        ->not->toContain('openStoryMedia')
        ->and($galleryPage)
        ->toContain('data-gallery-wall-lightbox')
        ->and($galleryWall)
        ->toContain("if (mediaUrl && isVideo)")
        ->toContain("document.createElement('iframe')")
        ->and($seeder)
        ->not->toContain('Dokumentasi dummy untuk pratinjau galeri sekolah.')
        ->not->toContain('Sample documentation for the school gallery preview.')
        ->not->toContain('محتوى تجريبي لمعاينة معرض المدرسة.');

    expect(file_exists(resource_path(
        'js/pages/welcome/gallery-story-lightbox.js'
    )))->toBeFalse();
});

it('renders only owned Gallery MP4 video rows as homepage motion thumbnails', function (): void {
    GalleryItem::query()->update(['show_on_homepage' => false]);

    $directUrl = rtrim((string) config('media.public_url'), '/')
        .'/gallery/media/manual/gallery-preview-v1.mp4';
    $providerUrl = 'https://www.youtube.com/embed/kb1dXcf3QQs';

    foreach ([
        ['url' => $directUrl, 'title' => 'Direct Gallery Motion', 'order' => 1],
        ['url' => $providerUrl, 'title' => 'Provider Gallery Video', 'order' => 2],
    ] as $video) {
        $item = GalleryItem::query()->create([
            'title' => $video['title'],
            'title_id' => $video['title'],
            'type' => 'video',
            'category' => 'Kegiatan',
            'category_id' => 'Kegiatan',
            'media_url' => $video['url'],
            'is_published' => true,
            'show_on_homepage' => true,
            'show_on_gallery_page' => false,
            'published_at' => now(),
        ]);
        $item->forceFill(['sort_order' => $video['order']])->save();
    }

    $content = $this->get(route('home'))->assertOk()->getContent();

    expect($content)
        ->toContain('data-gallery-video-preview')
        ->toContain('data-gallery-video-src="'.e($directUrl).'"')
        ->not->toContain(' src="'.e($directUrl).'"')
        ->not->toContain($providerUrl)
        ->not->toContain('<iframe');
});
