<?php

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('escapes database content rendered on public article cards', function (): void {
    Article::query()->create([
        'title_id' => '<script>alert(1)</script>',
        'description_id' => '\"><img src=x onerror=alert(1)>',
        'thumbnail_url' => '/images/placeholder.webp',
        'link_id' => 'https://example.com/article',
        'author' => 'javascript:alert(1)',
        'published_at' => now(),
    ]);

    $content = $this->get(route('artikel'))
        ->assertOk()
        ->getContent();

    expect($content)
        ->toContain('&lt;script&gt;alert(1)&lt;/script&gt;')
        ->toContain('&lt;img src=x onerror=alert(1)&gt;')
        ->not->toContain('<script>alert(1)</script>')
        ->not->toContain('<img src=x onerror=alert(1)>');
});

it('keeps the gallery lightbox free from HTML string injection sinks', function (): void {
    $javascript = file_get_contents(resource_path('js/pages/welcome.js'));

    expect($javascript)
        ->not->toBeFalse()
        ->not->toContain('.innerHTML')
        ->not->toContain('insertAdjacentHTML')
        ->not->toContain('document.write');
});
