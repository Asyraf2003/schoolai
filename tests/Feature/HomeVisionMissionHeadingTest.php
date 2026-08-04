<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders a localized semantic Vision and Mission fallback without legacy motion ownership', function (): void {
    foreach (['id', 'en', 'ar'] as $locale) {
        $response = $this
            ->withSession(['locale' => $locale])
            ->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('id="visi-misi"', false)
            ->assertSee('data-vision-mission-static', false)
            ->assertSee('data-vision-mission-list', false)
            ->assertSee('data-vision-mission-item', false)
            ->assertDontSee('data-story-root', false)
            ->assertDontSee('data-story-scene', false)
            ->assertDontSee('data-story-text', false)
            ->assertDontSee('data-story-effect', false)
            ->assertDontSee('direction-story', false)
            ->assertDontSee('story-unit', false);

        $content = $response->getContent();

        expect(substr_count($content, 'data-vision-mission-static'))->toBe(1)
            ->and(substr_count($content, 'data-vision-mission-list'))->toBe(1)
            ->and(substr_count($content, 'data-vision-mission-item'))->toBe(4)
            ->and(substr_count($content, 'id="mission-title-'))->toBe(4);

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
