<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders localized Vision and Mission content without owning Program copy', function (): void {
    $labels = [
        'id' => ['vision' => 'VISI', 'mission' => 'MISI'],
        'en' => ['vision' => 'VISION', 'mission' => 'MISSION'],
        'ar' => ['vision' => 'الرؤية', 'mission' => 'الرسالة'],
    ];
    $missionSnippets = [
        'id' => ['Membentuk generasi Islam berdasarkan', 'baik secara lokal maupun global.'],
        'en' => ['Nurturing a Muslim generation based on the', 'both locally and globally.'],
        'ar' => ['تنشئة جيل مسلم يستند إلى', 'على المستويين المحلي والعالمي.'],
    ];

    foreach (['id', 'en', 'ar'] as $locale) {
        app()->setLocale($locale);

        $response = $this
            ->withSession(['locale' => $locale])
            ->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('id="visi-misi"', false)
            ->assertSee('data-vision-story', false)
            ->assertSee('data-vision-track', false)
            ->assertSee('data-vision-intro', false)
            ->assertSee('data-vision-copy="vision"', false)
            ->assertSee('data-vision-copy="mission"', false)
            ->assertSee($labels[$locale]['vision'])
            ->assertSee($labels[$locale]['mission'])
            ->assertSee($missionSnippets[$locale][0])
            ->assertSee($missionSnippets[$locale][1])
            ->assertSee('data-vision-image-square', false)
            ->assertSee('data-vision-image-frame', false)
            ->assertSee('data-vision-image-stack', false)
            ->assertDontSee('data-vision-program', false)
            ->assertDontSee('data-program-origin', false)
            ->assertDontSee('vision-paper__program', false);

        $content = $response->getContent();

        expect(substr_count($content, 'data-vision-story'))->toBe(1)
            ->and(substr_count($content, 'data-vision-track'))->toBe(1)
            ->and(substr_count($content, 'data-vision-copy='))->toBe(2)
            ->and(substr_count($content, 'data-vision-typography='))->toBe(4)
            ->and(substr_count($content, 'data-vision-art'))->toBe(3)
            ->and(substr_count($content, 'media/home/vision-paper-'))->toBe(3)
            ->and(substr_count($content, 'data-vision-program'))->toBe(0)
            ->and(substr_count($content, 'data-program-origin'))->toBe(0);

        if ($locale === 'ar') {
            expect($content)->toContain('صلى الله عليه وسلم')->not->toContain('ﷺ');
        }
    }
});

it('keeps horizontal travel complete before the integrated Program phase begins', function (): void {
    $controller = file_get_contents(resource_path('js/surfaces/home/vision-story/controller.js'));
    $enhanced = file_get_contents(resource_path('css/pages/welcome-vision-waapi/enhanced.css'));

    expect($controller)
        ->toContain('track.scrollWidth - window.innerWidth')
        ->toContain('root.dataset.programStoryTravel')
        ->toContain('window.innerHeight + horizontalTravel + programTravel')
        ->toContain('distance = wide.matches')
        ->toContain("new CustomEvent('vision:layout')")
        ->and($enhanced)
        ->not->toContain('430svh')
        ->not->toContain('vision-paper__program');
});
