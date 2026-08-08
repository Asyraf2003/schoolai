<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders six localized kinetic Program cards and details', function (): void {
    $expected = [
        'id' => ['PROGRAM', 'Kelompok Bermain', 'Tahfidz Al-Qur’an', 'Literasi & Perpustakaan'],
        'en' => ['PROGRAM', 'Playgroup', 'Qur’an Memorization', 'Literacy & Library'],
        'ar' => ['البرامج', 'مجموعة اللعب', 'تحفيظ القرآن', 'القراءة والمكتبة'],
    ];

    foreach ($expected as $locale => $copy) {
        app()->setLocale($locale);
        $response = $this->withSession(['locale' => $locale])->get(route('home'));
        $response
            ->assertOk()
            ->assertSee('id="program"', false)
            ->assertSee('data-program-kinetic', false)
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

        expect(count($cards[0]))->toBe(6)
            ->and(count($triggers[0]))->toBe(6)
            ->and(count($details[0]))->toBe(6)
            ->and(substr_count($programSection, 'images.unsplash.com'))->toBe(12)
            ->and(substr_count($programSection, 'data-program-type-line'))->toBe(10)
            ->and($programSection)->not->toContain('data-program-sticky')
            ->and($programSection)->not->toContain('data-program-rail')
            ->and($programSection)->not->toContain('data-program-frame');
    }
});

it('implements the Codrops-inspired transition without GSAP or scroll hijacking', function (): void {
    $entry = file_get_contents(resource_path('js/pages/welcome/program-cards.js'));
    $controller = file_get_contents(resource_path('js/surfaces/home/program-journey/controller.js'));
    $motion = file_get_contents(resource_path('js/surfaces/home/program-journey/motion.js'));

    expect($entry)
        ->toContain('[data-program-kinetic]')
        ->and($controller)
        ->toContain("event.key === 'Escape'")
        ->toContain('integration.trapTab(event)')
        ->toContain('prefers-reduced-motion')
        ->not->toContain('scrollTo(')
        ->not->toContain('wheel')
        ->and($motion)
        ->toContain('element.animate')
        ->toContain('scale(2.7)')
        ->toContain('rotate(')
        ->toContain('translate3d')
        ->not->toContain('gsap');
});
