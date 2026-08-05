<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the localized desktop Vision and Mission paper story', function (): void {
    $labels = [
        'id' => ['vision' => 'VISI', 'mission' => 'MISI'],
        'en' => ['vision' => 'VISION', 'mission' => 'MISSION'],
        'ar' => ['vision' => 'الرؤية', 'mission' => 'الرسالة'],
    ];
    $missionSnippets = [
        'id' => [
            'Membentuk generasi Islam berdasarkan',
            'baik secara lokal maupun global.',
        ],
        'en' => [
            'Nurturing a Muslim generation based on the',
            'both locally and globally.',
        ],
        'ar' => [
            'تنشئة جيل مسلم يستند إلى',
            'على المستويين المحلي والعالمي.',
        ],
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
            ->assertSee('data-vision-mission-text', false)
            ->assertSee('data-vision-image-square', false)
            ->assertSee('data-vision-image-frame', false)
            ->assertSee('data-vision-image-stack', false)
            ->assertSee('data-vision-program', false)
            ->assertDontSee('vision-paper__mission-list', false)
            ->assertDontSee('vision-paper__mission-detail', false)
            ->assertDontSee('id="vision-mission-title-', false)
            ->assertDontSee('vision-paper__program-accent', false)
            ->assertDontSee('data-vision-panel-kind', false)
            ->assertDontSee('data-vision-outro', false)
            ->assertDontSee('data-vision-canvas', false)
            ->assertDontSee('data-story-root', false)
            ->assertDontSee('data-story-scene', false)
            ->assertDontSee('story-unit', false);

        $content = $response->getContent();

        expect(substr_count($content, 'data-vision-story'))->toBe(1)
            ->and(substr_count($content, 'data-vision-track'))->toBe(1)
            ->and(substr_count($content, 'data-vision-copy='))->toBe(2)
            ->and(substr_count($content, 'data-vision-art'))->toBe(3)
            ->and(substr_count($content, 'media/home/vision-paper-'))->toBe(3)
            ->and(substr_count($content, 'images.pexels.com'))->toBe(0)
            ->and(substr_count($content, 'vision-paper__mark--'))->toBe(4)
            ->and(substr_count($content, 'data-vision-mission-text'))->toBe(1)
            ->and(substr_count($content, 'data-vision-image-frame'))->toBe(1)
            ->and(substr_count($content, 'data-vision-image-stack'))->toBe(1)
            ->and(substr_count($content, 'data-vision-program'))->toBe(1);

        if ($locale === 'ar') {
            expect($content)
                ->toContain('صلى الله عليه وسلم')
                ->not->toContain('ﷺ');
        }
    }
});
