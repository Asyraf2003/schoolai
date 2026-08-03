<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the rebuilt localized semantic values story', function (): void {
    $locales = [
        'id' => [
            'removed_title' => 'Nilai yang Menjadi Arah Tumbuh Anak',
            'heading' => 'PONDASI KARAKTER',
            'honorific' => 'Rasulullah shallallahu ‘alaihi wasallam',
        ],
        'en' => [
            'removed_title' => 'Values That Guide Every Child’s Growth',
            'heading' => 'VALUES STUDENTS',
            'honorific' => 'the Messenger of Allah, peace and blessings be upon him',
        ],
        'ar' => [
            'removed_title' => 'قيم ترسم مسار نمو الطفل',
            'heading' => 'أَسَاسُ الْمَدْرَسَةِ',
            'honorific' => 'رَسُولُ اللهِ صَلَّى اللهُ عَلَيْهِ وَسَلَّمَ',
        ],
    ];

    foreach ($locales as $locale => $copy) {
        $response = $this
            ->withSession(['locale' => $locale])
            ->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('id="nilai"', false)
            ->assertSee('data-values-story', false)
            ->assertSee('class="values-story__entry"', false)
            ->assertSee('data-values-timeline', false)
            ->assertSee('data-values-stage', false)
            ->assertSee('data-values-perspective', false)
            ->assertSee('data-values-cards', false)
            ->assertSee('data-values-card-pose', false)
            ->assertSee('data-values-card-inner', false)
            ->assertSee('data-values-trail-path', false)
            ->assertSee('data-values-trail-head', false)
            ->assertSee('class="values-story__exit"', false)
            ->assertSee($copy['heading'])
            ->assertSee($copy['honorific'])
            ->assertDontSee($copy['removed_title'])
            ->assertDontSee('values-story__eyebrow', false)
            ->assertDontSee('class="values-transition"', false)
            ->assertDontSee('data-school-value-card', false)
            ->assertDontSee('aria-pressed=', false)
            ->assertDontSee('class="nilai-card', false);

        $content = $response->getContent();

        expect(substr_count($content, 'class="values-card"'))->toBe(4)
            ->and(substr_count($content, 'values-card__front'))->toBe(4)
            ->and(substr_count($content, 'values-card__back'))->toBe(4)
            ->and(substr_count($content, 'values-card__pose'))->toBe(4)
            ->and(substr_count($content, 'values-card__float'))->toBe(4)
            ->and(substr_count($content, 'values-story__title-text'))->toBe(2)
            ->and(substr_count($content, 'role="listitem"'))->toBeGreaterThanOrEqual(4)
            ->and(substr_count($content, 'pathLength="1"'))->toBe(1)
            ->and(substr_count($content, 'data-values-trail-head'))->toBe(1);

        if ($locale === 'ar') {
            expect($content)
                ->toContain('<html lang="ar" dir="rtl">')
                ->not->toContain('data-values-story-rtl');
        }
    }
});
