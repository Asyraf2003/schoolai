<?php

it('locks the faithful homepage depth gallery source contract', function (): void {
    $gallery = file_get_contents(resource_path('views/home/sections/gallery.blade.php'));
    $depth = file_get_contents(resource_path('views/home/sections/gallery-depth.blade.php'));
    $entry = file_get_contents(resource_path('css/pages/welcome-depth-gallery.css'));
    $base = file_get_contents(resource_path('css/surfaces/home/gallery-depth/base.css'));
    $responsive = file_get_contents(resource_path(
        'css/surfaces/home/gallery-depth/responsive.css'
    ));
    $controller = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/controller.js'
    ));
    $runtime = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/three-runtime.js'
    ));
    $engine = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/engine.js'
    ));
    $planes = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/gallery.js'
    ));
    $background = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/background.js'
    ));
    $trail = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/trail.js'
    ));
    $trailController = file_get_contents(resource_path(
        'js/surfaces/home/gallery-depth/trail-controller.js'
    ));
    $license = base_path('docs/third-party/codrops-depth-gallery-MIT.txt');

    expect($gallery)
        ->toContain("@include('home.sections.gallery-depth')")
        ->and($depth)
        ->toContain('data-depth-gallery-canvas')
        ->toContain('data-depth-gallery-labels')
        ->toContain('data-depth-gallery-source')
        ->toContain('class="depth-gallery__fallback-item" role="listitem"')
        ->toContain('data-position-x="{{ $preset[\'x\'] }}"')
        ->toContain('data-background-color')
        ->not->toContain('<svg')
        ->not->toContain('depth-gallery__card')
        ->and($entry)
        ->toContain('gallery-depth/base.css')
        ->toContain('gallery-depth/cards.css')
        ->toContain('gallery-depth/responsive.css')
        ->and($base)
        ->toContain('height: 100svh')
        ->toContain('position: sticky')
        ->not->toContain('perspective:')
        ->not->toContain('transform-style: preserve-3d')
        ->and($responsive)
        ->toContain('@media (min-width: 640px)')
        ->toContain('@media (min-width: 768px)')
        ->toContain('@media (min-width: 1024px)')
        ->toContain('@media (min-width: 1280px)')
        ->toContain('@media (min-width: 1536px)')
        ->toContain('@media (prefers-reduced-motion: reduce)')
        ->and($controller)
        ->toContain('loadThreeRuntime')
        ->toContain('DepthGalleryEngine')
        ->toContain('IntersectionObserver')
        ->toContain('visibilitychange')
        ->and($runtime)
        ->toContain('three@0.183.0')
        ->toContain('/* @vite-ignore */')
        ->and($engine)
        ->toContain('PerspectiveCamera(45, 1, 0.1, 100)')
        ->toContain('new this.THREE.WebGLRenderer')
        ->toContain('renderer.clearDepth()')
        ->toContain('Math.min(window.devicePixelRatio || 1, 1.5)')
        ->and($planes)
        ->toContain('PlaneGeometry(3, 3)')
        ->toContain('this.planeGap = 5')
        ->toContain('this.mobilePlaneScale = 0.65')
        ->toContain('this.mobileXSpreadFactor = 0.25')
        ->and($background)
        ->toContain('ShaderMaterial')
        ->toContain('setMoodBlend')
        ->and($trail)
        ->toContain('CatmullRomCurve3')
        ->toContain('createTaperedTube')
        ->and($trailController)
        ->toContain('horizontalCycles: 1.85')
        ->toContain('verticalCycles: 2.1')
        ->and(file_exists(resource_path(
            'js/surfaces/home/gallery-depth/renderer.js'
        )))
        ->toBeFalse()
        ->and(file_exists(resource_path(
            'js/surfaces/home/gallery-depth/scene.js'
        )))
        ->toBeFalse()
        ->and(file_exists($license))
        ->toBeTrue();
});

it('keeps every active depth gallery source within the file limit', function (): void {
    $files = glob(resource_path('js/surfaces/home/gallery-depth/*.js'));

    expect($files)->not->toBeEmpty();

    foreach ($files as $file) {
        $lines = count(file($file));
        expect($lines, basename($file).' exceeds 200 lines')->toBeLessThanOrEqual(200);
    }
});
