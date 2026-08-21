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
            ->assertSee('data-values-spatial', false)
            ->assertSee('data-values-card-pose', false)
            ->assertSee('data-values-card-inner', false)
            ->assertSee('class="values-story__exit"', false)
            ->assertSee($copy['heading'])
            ->assertSee($copy['honorific'])
            ->assertDontSee($copy['removed_title'])
            ->assertDontSee('values-story__eyebrow', false)
            ->assertDontSee('values-card__code', false)
            ->assertDontSee('values-card__rule', false)
            ->assertDontSee('values-card__footer', false)
            ->assertDontSee('values-card__back-code', false)
            ->assertDontSee('class="values-transition"', false)
            ->assertDontSee('data-values-trail', false)
            ->assertDontSee('data-school-value-card', false)
            ->assertDontSee('aria-pressed=', false)
            ->assertDontSee('class="nilai-card', false);

        $content = $response->getContent();

        expect(substr_count($content, 'class="values-card"'))->toBe(4)
            ->and(substr_count($content, 'values-card__front'))->toBe(4)
            ->and(substr_count($content, 'class="values-card__face values-card__back"'))->toBe(4)
            ->and(substr_count($content, 'values-card__pose'))->toBe(4)
            ->and(substr_count($content, 'values-card__float'))->toBe(4)
            ->and(substr_count($content, 'values-story__title-text'))->toBe(2)
            ->and(substr_count($content, 'role="listitem"'))->toBeGreaterThanOrEqual(4)
            ->and(substr_count($content, 'class="values-story__spatial-fallback'))->toBe(3);

        if ($locale === 'ar') {
            expect($content)
                ->toContain('<html lang="ar" dir="rtl">')
                ->not->toContain('data-values-story-rtl');
        }
    }
});

it('owns one lazy deterministic Three.js spatial scene', function (): void {
    $controller = file_get_contents(resource_path('js/surfaces/home/values/controller.js'));
    $spatialController = file_get_contents(
        resource_path('js/surfaces/home/values/spatial-controller.js'),
    );
    $scene = file_get_contents(resource_path('js/surfaces/home/values/spatial-scene.js'));
    $lifecycle = file_get_contents(resource_path('js/surfaces/home/values/lifecycle.js'));
    $cssEntry = file_get_contents(resource_path('css/pages/welcome-values-story.css'));

    expect($controller)
        ->toContain('createValuesSpatialBridge')
        ->toContain('readHandoffProgress')
        ->not->toContain("from './spatial-scene.js'")
        ->and($spatialController)
        ->toContain("import('./spatial-scene.js')")
        ->and($scene)
        ->toContain("from 'three'")
        ->toContain('three/addons/lines/Line2.js')
        ->toContain('three/addons/lines/LineGeometry.js')
        ->toContain('three/addons/lines/LineMaterial.js')
        ->toContain('new WebGLRenderer')
        ->toContain('renderer.dispose()')
        ->toContain('const HANDOFF_ENTRY_WEIGHT = .24')
        ->toContain('const STORY_JOURNEY_WEIGHT = .88')
        ->toContain('reveal: [0, .68]')
        ->toContain('reveal: [.18, .82]')
        ->toContain('reveal: [.50, 1]')
        ->not->toContain('TubeGeometry')
        ->not->toContain('Math.random')
        ->and(substr_count($scene, 'reveal: ['))->toBe(3)
        ->and($lifecycle)
        ->toContain('IntersectionObserver')
        ->toContain('ResizeObserver')
        ->toContain("window.addEventListener('pagehide'")
        ->toContain("window.addEventListener('pageshow'")
        ->and($cssEntry)
        ->toContain('story-spatial.css')
        ->toContain('story-handoff.css')
        ->not->toContain('story-trail.css');
});
