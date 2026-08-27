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
            ->assertDontSee('class="nilai-card', false);

        $content = $response->getContent();
        preg_match(
            '/<section\s+class="values-story".*?<\/section>/s',
            $content,
            $valuesSection,
        );

        expect($valuesSection[0] ?? '')
            ->not->toBe('')
            ->not->toContain('aria-pressed=');

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
    $frameTarget = file_get_contents(resource_path('js/surfaces/home/values/frame-target.js'));
    $spatialController = file_get_contents(
        resource_path('js/surfaces/home/values/spatial-controller.js'),
    );
    $scene = file_get_contents(resource_path('js/surfaces/home/values/spatial-scene.js'));
    $strokes = file_get_contents(resource_path('js/surfaces/home/values/spatial-strokes.js'));
    $lifecycle = file_get_contents(resource_path('js/surfaces/home/values/lifecycle.js'));
    $cssEntry = file_get_contents(resource_path('css/pages/welcome-values-story.css'));
    $shell = file_get_contents(resource_path('css/surfaces/home/values/story-shell.css'));

    expect($controller)
        ->toContain('createValuesSpatialBridge')
        ->toContain('readValuesFrameTarget')
        ->not->toContain("from './spatial-scene.js'")
        ->and($frameTarget)
        ->toContain('readHandoffProgress')
        ->toContain('readGalleryHandoffProgress')
        ->and($spatialController)
        ->toContain("import('./spatial-scene.js')")
        ->and($scene)
        ->toContain("from 'three'")
        ->toContain("from './spatial-strokes.js'")
        ->toContain('new WebGLRenderer')
        ->toContain('renderer.dispose()')
        ->toContain('const HANDOFF_ENTRY_WEIGHT = .24')
        ->toContain('const STORY_JOURNEY_WEIGHT = .88')
        ->and($strokes)
        ->toContain('three/addons/lines/Line2.js')
        ->toContain('three/addons/lines/LineGeometry.js')
        ->toContain('three/addons/lines/LineMaterial.js')
        ->toContain('[6.55, 3.55, .08]')
        ->toContain('[-6.55, 3.48, .16]')
        ->toContain('[6.55, 3.55, .34]')
        ->toContain('reveal: [0, .68]')
        ->toContain('reveal: [.18, .82]')
        ->toContain('reveal: [.50, 1]')
        ->not->toContain('TubeGeometry')
        ->not->toContain('Math.random')
        ->and(substr_count($strokes, 'reveal: ['))->toBe(3)
        ->and($shell)
        ->not->toContain('repeating-conic-gradient')
        ->not->toContain('--values-geometry-opacity')
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

it('keeps responsive values motion scroll-deterministic and tilt-bounded', function (): void {
    $controller = file_get_contents(resource_path('js/surfaces/home/values/controller.js'));
    $layout = file_get_contents(resource_path('js/surfaces/home/values/layout.js'));
    $geometry = file_get_contents(resource_path('js/surfaces/home/values/geometry.js'));
    $responsive = file_get_contents(resource_path('css/surfaces/home/values/story-responsive.css'));
    $paint = file_get_contents(resource_path('js/surfaces/home/values/paint.js'));

    expect($controller)
        ->toContain('geometry.mode < 4 || !active')
        ->toContain('target.handoff')
        ->toContain('target.galleryHandoff')
        ->not->toContain('if (!active) return;')
        ->and($layout)
        ->toContain('RESPONSIVE_MAX_TILT = 20')
        ->toContain('RESPONSIVE_GAP_RISE_SHARE = .95')
        ->toContain('Math.sin(Math.PI * clamp(local))')
        ->not->toContain('tabletRailY')
        ->not->toContain('TABLET_EDGE_ANGLE')
        ->and($geometry)
        ->toContain('rowGap: Math.max(0, numberFrom(gridStyles.rowGap))')
        ->and($responsive)
        ->toContain('perspective: none')
        ->and($paint)
        ->toContain('exitAmount(targetProgress)');
});
