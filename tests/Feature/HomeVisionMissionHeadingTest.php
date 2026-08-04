<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the localized WAAPI Vision and Mission story contract', function (): void {
    foreach (['id', 'en', 'ar'] as $locale) {
        $response = $this
            ->withSession(['locale' => $locale])
            ->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('id="visi-misi"', false)
            ->assertSee('data-vision-story', false)
            ->assertSee('data-vision-editorial', false)
            ->assertSee('data-vision-divider', false)
            ->assertSee('data-vision-mission-list', false)
            ->assertSee('resources/css/pages/welcome-vision-waapi.css', false)
            ->assertSee('resources/js/pages/welcome-vision-story.js', false)
            ->assertDontSee('data-story-root', false)
            ->assertDontSee('data-story-scene', false)
            ->assertDontSee('data-story-text', false)
            ->assertDontSee('direction-story', false)
            ->assertDontSee('story-unit', false);

        $content = $response->getContent();

        expect(substr_count($content, 'data-vision-story'))->toBe(1)
            ->and(substr_count($content, 'data-vision-panel'))->toBe(10)
            ->and(substr_count($content, 'data-vision-panel-kind="vision"'))->toBe(1)
            ->and(substr_count($content, 'data-vision-panel-kind="mission"'))->toBe(4)
            ->and(substr_count($content, 'data-vision-art'))->toBe(4)
            ->and(substr_count($content, 'id="vision-mission-title-'))->toBe(4);

        if ($locale === 'ar') {
            $storyStart = strpos($content, 'id="visi-misi"');
            $storyEnd = strpos($content, '</section>', $storyStart);
            $story = substr($content, $storyStart, $storyEnd - $storyStart);

            expect($story)
                ->toContain('data-vision-mission-honorific')
                ->toContain('صلى الله عليه وسلم')
                ->not->toContain('ﷺ');
        }
    }
});
