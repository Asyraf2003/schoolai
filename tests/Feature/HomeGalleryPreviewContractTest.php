<?php

it('keeps homepage gallery passive and reserves video for the gallery page', function (): void {
    $homeQuery = file_get_contents(app_path(
        'Http/Controllers/Concerns/BuildsHomeArticlesAndGallery.php'
    ));
    $normalizer = file_get_contents(app_path(
        'Http/Controllers/Concerns/NormalizesHomeGallery.php'
    ));
    $depthBlade = file_get_contents(resource_path(
        'views/home/sections/gallery-depth.blade.php'
    ));
    $controller = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/controller.js'
    ));
    $transition = file_get_contents(resource_path(
        'js/components/gallery-route-transition.js'
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
        ->toContain("->where('type', 'photo')")
        ->and($normalizer)
        ->toContain('isDummyGalleryCaption')
        ->toContain("__('home.galeri.section_subtitle')")
        ->and($depthBlade)
        ->toContain('data-depth-gallery-end-link')
        ->not->toContain('data-media-url')
        ->not->toContain('data-is-video')
        ->not->toContain('<iframe')
        ->and($controller)
        ->toContain('bindGalleryRouteExit')
        ->not->toContain('createGalleryStoryLightbox')
        ->not->toContain('openStoryMedia')
        ->and($transition)
        ->toContain('rotate(45deg)')
        ->toContain("filter: 'blur(18px)'")
        ->toContain('window.location.assign(link.href)')
        ->toContain('playGalleryRouteArrival')
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
