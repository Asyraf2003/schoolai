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
        ->assertSee('data-hero-controls', false)
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

it('keeps Hero WebGL deferred, local, and free of a general 3d engine', function (): void {
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
