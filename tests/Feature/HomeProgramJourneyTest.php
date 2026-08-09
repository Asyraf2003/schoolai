<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders six localized kinetic Program cards and a full Islamic word field', function (): void {
    $expected = [
        'id' => ['Program Kami', 'Kelompok Bermain', 'Tahfidz Al-Qur’an', 'Literasi & Perpustakaan', 'AKHLAKUL KARIMAH'],
        'en' => ['Our Programs', 'Playgroup', 'Qur’an Memorization', 'Literacy & Library', 'NOBLE CHARACTER'],
        'ar' => ['برامجنا', 'مجموعة اللعب', 'تحفيظ القرآن', 'القراءة والمكتبة', 'مكارم الأخلاق'],
    ];
    $backs = ['id' => 'Kembali', 'en' => 'Back', 'ar' => 'العودة'];

    foreach ($expected as $locale => $copy) {
        app()->setLocale($locale);
        $response = $this->withSession(['locale' => $locale])->get(route('home'));
        $response
            ->assertOk()
            ->assertSee('id="program"', false)
            ->assertSee('data-program-kinetic', false)
            ->assertSee('data-program-handoff', false)
            ->assertSee('data-program-type', false)
            ->assertSee('data-program-heading', false)
            ->assertSee('data-program-cards', false)
            ->assertSee('data-program-detail-layer', false)
            ->assertSee('data-program-back', false)
            ->assertSee($backs[$locale]);

        foreach ($copy as $text) $response->assertSee($text);

        $content = $response->getContent();
        preg_match('/<section[^>]+id="program".*?<\/section>/s', $content, $section);
        $programSection = $section[0] ?? '';
        preg_match_all('/\sdata-program-card(?:\s|>)/', $programSection, $cards);
        preg_match_all('/\sdata-program-open(?:\s|>)/', $programSection, $triggers);
        preg_match_all('/\sdata-program-detail(?:\s|>)/', $programSection, $details);
        preg_match_all('/\sdata-program-handoff-step="\d+"/', $programSection, $handoffSteps);
        preg_match_all('/\sdata-program-type(?:\s|>)/', $programSection, $typeFields);

        expect(count($cards[0]))->toBe(6)
            ->and(count($triggers[0]))->toBe(6)
            ->and(count($details[0]))->toBe(6)
            ->and(count($handoffSteps[0]))->toBe(11)
            ->and(count($typeFields[0]))->toBe(1)
            ->and(substr_count($programSection, 'images.unsplash.com'))->toBe(6)
            ->and(substr_count($programSection, 'data-program-type-line'))->toBe(20)
            ->and(substr_count($programSection, 'class="program-kinetic__summary"'))->toBe(6)
            ->and(substr_count($programSection, 'class="program-kinetic__detail-description"'))->toBe(6)
            ->and($programSection)->not->toContain('program-kinetic__handoff-type')
            ->and($programSection)->not->toContain('class="program-kinetic section"')
            ->and($programSection)->not->toContain('class="program-kinetic__meta"')
            ->and($programSection)->not->toContain('class="program-kinetic__eyebrow"')
            ->and($programSection)->not->toContain('class="program-kinetic__next"')
            ->and($programSection)->not->toContain('program-kinetic__detail-number')
            ->and($programSection)->not->toContain('program-kinetic__detail-eyebrow')
            ->and($programSection)->not->toContain('program-kinetic__detail-intro')
            ->and($programSection)->not->toContain('program-kinetic__detail-next')
            ->and($programSection)->not->toContain('data-program-detail-image')
            ->and($programSection)->not->toContain('01—06')
            ->and($programSection)->not->toContain('data-program-sticky')
            ->and($programSection)->not->toContain('data-program-rail')
            ->and($programSection)->not->toContain('data-program-frame');
    }
});

it('uses Codrops GSAP timing and restores the CSS kinetic baseline after close', function (): void {
    $entry = file_get_contents(resource_path('js/pages/welcome/program-cards.js'));
    $controller = file_get_contents(resource_path('js/surfaces/home/program-journey/controller.js'));
    $motion = file_get_contents(resource_path('js/surfaces/home/program-journey/motion.js'));
    $license = base_path('docs/licenses/CODROPS_KINETIC_TYPE_PAGE_TRANSITION_MIT.md');

    expect($entry)
        ->toContain('[data-program-kinetic]')
        ->and($controller)
        ->toContain("event.key === 'Escape'")
        ->toContain('integration.trapTab(event)')
        ->toContain('prefers-reduced-motion')
        ->toContain("addLabel('typeTransition', 0.3)")
        ->not->toContain('parts.imageWrap')
        ->not->toContain('parts.image')
        ->not->toContain('scrollTo(')
        ->not->toContain('wheel')
        ->and($motion)
        ->toContain('gsap@3.7.1')
        ->toContain("ease: 'power2.inOut'")
        ->toContain('scale: 2.7')
        ->toContain('stagger: 0.04')
        ->toContain('this.restOpacity = Number.parseFloat')
        ->toContain('opacity: this.restOpacity')
        ->toContain("clearProps: 'opacity,transform'")
        ->toContain("clearProps: 'transform'")
        ->not->toContain('opacity: 0.05')
        ->and(file_exists($license))->toBeTrue();
});

it('keeps Program detail to Back, section-style title and one description', function (): void {
    $geometry = file_get_contents(resource_path('js/surfaces/home/program-journey/geometry.js'));
    $hud = file_get_contents(resource_path('css/pages/welcome/program-journey/hud.css'));
    $wide = file_get_contents(resource_path('css/pages/welcome/program-journey/wide.css'));
    $compact = file_get_contents(resource_path('css/pages/welcome/program-journey/compact.css'));

    expect($geometry)
        ->toContain('.program-kinetic__detail-copy h3, .program-kinetic__detail-description')
        ->not->toContain('imageWrap')
        ->not->toContain('data-program-detail-image')
        ->and($hud)
        ->toContain('font-variation-settings: "wght" 360')
        ->toContain('font-weight: 360')
        ->toContain('letter-spacing: -.055em')
        ->toContain('text-transform: uppercase')
        ->toContain('width: min(100%, 42rem)')
        ->not->toContain('.program-kinetic__detail-number')
        ->not->toContain('.program-kinetic__detail-image-wrap')
        ->not->toContain('.program-kinetic__detail-intro')
        ->not->toContain('.program-kinetic__detail-next')
        ->and($wide)->not->toContain('.program-kinetic__detail-image-wrap')
        ->and($compact)->not->toContain('.program-kinetic__detail-image-wrap');
});

it('reveals the Program heading from the center without horizontal heading shift', function (): void {
    $headingCss = file_get_contents(resource_path('css/pages/welcome/program-journey/heading.css'));
    $heading = file_get_contents(resource_path('js/surfaces/home/program-journey/heading.js'));
    $controller = file_get_contents(resource_path('js/surfaces/home/program-journey/controller.js'));
    $entryCss = file_get_contents(resource_path('css/pages/welcome/program-showcase-desktop.css'));

    expect($headingCss)
        ->toContain('text-transform: uppercase')
        ->toContain('translate3d(0, 108%, 0)')
        ->toContain('translate3d(0, -108%, 0)')
        ->toContain('is-program-heading-revealed')
        ->and($heading)
        ->toContain('IntersectionObserver')
        ->toContain("rootMargin: '0px 0px -12% 0px'")
        ->toContain('threshold: 0.16')
        ->not->toContain('translateX')
        ->and($controller)
        ->toContain("from './heading.js'")
        ->toContain('mountProgramHeading(root)')
        ->and($entryCss)
        ->toContain("@import './program-journey/heading.css'");
});

it('reveals the real Program field through an exact 11-step Vision handoff', function (): void {
    $vision = file_get_contents(resource_path('css/pages/welcome-vision-waapi/base.css'));
    $base = file_get_contents(resource_path('css/pages/welcome/program-journey/base.css'));
    $handoff = file_get_contents(resource_path('css/pages/welcome/program-journey/handoff.css'));
    $hud = file_get_contents(resource_path('css/pages/welcome/program-journey/hud.css'));

    expect($vision)
        ->toContain('background: #f4f1e9')
        ->and($base)
        ->toContain('--program-bg: #e7f5ff')
        ->toContain('--program-type: #397aa6')
        ->toContain('--program-type-opacity: .16')
        ->toContain('--program-type-size: clamp(7rem, 18.75vh, 15rem)')
        ->toContain('--program-handoff-height: clamp(18rem, 28vw, 30rem)')
        ->and($handoff)
        ->toContain('--handoff-a: #f4f1e9')
        ->toContain('--handoff-line: #fff')
        ->toContain('z-index: 4')
        ->toContain('background: transparent')
        ->toContain('grid-template-rows: repeat(11')
        ->toContain('var(--handoff-a) 0 9px, transparent 9px 10px')
        ->toContain('var(--handoff-a) 0 5px, transparent 5px 10px')
        ->toContain('var(--handoff-a) 0 1px, transparent 1px 10px')
        ->toContain('var(--handoff-line) var(--handoff-line-start) var(--handoff-cover)')
        ->not->toContain('--handoff-b:')
        ->not->toContain('--handoff-line: var(--program-type)')
        ->not->toContain('opacity: .42')
        ->not->toContain('.program-kinetic__handoff-type')
        ->not->toContain('filter: blur(')
        ->not->toContain('backdrop-filter: blur(')
        ->and($hud)
        ->toContain('bottom: 0')
        ->toContain('height: auto')
        ->toContain('min-height: calc(var(--program-handoff-height) + 100svh)')
        ->toContain('justify-content: space-between')
        ->toContain('bottom: auto')
        ->toContain('position: fixed')
        ->not->toContain('height: calc(var(--program-handoff-height) + 100svh)');
});

it('adapts Codrops card geometry to the six-card SchoolAI tiers', function (): void {
    $wide = file_get_contents(resource_path('css/pages/welcome/program-journey/wide.css'));
    $compact = file_get_contents(resource_path('css/pages/welcome/program-journey/compact.css'));

    expect($wide)
        ->toContain('--program-card-base: 7svh')
        ->toContain('--program-card-interval: 9svh')
        ->toContain('grid-template-columns: repeat(4, minmax(0, 1fr))')
        ->toContain('width: 96vw')
        ->toContain('grid-column: 3')
        ->toContain('grid-column: 4')
        ->toContain('calc(var(--program-card-base) + var(--program-card-interval) * 3)')
        ->toContain('aspect-ratio: 4 / 3')
        ->and($compact)
        ->toContain('grid-template-columns: repeat(3, minmax(0, 1fr))')
        ->toContain('--program-card-base: 3vw')
        ->toContain('--program-card-interval: 4vw')
        ->toContain('calc(var(--program-card-base) + var(--program-card-interval) * 2)')
        ->toContain('grid-template-columns: repeat(2, minmax(0, 1fr))')
        ->toContain('aspect-ratio: 4 / 3')
        ->not->toContain('aspect-ratio: 4 / 5');
});
