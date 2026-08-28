<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;

uses(RefreshDatabase::class);

it('keeps all H5 group two Blade owners free of PHP shaping', function (): void {
    $files = [
        resource_path('views/welcome.blade.php'),
        resource_path('views/home/sections/featured-programs.blade.php'),
        resource_path('views/home/sections/vision-mission.blade.php'),
        resource_path('views/home/sections/gallery.blade.php'),
        resource_path('views/home/sections/gallery-depth.blade.php'),
        resource_path('views/home/sections/school-values.blade.php'),
        resource_path('views/home/sections/articles.blade.php'),
        resource_path('views/home/partials/editorial-section-heading.blade.php'),
    ];

    foreach ($files as $file) {
        $source = file_get_contents($file);

        expect($source, basename($file))
            ->not->toContain('@php')
            ->not->toContain('@endphp')
            ->not->toContain('<?php');
    }
});

it('renders localized Home facilities presentation and final Arabic mission text', function (): void {
    $expected = [
        'id' => ['heading' => 'FASILITAS KAMI', 'facility' => 'Fasilitas Multimedia & Lab IT'],
        'en' => ['heading' => 'OUR FACILITIES', 'facility' => 'Multimedia Facilities & IT Lab'],
        'ar' => ['heading' => 'مرافقنا', 'facility' => 'مرافق الوسائط المتعددة ومختبر تقنية المعلومات'],
    ];

    foreach ($expected as $locale => $copy) {
        $response = $this->withSession(['locale' => $locale])->get(route('home'));

        $response
            ->assertOk()
            ->assertSee($copy['heading'])
            ->assertSee($copy['facility']);

        expect(substr_count($response->getContent(), 'data-gallery-story-item'))->toBe(9);

        if ($locale === 'ar') {
            $response
                ->assertSee('صلى الله عليه وسلم')
                ->assertDontSee('ﷺ');
        }
    }

    $welcome = file_get_contents(resource_path('views/welcome.blade.php'));
    expect($welcome)
        ->toContain("@include('home.sections.articles')")
        ->toContain('welcome-article-showcase.css')
        ->not->toContain('welcome-article-story.css');
});

it('preserves Gallery preset shaping while rendering the alternating editorial stream', function (): void {
    $items = collect(range(1, 7))->map(fn (int $index): array => [
        'title' => 'Gallery '.$index,
        'caption' => 'Caption '.$index,
        'thumbnail_url' => '/gallery-'.$index.'.jpg',
    ])->all();

    $html = View::make('home.sections.gallery-depth', [
        'galleryHeading' => 'Gallery',
        'gallerySection' => [
            'items' => $items,
            'cta' => ['href' => '/galeri', 'label' => 'Explore'],
        ],
    ])->render();

    expect($html)
        ->toContain('data-gallery-story')
        ->toContain('--gallery-story-count: 7')
        ->toContain('gallery-story__item--right')
        ->toContain('gallery-story__item--left')
        ->toContain('data-gallery-background')
        ->toContain('data-gallery-story-media')
        ->toContain('data-gallery-story-copy')
        ->toContain('data-depth-gallery-end-link')
        ->and(substr_count($html, 'data-gallery-story-item'))->toBe(7)
        ->and(substr_count($html, 'data-depth-gallery-end-link'))->toBe(1);
});

it('shapes the final hero collection at the included section boundary', function (): void {
    $html = View::make('home.sections.hero', [
        'hero' => [
            'slide_label' => 'Frame :current of :total',
            'slides' => [[
                'type' => 'image',
                'render_type' => 'image',
                'title' => 'Final composed hero',
                'media_url' => '/media/home/final-hero.webp',
                'media_alt' => 'Final hero',
                'focal_position' => 'center center',
                'overlay_strength' => '.4',
                'cta' => [],
            ]],
        ],
    ])->render();

    expect($html)
        ->toContain('aria-label="Frame 1 of 1"')
        ->toContain('data-hero-mode="opening"')
        ->toContain('Final composed hero')
        ->not->toContain('data-hero-previous')
        ->not->toContain('data-hero-next')
        ->not->toContain('data-hero-dot');
});

it('preserves the latent editorial heading split contract', function (): void {
    $html = View::make('home.partials.editorial-section-heading', [
        'title' => 'One Two Three Four Five',
        'description' => 'One Two Three Four Five Six Seven',
    ])->render();

    expect($html)
        ->toContain('One Two Three')
        ->toContain('Four Five')
        ->and(substr_count($html, 'welcome-editorial-heading__description-line'))
        ->toBe(3);
});
