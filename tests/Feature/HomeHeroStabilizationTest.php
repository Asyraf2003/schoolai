<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

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
        ->assertDontSee('data-enhanced="true"', false);
});

it('keeps exactly two lower floating chevrons and omits the retired rail', function (): void {
    $response = $this
        ->withSession(['locale' => 'id'])
        ->get(route('home'));

    $controls = file_get_contents(resource_path('css/surfaces/home/hero/controls.css'));

    $response
        ->assertOk()
        ->assertSee('class="hero-cinema__arrow hero-cinema__arrow--previous"', false)
        ->assertSee('class="hero-cinema__arrow hero-cinema__arrow--next"', false)
        ->assertDontSee('data-hero-controls', false)
        ->assertDontSee('data-hero-progress', false)
        ->assertDontSee('data-hero-dot', false)
        ->assertDontSee('data-hero-playback', false);

    expect(substr_count($response->getContent(), 'class="hero-cinema__arrow hero-cinema__arrow--'))
        ->toBe(2)
        ->and($controls)
        ->toContain('.hero-cinema__arrow--previous')
        ->toContain('.hero-cinema__arrow--next')
        ->toContain('inset-block-end: 150px')
        ->toContain('inset-block-end: 50px')
        ->not->toContain('.hero-cinema__controls')
        ->not->toContain('.hero-cinema__playback')
        ->not->toContain('.hero-cinema__dot');
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

it('keeps hero ownership explicit and free of heavy animation dependencies', function (): void {
    $heroCss = file_get_contents(resource_path('css/pages/welcome-hero.css'));
    $package = json_decode(file_get_contents(base_path('package.json')), true, flags: JSON_THROW_ON_ERROR);
    $dependencies = array_merge($package['dependencies'] ?? [], $package['devDependencies'] ?? []);

    expect($heroCss)
        ->toContain('../surfaces/home/hero/layout.css')
        ->toContain('../surfaces/home/hero/media.css')
        ->toContain('../surfaces/home/hero/motion.css')
        ->toContain('../surfaces/home/hero/controls.css')
        ->toContain('../surfaces/home/hero/responsive.css')
        ->toContain('../surfaces/home/hero/locale.css')
        ->toContain('../surfaces/home/hero/reduced-motion.css')
        ->and(array_keys($dependencies))
        ->not->toContain('three', 'babylonjs', 'gsap');
});

it('keeps each new hero source file within the 200 line contract', function (): void {
    $files = glob(resource_path('css/surfaces/home/hero/*.css')) ?: [];
    $files = array_merge($files, glob(resource_path('js/surfaces/home/hero/*.js')) ?: []);

    expect($files)->not->toBeEmpty();

    foreach ($files as $file) {
        $lineCount = count(file($file, FILE_IGNORE_NEW_LINES));
        expect($lineCount, basename($file))->toBeLessThanOrEqual(200);
    }
});
