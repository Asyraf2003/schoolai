<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders eight localized featured Program cards and a full Islamic word field', function (): void {
    $expected = [
        'id' => ['Program Kami', 'Tahfidz Al-Qur’an', 'Khitobah (Public Speaking)', 'Small Class Concept', 'AKHLAKUL KARIMAH'],
        'en' => ['Our Programs', 'Qur’an Memorization', 'Khitobah (Public Speaking)', 'Small Class Concept', 'NOBLE CHARACTER'],
        'ar' => ['برامجنا', 'تحفيظ القرآن', 'الخطابة (التحدث أمام الجمهور)', 'مفهوم الصفوف الصغيرة', 'مكارم الأخلاق'],
    ];
    $deferred = [
        'id' => ['PAUD Tahfidz / Kelompok Bermain', 'Market Day (Healthy Shopping)'],
        'en' => ['Tahfidz Early Childhood / Playgroup', 'Market Day (Healthy Shopping)'],
        'ar' => ['الطفولة المبكرة لتحفيظ القرآن / مجموعة اللعب', 'يوم السوق (التسوق الصحي)'],
    ];
    $backs = ['id' => 'Kembali', 'en' => 'Back', 'ar' => 'العودة'];

    foreach ($expected as $locale => $copy) {
        app()->setLocale($locale);
        $response = $this->withSession(['locale' => $locale])->get(route('home'));
        $response->assertOk()->assertSee('id="program"', false)->assertSee($backs[$locale]);
        foreach ($copy as $text) {
            $response->assertSee($text);
        }

        $content = $response->getContent();
        preg_match('/<section[^>]+id="program".*?<\/section>/s', $content, $section);
        $programSection = $section[0] ?? '';
        preg_match_all('/\sdata-program-card(?:\s|>)/', $programSection, $cards);
        preg_match_all('/\sdata-program-open(?:\s|>)/', $programSection, $triggers);
        preg_match_all('/\sdata-program-detail(?:\s|>)/', $programSection, $details);
        preg_match_all('/\sdata-program-back(?:\s|>)/', $programSection, $backControls);
        preg_match_all('/\sdata-title-scale="(?:short|medium|long)"/', $programSection, $titleScales);
        preg_match_all('/\sdata-program-handoff-step="\d+"/', $programSection, $handoffSteps);
        preg_match_all('/\sdata-program-type(?:\s|>)/', $programSection, $typeFields);

        expect(count($cards[0]))->toBe(8)
            ->and(count($triggers[0]))->toBe(8)
            ->and(count($details[0]))->toBe(8)
            ->and(count($backControls[0]))->toBe(8)
            ->and(count($titleScales[0]))->toBe(8)
            ->and(count($handoffSteps[0]))->toBe(11)
            ->and(count($typeFields[0]))->toBe(1)
            ->and($programSection)->not->toContain('images.unsplash.com')
            ->and(substr_count($programSection, '/site/school-life/'))->toBe(16)
            ->and(substr_count($programSection, 'data-program-detail-image-wrap'))->toBe(8)
            ->and(substr_count($programSection, 'data-program-type-line'))->toBe(20)
            ->and(substr_count($programSection, 'class="program-kinetic__detail-description"'))->toBe(8)
            ->and($programSection)->not->toContain($deferred[$locale][0])
            ->and($programSection)->not->toContain($deferred[$locale][1])
            ->and($programSection)->not->toContain('program-kinetic__detail-number')
            ->and($programSection)->not->toContain('program-kinetic__detail-eyebrow')
            ->and($programSection)->not->toContain('program-kinetic__detail-intro')
            ->and($programSection)->not->toContain('program-kinetic__detail-next')
            ->and($programSection)->not->toContain('data-program-sticky')
            ->and($programSection)->not->toContain('data-program-rail');
    }
});

it('uses Codrops GSAP timing and detail media reveal', function (): void {
    $controller = file_get_contents(resource_path('js/surfaces/home/program-journey/controller.js'));
    $gsapController = file_get_contents(resource_path('js/surfaces/home/program-journey/gsap-controller.js'));
    $geometry = file_get_contents(resource_path('js/surfaces/home/program-journey/geometry.js'));
    $motion = file_get_contents(resource_path('js/surfaces/home/program-journey/motion.js'));

    expect($controller)
        ->toContain("from './gsap-controller.js'")
        ->not->toContain('scrollTo(')
        ->not->toContain('wheel')
        ->and($gsapController)
        ->toContain("addLabel('typeTransition', 0.3)")
        ->toContain('parts.imageWrap')
        ->toContain('parts.image')
        ->toContain('dom.backs.forEach')
        ->toContain('parts.back?.focus')
        ->not->toMatch('/\bdom\.back\b/')
        ->not->toContain('scrollTo(')
        ->not->toContain('wheel')
        ->and($geometry)
        ->toContain("backs: [...root.querySelectorAll('[data-program-back]')]")
        ->toContain('.program-kinetic__back, .program-kinetic__detail-copy h3')
        ->and($motion)
        ->toContain('gsap@3.7.1')
        ->toContain('scale: 2.7')
        ->toContain('stagger: 0.04')
        ->toContain('opacity: this.restOpacity')
        ->not->toContain('opacity: 0.05');
});

it('keeps Program controls functional while GSAP loads, fails, or is opening', function (): void {
    $controller = file_get_contents(resource_path('js/surfaces/home/program-journey/controller.js'));
    $gsapController = file_get_contents(resource_path('js/surfaces/home/program-journey/gsap-controller.js'));
    $rail = file_get_contents(resource_path('css/pages/welcome/program-journey/rail.css'));

    expect($controller)
        ->toContain('pendingTrigger')
        ->toContain('replayPending')
        ->toContain('cleanup = mountReduced(dom, integration);')
        ->and($gsapController)
        ->toContain('activeTimeline?.kill()')
        ->toContain('if (isAnimating && opening)')
        ->and($rail)
        ->toContain('.program-kinetic.gsap-failed .program-kinetic__trigger')
        ->toContain('pointer-events: auto')
        ->toContain('cursor: pointer');
});

it('keeps Codrops geometry while adapting long localized detail titles', function (): void {
    $blade = file_get_contents(resource_path('views/home/sections/featured-programs.blade.php'));
    $composer = file_get_contents(app_path('View/Composers/HomeProgramComposer.php'));
    $hud = file_get_contents(resource_path('css/pages/welcome/program-journey/hud.css'));
    $wide = file_get_contents(resource_path('css/pages/welcome/program-journey/wide.css'));
    $compact = file_get_contents(resource_path('css/pages/welcome/program-journey/compact.css'));

    expect($blade)
        ->toContain('class="program-kinetic__detail-copy"')
        ->toContain('class="program-kinetic__back"')
        ->toContain('&lt;&lt;&lt;')
        ->toContain('class="program-kinetic__detail-media"')
        ->toContain('data-title-scale="{{ $program[\'title_scale\'] }}"')
        ->not->toContain('mb_strlen')
        ->and($composer)
        ->toContain('HOMEPAGE_FEATURED_CODES')
        ->toContain('PROGRAM_MEDIA')
        ->toContain("'TQ'")
        ->toContain("'SC'")
        ->toContain('in_array(')
        ->toContain('Str::length')
        ->toContain("'short'")
        ->toContain("'medium'")
        ->toContain("'long'")
        ->and($hud)
        ->toContain('font-size: 8vw')
        ->toContain('font-variation-settings: "wght" 700')
        ->toContain('font-weight: 700')
        ->toContain('line-height: .85')
        ->toContain('font-size: 1rem')
        ->toContain('line-height: normal')
        ->toContain('border-radius: 17px 17px 0 0')
        ->and($wide)
        ->toContain('top: 20svh')
        ->toContain('height: 80svh')
        ->toContain('width: calc(38vw + 280px)')
        ->toContain('grid-template-rows: 10vw 2rem auto auto 1fr')
        ->toContain('grid-template-columns: 1.5rem 30% 1fr 1.5rem')
        ->toContain('h3[data-title-scale="short"]')
        ->toContain('font-size: 5.75vw')
        ->toContain('font-size: 4.75vw')
        ->toContain('text-wrap: balance')
        ->toContain('grid-column: 2 / 4')
        ->toContain('grid-row: 3')
        ->toContain('grid-column: 3')
        ->toContain('grid-row: 1 / 6')
        ->not->toContain('grid-template-rows: 10vw 2rem 12vw auto 1fr')
        ->and($compact)
        ->toContain('width: min(78vw, 31rem)')
        ->toContain('grid-row: 1')
        ->toContain('grid-row: 2');
});

it('replays the Program center split with breathing room and no horizontal heading shift', function (): void {
    $headingCss = file_get_contents(resource_path('css/pages/welcome/program-journey/heading.css'));
    $sharedHeadingCss = file_get_contents(resource_path('css/surfaces/home/section-display-heading.css'));
    $heading = file_get_contents(resource_path('js/surfaces/home/program-journey/heading.js'));

    expect($headingCss)
        ->toContain('section-display-heading.css')
        ->toContain('translate3d(0, 114%, 0)')
        ->toContain('translate3d(0, -114%, 0)')
        ->and($sharedHeadingCss)
        ->toContain('row-gap: .12em')
        ->toContain('margin-block: 0')
        ->toContain('line-height: .84')
        ->and($heading)
        ->toContain('intersectionRatio >= 0.16')
        ->toContain('threshold: [0, 0.16]')
        ->not->toContain('translateX');
});

it('keeps the exact eleven-step Vision to Program handoff', function (): void {
    $handoff = file_get_contents(resource_path('css/pages/welcome/program-journey/handoff.css'));
    expect($handoff)
        ->toContain('grid-template-rows: repeat(11')
        ->toContain('var(--handoff-a) 0 9px, transparent 9px 10px')
        ->toContain('var(--handoff-a) 0 5px, transparent 5px 10px')
        ->toContain('var(--handoff-a) 0 1px, transparent 1px 10px')
        ->toContain('--handoff-line: #fff');
});

it('keeps eight-card responsive geometry bounded across desktop tablet and mobile', function (): void {
    $wide = file_get_contents(resource_path('css/pages/welcome/program-journey/wide.css'));
    $compact = file_get_contents(resource_path('css/pages/welcome/program-journey/compact.css'));
    $formation = file_get_contents(resource_path('js/surfaces/home/program-journey/formation.js'));

    expect($wide)
        ->toContain('--program-card-base: 7svh')
        ->toContain('--program-card-interval: 9svh')
        ->toContain('grid-template-columns: repeat(4, minmax(0, 1fr))')
        ->toContain('nth-child(-n + 4)')
        ->toContain('nth-child(n + 5):nth-child(-n + 8)')
        ->toContain('.program-kinetic__card:nth-child(8)')
        ->and($compact)
        ->toContain('grid-template-columns: repeat(4, minmax(0, 1fr))')
        ->toContain('nth-child(4n + 2)')
        ->toContain('nth-child(4n + 3)')
        ->toContain('nth-child(4n)')
        ->toContain('grid-template-columns: repeat(2, minmax(0, 1fr))')
        ->toContain('last-child:nth-child(odd)')
        ->not->toContain('aspect-ratio: 4 / 5')
        ->and($formation)
        ->toContain('function readStaggerIndex(index, viewportWidth)')
        ->toContain('if (viewportWidth >= 1024) return index % 4')
        ->toContain('if (viewportWidth >= 640) return 0')
        ->toContain('return index % 2')
        ->not->toContain('return index % 3');
});
