<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders one nonce critical gate before deferred runtime and one localized native progress in each locale', function (string $locale): void {
    app()->setLocale($locale);
    $response = $this->get(route('home'));
    $response->assertOk()->assertSee(__('home.opening.preparing'))
        ->assertSee('<html lang="'.$locale.'"', false);
    $html = $response->getContent();
    preg_match('/<script[^>]+data-home-opening-bootstrap[^>]*>/', $html, $bootstrap);
    expect($bootstrap[0] ?? '')->toContain('nonce=')->toContain('data-home-opening-recovery=')
        ->and(strpos($html, 'data-home-opening-bootstrap'))->toBeLessThan(strpos($html, 'type="module"'))
        ->and(preg_match_all('/<div\b[^>]*\bdata-home-opening(?=[\s>])/', $html))->toBe(1)
        ->and(preg_match_all('/<progress\b[^>]*data-home-opening-progress/', $html))->toBe(1)
        ->and($html)->toContain('aria-labelledby="home-opening-label"')->toContain('value="0" max="100"')
        ->not->toContain('<html data-home-scroll-gate="locked"');
})->with(['id', 'en', 'ar']);
