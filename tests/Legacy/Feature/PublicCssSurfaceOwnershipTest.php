<?php

it('loads Article Detail styling only from its public surface adapter', function (): void {
    $sharedEntry = file_get_contents(resource_path('css/pages/welcome.css'));
    $sharedTail = file_get_contents(resource_path('css/pages/welcome/035-welcome-cascade-035.css'));
    $surface = readOwnedSource(resource_path('css/surfaces/public/article-detail.css'), ['resources/css/surfaces/public/article-detail-hero.css', 'resources/css/surfaces/public/article-detail-content.css', 'resources/css/surfaces/public/article-detail-responsive.css']);
    $view = file_get_contents(resource_path('views/pages/artikel-detail.blade.php'));
    $vite = file_get_contents(base_path('vite.config.js'));

    expect($sharedEntry)
        ->not->toContain('./welcome/036-welcome-cascade-036.css')
        ->not->toContain('./welcome/037-welcome-cascade-037.css')
        ->and($sharedTail)
        ->not->toContain('.article-detail-')
        ->and($surface)
        ->toContain('.article-detail-hero {')
        ->toContain('.article-detail-content__grid')
        ->toContain('.article-related-card')
        ->toContain('@media (max-width: 980px)')
        ->toContain('@media (max-width: 767px)')
        ->and($view)
        ->toContain("@vite('resources/css/surfaces/public/article-detail.css')")
        ->toContain('class="article-detail-page"')
        ->and($vite)
        ->toContain("'resources/css/surfaces/public/article-detail.css'");
});

it('loads Gallery styling only from its public surface adapter', function (): void {
    $sharedEntry = file_get_contents(resource_path('css/pages/welcome.css'));
    $legacyGalleryExists = file_exists(resource_path('css/pages/welcome/049-gallery-codrops-navigation.css'));
    $surface = readOwnedSource(resource_path('css/surfaces/public/gallery.css'), ['resources/css/surfaces/public/gallery-grid.css', 'resources/css/surfaces/public/gallery-effects.css', 'resources/css/surfaces/public/gallery-modal.css']);
    $view = file_get_contents(resource_path('views/pages/galeri.blade.php'));
    $vite = file_get_contents(base_path('vite.config.js'));

    expect($sharedEntry)
        ->not->toContain('./welcome/049-gallery-codrops-navigation.css')
        ->and($surface)
        ->toContain('.gallery-grid-demo {')
        ->toContain('.gallery-grid.effect-8 li.animate')
        ->toContain('.gallery-grid-modal {')
        ->not->toContain('.home-gallery-links')
        ->and($legacyGalleryExists)
        ->toBeFalse()
        ->and($view)
        ->toContain("@vite('resources/css/surfaces/public/gallery.css')")
        ->toContain('data-gallery-grid')
        ->and($vite)
        ->toContain("'resources/css/surfaces/public/gallery.css'");
});
