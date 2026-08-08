<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders six localized kinetic Program cards and details', function (): void {
    $expected = [
        'id' => ['Program Kami', 'Kelompok Bermain', 'Tahfidz Al-Qur’an', 'Literasi & Perpustakaan'],
        'en' => ['Our Programs', 'Playgroup', 'Qur’an Memorization', 'Literacy & Library'],
        'ar' => ['برامجنا', 'مجموعة اللعب', 'تحفيظ القرآن', 'القراءة والمكتبة'],
    ];

    foreach ($expected as $locale => $copy) {
        app()->setLocale($locale);
        $response = $this->withSession(['locale' => $locale])->get(route('home'));
        $response
            ->assertOk()
            ->assertSee('id="program"', false)
            ->assertSee('data-program-kinetic', false)
            ->assertSee('data-program-handoff', false)
            ->assertSee('data-program-type', false)
            ->assertSee('data-program-cards', false)
            ->assertSee('data-program-detail-layer', false)
            ->assertSee('data-program-back', false);

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
            ->and(substr_count($programSection, 'images.unsplash.com'))->toBe(12)
            ->and(substr_count($programSection, 'data-program-type-line'))->toBe(10)
            ->and(substr_count($programSection, 'class="program-kinetic__summary"'))->toBe(6)
            ->and($programSection)->not->toContain('program-kinetic__handoff-type')
            ->and($programSection)->not->toContain('class="program-kinetic section"')
            ->and($programSection)->not->toContain('class="program-kinetic__meta"')
            ->and($programSection)->not->toContain('class="program-kinetic__eyebrow"')
            ->and($programSection)->not->toContain('class="program-kinetic__next"')
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

it('blends Vision into one continuous Program kinetic field with tapered crisp line geometry', function (): void {
    $base = file_get_contents(resource_path('css/pages/welcome/program-journey/base.css'));
    $handoff = file_get_contents(resource_path('css/pages/welcome/program-journey/handoff.css'));
    $hud = file_get_contents(resource_path('css/pages/welcome/program-journey/hud.css'));

    expect($base)
        ->toContain('--program-bg: #e7f5ff')
        ->toContain('--program-type: #397aa6')
        ->toContain('--program-type-opacity: .16')
        ->toContain('--program-type-size: clamp(7rem, 18.75vh, 15rem)')
        ->toContain('--program-handoff-height: clamp(18rem, 28vw, 30rem)')
        ->and($handoff)
        ->toContain('--handoff-b: var(--program-bg)')
        ->toContain('--handoff-line: var(--program-type)')
        ->toContain('height: var(--program-handoff-height)')
        ->toContain('grid-template-rows: repeat(11')
        ->toContain('repeating-linear-gradient(180deg')
        ->toContain('.program-kinetic__handoff-step::after')
        ->toContain('opacity: .42')
        ->toContain('transparent 0 1px, var(--handoff-line) 1px 10px')
        ->toContain('transparent 0 5px, var(--handoff-line) 5px 10px')
        ->toContain('transparent 0 9px, var(--handoff-line) 9px 10px')
        ->not->toContain('opacity: .62')
        ->not->toContain('.program-kinetic__handoff-type')
        ->not->toContain('filter: blur(')
        ->not->toContain('backdrop-filter: blur(')
        ->and($hud)
        ->toContain('height: calc(var(--program-handoff-height) + 100svh)')
        ->toContain('padding-top: calc(var(--program-handoff-height) / 11)')
        ->toContain('position: fixed')
        ->not->toContain('font-size: clamp(7rem, 18.75vh, 15rem)');
});
