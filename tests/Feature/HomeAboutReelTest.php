<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the localized about reel contract without legacy statistics markup', function (): void {
    $copy = [
        'id' => [
            'line_one' => 'Gagasan Berani,',
            'line_two' => 'Dihidupkan Bersama',
            'cta' => 'Arah Pendidikan Kami',
            'media' => 'Lihat Perjalanan Kami',
        ],
        'en' => [
            'line_one' => 'Bold Ideas,',
            'line_two' => 'Brought to Life',
            'cta' => 'Our Approach',
            'media' => 'Explore Our Journey',
        ],
        'ar' => [
            'line_one' => 'أفكار جريئة،',
            'line_two' => 'نحوّلها إلى واقع',
            'cta' => 'نهجنا التعليمي',
            'media' => 'اكتشف رحلتنا',
        ],
    ];

    foreach ($copy as $locale => $expected) {
        $response = $this
            ->withSession(['locale' => $locale])
            ->get(route('home'));

        $response
            ->assertOk()
            ->assertViewHas('stats', fn (array $stats): bool => $stats !== [])
            ->assertSee('id="tentang"', false)
            ->assertSee('class="about-reel"', false)
            ->assertSee('data-about-reel-track', false)
            ->assertSee('aria-labelledby="about-reel-title"', false)
            ->assertSee($expected['line_one'])
            ->assertSee($expected['line_two'])
            ->assertSee($expected['cta'])
            ->assertSee($expected['media'])
            ->assertSee('href="#visi-misi"', false)
            ->assertSee('pathLength="1"', false)
            ->assertDontSee('about-stats-story', false)
            ->assertDontSee('data-about-stats-item', false)
            ->assertDontSee('about-stats-story__tv-neck', false);

        expect($response->getContent())
            ->toMatch('/<video\b(?=[^>]*\bdata-about-reel-video\b)(?![^>]*\bautoplay\b)[^>]*>/s');
    }
});
