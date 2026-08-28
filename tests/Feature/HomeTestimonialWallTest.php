<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the testimonial wall after gallery with three seven-card tracks', function (): void {
    $response = $this->get(route('home'))->assertOk();
    $html = $response->getContent();
    $galleryPosition = strpos($html, 'id="galeri"');
    $testimonialPosition = strpos($html, 'id="testimoni"');

    expect($galleryPosition)->not->toBeFalse()
        ->and($testimonialPosition)->not->toBeFalse()
        ->and($galleryPosition)->toBeLessThan($testimonialPosition)
        ->and($html)->not->toContain('testimonial-wall__eyebrow')
        ->and(substr_count($html, 'data-testimonial-track'))->toBe(3)
        ->and(substr_count($html, 'data-testimonial-card'))->toBe(21);
});

it('keeps testimonial sample content complete in every public locale', function (): void {
    foreach (['id', 'en', 'ar'] as $locale) {
        $section = trans('testimonials', [], $locale);
        $rows = $section['rows'] ?? [];

        expect($section)->toBeArray()
            ->and(trim((string) ($section['title'] ?? '')))->not->toBe('')
            ->and($rows)->toBeArray()->toHaveCount(3);

        foreach ($rows as $row) {
            $cards = $row['cards'] ?? [];

            expect($cards)->toBeArray()->toHaveCount(7)
                ->and(collect($cards)->every(
                    fn (array $card): bool => trim((string) ($card['quote'] ?? '')) !== ''
                        && trim((string) ($card['name'] ?? '')) !== ''
                        && trim((string) ($card['role'] ?? '')) !== ''
                ))->toBeTrue();
        }
    }
});

it('mirrors Testimonial travel physically in RTL without reversing row phase', function (): void {
    $script = file_get_contents(resource_path('js/pages/welcome/testimonial-wall.js'));

    expect($script)
        ->toContain('const signedTravel = isRtl ? travel : -travel;')
        ->toContain('const startX = towardInlineStart ? 0 : signedTravel;')
        ->toContain('const endX = towardInlineStart ? signedTravel : 0;')
        ->toContain('const travel = overflow * 0.9;')
        ->not->toContain('towardInlineStart ? -travel : 0');
});
