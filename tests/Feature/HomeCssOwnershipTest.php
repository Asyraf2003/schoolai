<?php

it('keeps retired homepage visual families out of the active legacy entry', function (): void {
    $entry = file_get_contents(resource_path('css/pages/welcome.css'));

    $retiredImports = [
        './welcome/004-11-nilai-sekolah.css',
        './welcome/007-welcome-cascade-007.css',
        './welcome/008-welcome-cascade-008.css',
        './welcome/009-welcome-cascade-009.css',
        './welcome/010-welcome-cascade-010.css',
        './welcome/011-welcome-cascade-011.css',
        './welcome/012-welcome-cascade-012.css',
        './welcome/013-welcome-cascade-013.css',
        './welcome/014-gallery-teaser-data-ready-local.css',
        './welcome/015-premium-compact-gallery-buffer-like-centered-header.css',
        './welcome/016-edit-di-sini-jarak-antara-teks-kiri-dan-gambar-kanan.css',
        './welcome/017-edit-di-sini-mobile-jangan-geser-gambar-di-tablet-hp.css',
        './welcome/018-editorial-article-layout-featured-story-compact-side.css',
        './welcome/019-welcome-cascade-019.css',
        './welcome/020-kartu-program-dipindah-ke-kiri-spotlight-besar-ke-ka.css',
    ];

    foreach ($retiredImports as $retiredImport) {
        expect($entry)->not->toContain($retiredImport);
    }
});

it('keeps rebuilt home surfaces owned by their dedicated adapters', function (): void {
    $valuesView = file_get_contents(resource_path('views/home/sections/school-values.blade.php'));
    $programView = file_get_contents(resource_path('views/home/sections/featured-programs.blade.php'));
    $galleryView = file_get_contents(resource_path('views/home/sections/gallery-depth.blade.php'));
    $articlesView = file_get_contents(resource_path('views/home/sections/articles.blade.php'));

    $valuesEntry = file_get_contents(resource_path('css/pages/welcome-values-story.css'));
    $programLoader = file_get_contents(resource_path('js/pages/welcome/program-cards.js'));
    $galleryEntry = file_get_contents(resource_path('css/pages/welcome-depth-gallery.css'));
    $galleryBase = file_get_contents(resource_path('css/pages/welcome-depth-gallery/base.css'));
    $articlesEntry = file_get_contents(resource_path('css/pages/welcome-article-showcase.css'));

    expect($valuesView)
        ->toContain('class="values-story"')
        ->not->toContain('class="nilai-card')
        ->and($valuesEntry)
        ->toContain('story-shell.css')
        ->toContain('story-cards.css')
        ->and($programView)
        ->toContain('class="program-kinetic"')
        ->not->toContain('class="program-card')
        ->and($programLoader)
        ->toContain("import '../../../css/pages/welcome/program-showcase-desktop.css';")
        ->and($galleryView)
        ->toContain('class="gallery-story"')
        ->not->toContain('class="galeri-story"')
        ->and($galleryEntry)
        ->toContain('@import "./welcome-depth-gallery/base.css";')
        ->and($galleryBase)
        ->toContain('.home-page .galeri-section')
        ->toContain('.gallery-story')
        ->and($articlesView)
        ->toContain('class="article-showcase"')
        ->not->toContain('class="artikel-section')
        ->and($articlesEntry)
        ->toContain('article-showcase/base.css')
        ->toContain('article-showcase/typography.css')
        ->toContain('article-showcase/responsive.css');
});
