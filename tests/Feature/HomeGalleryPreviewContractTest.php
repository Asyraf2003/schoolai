<?php

it('uses the Codrops GridLoadingEffects contract for the public gallery', function (): void {
    $galleryPage = file_get_contents(resource_path('views/pages/galeri.blade.php'));
    $galleryRuntime = file_get_contents(resource_path('js/pages/welcome/gallery-wall.js'));
    $galleryCss = file_get_contents(resource_path('css/pages/welcome/049-gallery-codrops-navigation.css'));

    expect($galleryPage)
        ->toContain("@extends('layouts.public'")
        ->toContain('gallery-grid-demo__demos')
        ->toContain('data-gallery-category-target')
        ->toContain('class="gallery-grid effect-{{ ($categoryIndex % 8) + 1 }}"')
        ->toContain('data-gallery-modal-open')
        ->toContain('data-gallery-modal-title')
        ->toContain('gallery-grid-modal__close')
        ->not->toContain('@elseif')
        ->not->toContain('@else')
        ->not->toContain('gallery-perspective')
        ->not->toContain('layouts.public-gallery')
        ->and($galleryRuntime)
        ->toContain("if (window.matchMedia('(max-width: 400px)').matches) return 1")
        ->toContain("if (window.matchMedia('(max-width: 900px)').matches) return 2")
        ->toContain('return 3;')
        ->toContain('Math.random() * .3 + .4')
        ->toContain('threshold: .2')
        ->toContain("document.createElement('video')")
        ->toContain("document.createElement('iframe')")
        ->toContain("document.createElement('img')")
        ->and($galleryCss)
        ->toContain('max-width: 69em;')
        ->toContain('width: 33.333333%;')
        ->toContain('transform: translateY(200px);')
        ->toContain('transform: scale(.6);')
        ->toContain('translateZ(400px) translateY(300px) rotateX(-90deg)')
        ->toContain('transform: rotateX(-180deg);')
        ->toContain('transform: rotateX(-80deg);')
        ->toContain('transform: rotateY(-180deg);')
        ->toContain('transform: scale(.4);')
        ->toContain('@media screen and (max-width: 900px)')
        ->toContain('@media screen and (max-width: 400px)');
});

it('replaces the homepage facilities story with up to three gallery section links', function (): void {
    $galleryBlade = file_get_contents(resource_path('views/home/sections/gallery.blade.php'));
    $composer = file_get_contents(app_path('View/Composers/HomeGalleryComposer.php'));
    $idPresentation = file_get_contents(lang_path('id/home_presentation.php'));
    $enPresentation = file_get_contents(lang_path('en/home_presentation.php'));
    $arPresentation = file_get_contents(lang_path('ar/home_presentation.php'));

    expect($galleryBlade)
        ->toContain('home-gallery-links__list')
        ->toContain('home-gallery-links__item')
        ->toContain("href=\"{{ \$teaser['href'] }}\"")
        ->not->toContain("@include('home.sections.gallery-depth')")
        ->and($composer)
        ->toContain('GalleryPageSection::query()')
        ->toContain("route('galeri').'#gallery-main'")
        ->toContain("route('galeri').'#gallery-section-'.\$section->id")
        ->toContain('->limit(2)')
        ->toContain('array_slice($teasers, 0, 3)')
        ->and($idPresentation)
        ->toContain("'gallery_heading' => 'GALERI'")
        ->and($enPresentation)
        ->toContain("'gallery_heading' => 'GALLERY'")
        ->and($arPresentation)
        ->toContain("'gallery_heading' => 'المعرض'");
});

it('builds the gallery navbar from three random page sections plus the homepage gallery link', function (): void {
    $presenter = file_get_contents(app_path('View/Presenters/SiteNavbarMenuPresenter.php'));

    expect($presenter)
        ->toContain('prepareGalleryItem(')
        ->toContain('GalleryPageSection::query()')
        ->toContain('->inRandomOrder()')
        ->toContain('->limit(3)')
        ->toContain("route('galeri').'#gallery-section-'.\$section->id")
        ->toContain("'href' => \$homeAnchor('#galeri')")
        ->toContain("\$item['label'] = (string) \$this->translator->get('pages.galeri.title')")
        ->toContain("\$item['route_patterns'] = ['galeri']");
});
