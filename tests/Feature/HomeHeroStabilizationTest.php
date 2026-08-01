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

it('restores the pre-akella CSS transition and removes the production WebGL graph', function (): void {
    $heroCss = file_get_contents(resource_path('css/pages/welcome-hero.css'));
    $controller = file_get_contents(resource_path('js/surfaces/home/hero/controller.js'));
    $events = file_get_contents(resource_path('js/surfaces/home/hero/events.js'));
    $package = json_decode(file_get_contents(base_path('package.json')), true, flags: JSON_THROW_ON_ERROR);
    $dependencies = array_merge($package['dependencies'] ?? [], $package['devDependencies'] ?? []);

    expect($heroCss)
        ->toContain('../surfaces/home/hero/motion.css')
        ->not->toContain('webgl.css')
        ->and($controller)
        ->toContain("window.setTimeout(clearTransition, 980)")
        ->not->toContain('createHeroTransitionController')
        ->not->toContain("import('./webgl/renderer.js')")
        ->and($events)
        ->toContain("document.documentElement.dir === 'rtl'")
        ->not->toContain("from './direction.js'")
        ->and(File::exists(resource_path('js/surfaces/home/hero/transition.js')))
        ->toBeFalse()
        ->and(File::exists(resource_path('js/surfaces/home/hero/webgl/renderer.js')))
        ->toBeFalse()
        ->and(File::exists(resource_path('css/surfaces/home/hero/webgl.css')))
        ->toBeFalse()
        ->and(array_keys($dependencies))
        ->not->toContain('three', 'babylonjs', 'gsap');
});

it('keeps the accepted bright Hero and floating chevron presentation', function (): void {
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

it('retains the rejected WebGL history in architecture documentation', function (): void {
    $readme = file_get_contents(base_path('docs/architecture/README.md'));
    $currentState = file_get_contents(base_path('docs/architecture/UI_UX_CURRENT_STATE.md'));

    expect(File::exists(base_path(
        'docs/architecture/blueprints/2026-08-01-home-hero-webgl-demo1.md'
    )))->toBeTrue()
        ->and(File::exists(base_path(
            'docs/architecture/blueprints/2026-08-01-home-hero-scope-correction.md'
        )))->toBeTrue()
        ->and($readme)
        ->toContain('Hero transition scope violation')
        ->and($currentState)
        ->toContain('HOME-HERO-REMOVE-AKELLA-WEBGL-001');
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
