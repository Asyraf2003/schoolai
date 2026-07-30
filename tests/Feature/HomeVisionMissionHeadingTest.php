<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the vision mission heading as one standalone word sequence for every locale', function (): void {
    $titles = [
        'id' => 'Arah Visi dan Misi',
        'en' => 'Direction, Vision and Mission',
        'ar' => 'التوجه والرؤية والرسالة',
    ];

    foreach ($titles as $locale => $title) {
        app()->setLocale($locale);

        $response = $this
            ->withSession(['locale' => $locale])
            ->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('data-vision-mission', false)
            ->assertSee('data-vision-mission-heading', false)
            ->assertSee('aria-label="'.$title.'"', false);

        $content = $response->getContent();
        $sectionStart = strpos($content, 'data-vision-mission');
        $sectionEnd = $sectionStart === false ? false : strpos($content, '</section>', $sectionStart);

        expect($sectionStart)->not->toBeFalse()
            ->and($sectionEnd)->not->toBeFalse();

        $sectionHtml = substr($content, $sectionStart, $sectionEnd - $sectionStart);
        $wordCount = count(preg_split('/\s+/u', $title, -1, PREG_SPLIT_NO_EMPTY) ?: []);

        expect(substr_count($sectionHtml, 'data-vision-word'))->toBe($wordCount)
            ->and($sectionHtml)->not->toContain('data-editorial-heading')
            ->and($sectionHtml)->not->toContain('welcome-editorial-heading__line--bottom');
    }
});
