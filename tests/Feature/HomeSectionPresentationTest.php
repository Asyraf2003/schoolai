<?php

use App\Models\Article;
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

it('renders localized Home presentation copy and final Arabic mission text', function (): void {
    Article::query()->create([
        'article_source' => Article::SOURCE_NATIVE,
        'article_status' => Article::STATUS_PUBLISHED,
        'slug' => 'home-presentation-proof',
        'title_id' => 'Bukti presentasi Home',
        'title_en' => 'Home presentation proof',
        'description_id' => 'Bukti shaping artikel Home.',
        'description_en' => 'Home article shaping proof.',
        'thumbnail_url' => '/storage/articles/home-presentation-proof.jpg',
        'link_id' => '/artikel/home-presentation-proof',
        'link_en' => '/artikel/home-presentation-proof',
        'author' => 'SchoolAI',
        'published_at' => now(),
    ]);

    $expected = [
        'id' => ['AREA GALERI', 'ARTIKEL', 'Mau lihat artikel selengkapnya?'],
        'en' => ['AREA OF GALLERY', 'ARTICLES', 'Want to explore more articles?'],
        'ar' => ['مساحة المعرض', 'المقالات', 'هل ترغب في استكشاف المزيد من المقالات؟'],
    ];

    foreach ($expected as $locale => $copy) {
        $response = $this->withSession(['locale' => $locale])->get(route('home'));

        $response
            ->assertOk()
            ->assertSee($copy[0])
            ->assertSee($copy[1])
            ->assertSee($copy[2]);

        if ($locale === 'ar') {
            $response
                ->assertSee('صلى الله عليه وسلم')
                ->assertDontSee('ﷺ');
        }
    }
});

it('preserves depth Gallery preset cycling journey and closing media shaping', function (): void {
    $items = collect(range(1, 7))->map(fn (int $index): array => [
        'title' => 'Gallery '.$index,
        'caption' => 'Caption '.$index,
        'thumbnail_url' => '/gallery-'.$index.'.jpg',
    ])->all();

    $html = View::make('home.sections.gallery-depth', [
        'gallerySection' => [
            'items' => $items,
            'section_subtitle' => 'Closing copy',
            'cta' => ['href' => '/galeri', 'label' => 'Explore'],
        ],
    ])->render();

    expect($html)
        ->toContain('data-depth-gallery-end-steps="1"')
        ->toContain('--depth-gallery-count: 8')
        ->toContain('data-position-x="-0.9"')
        ->toContain('data-position-x="0.8"')
        ->and(substr_count($html, 'depth-gallery__end-media--'))->toBe(2)
        ->and(substr_count($html, 'data-depth-gallery-source'))->toBe(7);
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
        ->toContain('Final composed hero')
        ->not->toContain('data-hero-previous')
        ->not->toContain('data-hero-next');
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
