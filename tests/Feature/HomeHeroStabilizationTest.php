<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;

uses(RefreshDatabase::class);

it('keeps the homepage hero available before javascript enhancement', function (): void {
    $response = $this
        ->withSession(['locale' => 'id'])
        ->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('class="hero-cinema__slide is-active"', false)
        ->assertSee('aria-hidden="false"', false)
        ->assertSee('fetchpriority="high"', false)
        ->assertSee('hero-cinema__arrow--previous', false)
        ->assertSee('hero-cinema__arrow--next', false)
        ->assertDontSee('data-hero-controls', false)
        ->assertDontSee('data-enhanced="true"', false);
});

it('uses the exact 1180 and 1181 navigation boundary', function (): void {
    $legacyNavigation = file_get_contents(resource_path(
        'css/pages/welcome/029-switch-bahasa-server-side-session-cookie-tetap-aktif.css'
    ));
    $mobileNavigation = file_get_contents(resource_path('css/pages/mobile-navigation-cinematic.css'));

    expect($legacyNavigation)
        ->toContain('@media (max-width: 1180px)')
        ->not->toContain('max-width: 1200px')
        ->and($mobileNavigation)
        ->toContain('@media (max-width: 1180px)')
        ->toContain('@media (min-width: 1181px)');
});

it('keeps Hero WebGL deferred local and free of a general 3d engine', function (): void {
    $heroCss = file_get_contents(resource_path('css/pages/welcome-hero.css'));
    $transition = file_get_contents(resource_path('js/surfaces/home/hero/transition.js'));
    $renderer = file_get_contents(resource_path('js/surfaces/home/hero/webgl/renderer.js'));
    $package = json_decode(file_get_contents(base_path('package.json')), true, flags: JSON_THROW_ON_ERROR);
    $dependencies = array_merge($package['dependencies'] ?? [], $package['devDependencies'] ?? []);

    expect($heroCss)
        ->toContain('../surfaces/home/hero/webgl.css')
        ->and($transition)
        ->toContain("import('./webgl/renderer.js')")
        ->and($renderer)
        ->toContain("canvas.getContext('webgl'")
        ->toContain("Math.min(window.devicePixelRatio || 1, 1.5)")
        ->and(array_keys($dependencies))
        ->not->toContain('three', 'babylonjs', 'gsap');
});

it('records physical and automatic Hero transition direction ownership', function (): void {
    $events = file_get_contents(resource_path('js/surfaces/home/hero/events.js'));
    $direction = file_get_contents(resource_path('js/surfaces/home/hero/direction.js'));
    $controller = file_get_contents(resource_path('js/surfaces/home/hero/controller.js'));

    expect($events)
        ->toContain('HERO_LEFT_TO_RIGHT')
        ->toContain('HERO_RIGHT_TO_LEFT')
        ->and($direction)
        ->toContain("document.documentElement.dir === 'rtl'")
        ->and($controller)
        ->toContain("{ origin: 'automatic' }");
});

it('starts incoming Hero video before the WebGL transition begins', function (): void {
    $controller = file_get_contents(resource_path('js/surfaces/home/hero/controller.js'));
    $syncPosition = strpos($controller, 'media.sync(nextIndex, canAutoAdvance());');
    $transitionPosition = strpos($controller, 'transitions.play(previousIndex, nextIndex, request);');

    expect($syncPosition)
        ->not->toBeFalse()
        ->and($transitionPosition)
        ->not->toBeFalse()
        ->and($syncPosition)
        ->toBeLessThan($transitionPosition);
});

it('restores the accepted bright Hero and floating chevron presentation', function (): void {
    $media = file_get_contents(resource_path('css/surfaces/home/hero/media.css'));
    $controls = file_get_contents(resource_path('css/surfaces/home/hero/controls.css'));
    $locale = file_get_contents(resource_path('css/surfaces/home/hero/locale.css'));

    expect($media)
        ->toContain('rgb(2 13 12 / 0.04)')
        ->toContain('transparent 48%')
        ->toContain('rgb(2 13 12 / 0.08)')
        ->not->toContain('var(--hero-overlay-strength)')
        ->and($controls)
        ->toContain('.hero-cinema__arrow--previous')
        ->toContain('.hero-cinema__arrow--next')
        ->toContain('width: clamp(92px, 7.4vw, 128px)')
        ->toContain('background: transparent')
        ->and($locale)
        ->not->toContain('.hero-cinema__media::after');
});

it('reveals the same native video without sampling or restarting it', function (): void {
    $textures = file_get_contents(resource_path('js/surfaces/home/hero/webgl/textures.js'));
    $renderer = file_get_contents(resource_path('js/surfaces/home/hero/webgl/renderer.js'));
    $shaders = file_get_contents(resource_path('js/surfaces/home/hero/webgl/shaders.js'));
    $transition = file_get_contents(resource_path('js/surfaces/home/hero/transition.js'));
    $graphics = $textures.$renderer.$shaders.$transition;

    expect($textures)
        ->toContain('function waitForLiveVideoSource')
        ->toContain('? waitForLiveVideoSource(slide, video, timeout)')
        ->toContain("kind: 'live-video'")
        ->and($renderer)
        ->toContain("alpha: true")
        ->toContain("source.kind === 'live-video'")
        ->toContain("heroWebglIncomingSource = source.kind")
        ->and($shaders)
        ->toContain('uniform float uRevealLive')
        ->toContain('vec4(outgoing.rgb, 1.0 - mask)')
        ->and($graphics)
        ->not->toContain('video.play(')
        ->not->toContain('video.load(')
        ->not->toContain('video.currentTime');
});

it('keeps every Hero source file within the 200 line contract', function (): void {
    $files = array_merge(
        File::allFiles(resource_path('css/surfaces/home/hero')),
        File::allFiles(resource_path('js/surfaces/home/hero')),
    );

    expect($files)->not->toBeEmpty();

    foreach ($files as $file) {
        $lineCount = count(file($file->getPathname(), FILE_IGNORE_NEW_LINES));
        expect($lineCount, $file->getRelativePathname())->toBeLessThanOrEqual(200);
    }
});
