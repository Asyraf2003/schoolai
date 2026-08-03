<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders one localized semantic values card story in every locale', function (): void {
    $titles = [
        'id' => 'Nilai yang Menjadi Arah Tumbuh Anak',
        'en' => 'Values That Guide Every Child’s Growth',
        'ar' => 'قيم ترسم مسار نمو الطفل',
    ];

    foreach ($titles as $locale => $title) {
        $response = $this
            ->withSession(['locale' => $locale])
            ->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('class="values-transition"', false)
            ->assertSee('id="nilai"', false)
            ->assertSee('data-values-story', false)
            ->assertSee('data-values-stage', false)
            ->assertSee('values-story__trail-line', false)
            ->assertSee('values-story__trail-head', false)
            ->assertSee('values-story__title-line--one', false)
            ->assertSee('values-story__title-line--two', false)
            ->assertSee('data-values-cards', false)
            ->assertSee('data-values-card-inner', false)
            ->assertSee('values-card__front', false)
            ->assertSee('values-card__back', false)
            ->assertSee('aria-hidden="true"', false)
            ->assertSee($title)
            ->assertDontSee('data-school-value-card', false)
            ->assertDontSee('aria-pressed=', false)
            ->assertDontSee('class="nilai-card', false);

        $content = $response->getContent();

        expect(substr_count($content, 'class="values-card"'))->toBe(4)
            ->and(substr_count($content, 'values-card__front'))->toBe(4)
            ->and(substr_count($content, 'values-card__back'))->toBe(4)
            ->and(substr_count($content, 'role="listitem"'))->toBeGreaterThanOrEqual(4)
            ->and(substr_count($content, 'pathLength="1"'))->toBe(3);

        if ($locale === 'ar') {
            expect($content)
                ->toContain('<html lang="ar" dir="rtl">')
                ->not->toContain('data-values-story-rtl');
        }
    }
});
