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
        'id' => ['Membentuk generasi Islami berdasarkan', 'peduli lingkungan'],
        'en' => ['Nurturing an Islamic generation based on the', 'environmental awareness'],
        'ar' => ['تنشئة جيل إسلامي يستند إلى', 'الوعي البيئي'],
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
            ->assertSee((string) config('media.homepage_about_video_url'), false)
            ->assertSee((string) config('media.homepage_mission_video_url'), false)
            ->assertDontSee('data-vision-track', false)
            ->assertDontSee('data-vision-program', false);

        $content = $response->getContent();
        preg_match_all('/<img(?=[^>]*data-vision-art)[^>]*\ssrc=/', $content, $eagerVisionImages);
        expect(substr_count($content, 'data-vision-panel='))->toBe(3)
            ->and(substr_count($content, 'data-vision-visual='))->toBe(3)
            ->and(substr_count($content, 'data-vision-background-layer='))->toBe(2)
            ->and(substr_count($content, 'data-vision-art'))->toBe(3)
            ->and(substr_count($content, 'data-vision-video-preview'))->toBe(2)
            ->and(substr_count($content, 'data-lazy-src='))->toBe(1)
            ->and(count($eagerVisionImages[0]))->toBe(0)
            ->and(substr_count(
                $content,
                'media.almustaqbal.sch.id/site/vision/vision-paper-'
            ))->toBe(1)
            ->and($content)->not->toContain('/media/home/vision-paper-')
            ->and(strpos($content, 'id="visi-misi"'))
            ->toBeLessThan(strpos($content, 'id="program"'));

        if ($locale === 'ar') {
            expect($content)->toContain('صلى الله عليه وسلم')->not->toContain('ﷺ');
        }
    }
});

it('keeps About Vision and Mission body copy readable without enlarging the headings', function (): void {
    $base = file_get_contents(resource_path('css/pages/welcome-vision-waapi/base.css'));
    $enhanced = file_get_contents(resource_path('css/pages/welcome-vision-waapi/enhanced.css'));

    expect($base)
        ->toContain('font-size: clamp(1.08rem, 1.55vw, 1.28rem)')
        ->toContain('color: rgb(21 21 21 / .84)')
        ->toContain('font-size: clamp(.92rem, .25vw + .87rem, 1rem)')
        ->toContain('color: rgb(21 21 21 / .78)')
        ->toContain('font-size: clamp(2.5rem, 7vw, 5.8rem)')
        ->and($enhanced)
        ->toContain('font-size: clamp(.92rem, .3vw + .86rem, 1rem)')
        ->toContain('font-size: clamp(3rem, 4.6vw, 5.7rem)');
});

it('provides one configurable gradual Vision background compositor', function (): void {
    $timeline = file_get_contents(resource_path('js/surfaces/home/vision-story/timeline.js'));
    $compositor = file_get_contents(
        resource_path('js/surfaces/home/vision-story/background-compositor.js'),
    );
    $background = file_get_contents(
        resource_path('css/pages/welcome-vision-waapi/background.css'),
    );
    $blade = file_get_contents(
        resource_path('views/home/sections/vision-mission.blade.php'),
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
        ->toContain('--vision-about-pattern: none')
        ->toContain('--vision-vision-pattern: none')
        ->toContain('--vision-mission-pattern: none')
        ->not->toContain('/media/seed/hero/')
        ->toContain('radial-gradient(circle at 50% 42%')
        ->toContain('var(--vision-state-pattern)')
        ->toContain('filter: blur(var(--vision-state-diffusion))')
        ->and($blade)
        ->toContain("config('media.static.ornaments.geometry_32')")
        ->toContain("config('media.static.ornaments.geometry_33')");
});

it('uses a native pinned mask reveal without owning document scroll', function (): void {
    $controller = file_get_contents(resource_path('js/surfaces/home/vision-story/controller.js'));
    $preparation = file_get_contents(resource_path('js/surfaces/home/vision-story/preparation.js'));
    $timeline = file_get_contents(resource_path('js/surfaces/home/vision-story/timeline.js'));
    $enhanced = file_get_contents(resource_path('css/pages/welcome-vision-waapi/enhanced.css'));

    expect($controller)
        ->toContain("matchMedia('(min-width: 1024px)')")
        ->toContain("rootMargin: '0px 0px -5% 0px'")
        ->toContain('current += (target - current) * alpha')
        ->not->toContain('window.scrollTo')
        ->not->toContain('programStoryTravel')
        ->not->toContain("new CustomEvent('vision:layout')")
        ->and($preparation)
        ->toContain("[data-vision-art][data-lazy-src]")
        ->toContain("image.dataset.lazyHydrated = '1'")
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
