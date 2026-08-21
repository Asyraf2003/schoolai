<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders localized About Vision and Mission before Program', function (): void {
    $labels = [
        'id' => ['about' => 'TENTANG', 'vision' => 'VISI', 'mission' => 'MISI'],
        'en' => ['about' => 'ABOUT', 'vision' => 'VISION', 'mission' => 'MISSION'],
        'ar' => ['about' => 'عن المدرسة', 'vision' => 'الرؤية', 'mission' => 'الرسالة'],
    ];
    $missionSnippets = [
        'id' => ['Membentuk generasi Islam berdasarkan', 'baik secara lokal maupun global.'],
        'en' => ['Nurturing a Muslim generation based on the', 'both locally and globally.'],
        'ar' => ['تنشئة جيل مسلم يستند إلى', 'على المستويين المحلي والعالمي.'],
    ];

    foreach (['id', 'en', 'ar'] as $locale) {
        app()->setLocale($locale);
        $response = $this->withSession(['locale' => $locale])->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('id="visi-misi"', false)
            ->assertSee('data-vision-story', false)
            ->assertSee('data-vision-stories', false)
            ->assertSee('data-vision-visuals', false)
            ->assertSee('data-vision-background', false)
            ->assertSee($labels[$locale]['about'])
            ->assertSee($labels[$locale]['vision'])
            ->assertSee($labels[$locale]['mission'])
            ->assertSee($missionSnippets[$locale][0])
            ->assertSee($missionSnippets[$locale][1])
            ->assertDontSee('data-vision-track', false)
            ->assertDontSee('data-vision-program', false);

        $content = $response->getContent();
        expect(substr_count($content, 'data-vision-panel='))->toBe(3)
            ->and(substr_count($content, 'data-vision-visual='))->toBe(3)
            ->and(substr_count($content, 'data-vision-background-layer='))->toBe(2)
            ->and(substr_count($content, 'data-vision-art'))->toBe(3)
            ->and(substr_count($content, 'media/home/vision-paper-'))->toBe(3)
            ->and(strpos($content, 'id="visi-misi"'))
            ->toBeLessThan(strpos($content, 'id="program"'));

        if ($locale === 'ar') {
            expect($content)->toContain('صلى الله عليه وسلم')->not->toContain('ﷺ');
        }
    }
});

it('provides one configurable gradual Vision background compositor', function (): void {
    $timeline = file_get_contents(resource_path('js/surfaces/home/vision-story/timeline.js'));
    $compositor = file_get_contents(
        resource_path('js/surfaces/home/vision-story/background-compositor.js'),
    );
    $background = file_get_contents(
        resource_path('css/pages/welcome-vision-waapi/background.css'),
    );

    expect($timeline)
        ->toContain('createVisionBackgroundCompositor')
        ->toContain('background.setProgress(progress)')
        ->and($compositor)
        ->toContain('visionBackgroundColor')
        ->toContain('visionBackgroundPattern')
        ->toContain("pattern: 'none'")
        ->toContain("const DESKTOP_STATE_NAMES = ['about', 'vision', 'mission']")
        ->toContain("matchMedia('(min-width: 1280px)')")
        ->toContain('--vision-state-diffusion')
        ->not->toContain('Krawangan')
        ->not->toContain('Mashrabiya')
        ->and($background)
        ->toContain('--vision-background-default: #f4f1e9')
        ->toContain('--vision-about-color: #efe3ca')
        ->toContain('--vision-vision-color: #cbdfe4')
        ->toContain('--vision-mission-color: #d1dfca')
        ->toContain('repeating-conic-gradient')
        ->toContain('background-image: var(--vision-state-pattern)')
        ->toContain('filter: blur(var(--vision-state-diffusion))');
});

it('uses a native pinned mask reveal without owning document scroll', function (): void {
    $controller = file_get_contents(resource_path('js/surfaces/home/vision-story/controller.js'));
    $timeline = file_get_contents(resource_path('js/surfaces/home/vision-story/timeline.js'));
    $enhanced = file_get_contents(resource_path('css/pages/welcome-vision-waapi/enhanced.css'));

    expect($controller)
        ->toContain("matchMedia('(min-width: 1024px)')")
        ->toContain('current += (target - current) * alpha')
        ->not->toContain('window.scrollTo')
        ->not->toContain('programStoryTravel')
        ->not->toContain("new CustomEvent('vision:layout')")
        ->and($timeline)
        ->toContain('style.clipPath')
        ->toContain('translate3d(0, ${y.toFixed(3)}%, 0) scale(1.08)')
        ->and($enhanced)
        ->toContain('position: sticky')
        ->toContain('clip-path: inset(0 0 0% 0)')
        ->not->toContain('has-integrated-program');
});

it('pins Mission copy to the flexible grid track in both LTR and RTL', function (): void {
    $base = file_get_contents(resource_path('css/pages/welcome-vision-waapi/base.css'));
    $enhanced = file_get_contents(resource_path('css/pages/welcome-vision-waapi/enhanced.css'));

    expect($base)
        ->toContain('grid-template-columns: 2.5rem minmax(0, 1fr)')
        ->toContain('grid-column: 1')
        ->toContain('grid-column: 2')
        ->toContain('grid-template-columns: minmax(0, 1fr) 2.5rem')
        ->toContain('html[dir="rtl"] .vision-arch__mission-list li::before')
        ->toContain('html[dir="rtl"] .vision-arch__mission-list h4')
        ->toContain('text-align: start')
        ->and($enhanced)
        ->toContain('grid-template-columns: 2.2rem minmax(0, 1fr)')
        ->toContain('html[dir="rtl"] .vision-arch.is-enhanced .vision-arch__mission-list li')
        ->toContain('grid-template-columns: minmax(0, 1fr) 2.2rem');
});
