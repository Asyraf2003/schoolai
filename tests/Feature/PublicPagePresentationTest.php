<?php

use App\Models\PpdbSetting;
use App\Models\PpdbShowcaseItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

uses(RefreshDatabase::class);

afterEach(function (): void {
    app()->setLocale('id');
});

it('keeps all H5 group three Blade owners free of PHP shaping', function (): void {
    $files = [
        resource_path('views/pages/ppdb.blade.php'),
        resource_path('views/pages/ppdb/showcase.blade.php'),
        resource_path('views/pages/artikel.blade.php'),
        resource_path('views/pages/artikel-detail.blade.php'),
        resource_path('views/pages/galeri.blade.php'),
        resource_path('views/pages/partials/gallery-wall-card.blade.php'),
    ];

    foreach ($files as $file) {
        $source = file_get_contents($file);

        expect($source, basename($file))
            ->not->toContain('@php')
            ->not->toContain('@endphp')
            ->not->toContain('<?php');
    }

    $provider = file_get_contents(app_path('Providers/AppServiceProvider.php'));
    expect($provider)
        ->toContain("View::composer('pages.artikel-detail', PublicArticleDetailComposer::class)")
        ->toContain("View::composer('pages.partials.gallery-wall-card', GalleryWallCardComposer::class)");
});

it('preserves localized PPDB audiences initial selection and restarted numbering', function (): void {
    DB::table('ppdb_showcase_items')->delete();
    $setting = PpdbSetting::query()->firstOrFail();
    $setting->update([
        'registration_url' => 'https://apply.example.test/public-proof',
        'is_active' => true,
    ]);

    makePublicPpdbPresentationItem('parents', 1, 'Orang Tua Satu', 'Parent One', 'الوالدان واحد');
    makePublicPpdbPresentationItem('parents', 2, 'Orang Tua Dua', 'Parent Two', 'الوالدان اثنان');
    makePublicPpdbPresentationItem('school', 1, 'Sekolah Satu', 'School One', 'المدرسة واحد');

    $expected = [
        'id' => ['Orang Tua Satu', 'Sekolah Satu'],
        'en' => ['Parent One', 'School One'],
        'ar' => ['الوالدان واحد', 'المدرسة واحد'],
    ];

    foreach ($expected as $locale => $titles) {
        $content = $this->withSession(['locale' => $locale])
            ->get(route('ppdb'))
            ->assertOk()
            ->assertSee($titles[0])
            ->assertSee($titles[1])
            ->getContent();

        expect($content)
            ->toContain('data-active-audience="parents"')
            ->and(substr_count($content, 'class="ppdb-liftoff-step">01'))
            ->toBe(2)
            ->and(substr_count($content, 'class="ppdb-liftoff-step">02'))
            ->toBe(1);
    }

    PpdbShowcaseItem::query()->where('audience', 'parents')->delete();
    $schoolOnly = $this->withSession(['locale' => 'en'])
        ->get(route('ppdb'))
        ->assertOk()
        ->getContent();

    expect($schoolOnly)
        ->toContain('data-active-audience="school"')
        ->toMatch('/data-ppdb-liftoff-tab="parents"[^>]*disabled/s');
});

it('prepares localized Article aliases and fallback numbering', function (): void {
    foreach (['id', 'en', 'ar'] as $locale) {
        app()->setLocale($locale);
        $readLabel = __('pages.common.read_more');
        $html = View::make('pages.artikel', [
            'page' => [
                'title' => 'Article proof',
                'description' => 'Article presentation proof',
                'hero' => ['heading' => 'Article proof', 'search_placeholder' => 'Search'],
            ],
            'articles' => [[
                'href' => '/artikel/proof',
                'title' => 'Prepared article',
                'thumbnail_url' => null,
            ]],
            'categories' => [],
            'activeCategory' => null,
        ])->render();

        expect($html)
            ->toContain('aria-label="'.$readLabel.': Prepared article"')
            ->toContain('<span>01</span>')
            ->toContain('dir="'.($locale === 'ar' ? 'rtl' : 'ltr').'"');
    }
});

it('preserves Gallery database precedence and deterministic fallback cards', function (): void {
    $page = [
        'title' => 'Gallery proof',
        'description' => 'Gallery presentation proof',
        'hero' => ['heading' => 'Gallery proof', 'cards' => []],
        'wall' => ['title' => 'Gallery wall'],
        'items' => [['label' => 'Fallback Item', 'media_url' => null]],
    ];
    $databaseHtml = View::make('pages.galeri', [
        'page' => $page,
        'galleryItems' => [[
            'label' => 'Database Item',
            'media_url' => 'https://example.test/database.jpg',
        ]],
        'gallerySections' => [],
    ])->render();
    $fallbackHtml = View::make('pages.galeri', [
        'page' => $page,
        'galleryItems' => [],
        'gallerySections' => [],
    ])->render();

    expect($databaseHtml)
        ->toContain('data-gallery-title="Database Item"')
        ->not->toContain('data-gallery-title="Fallback Item"')
        ->and($fallbackHtml)
        ->toContain('data-gallery-title="Fallback Item"')
        ->toContain('data-gallery-is-video="0"')
        ->toContain('images.unsplash.com');
});

it('preserves Gallery video provider fallback presentation', function (): void {
    $html = View::make('pages.partials.gallery-wall-card', [
        'item' => [
            'type' => 'video',
            'label' => 'Video proof',
            'media_url' => 'https://www.youtube.com/embed/proof',
            'thumbnail_url' => null,
            'video_provider' => 'youtube',
        ],
    ])->render();

    expect($html)
        ->toContain('data-gallery-is-video="1"')
        ->toContain('social-video-cover--youtube')
        ->toContain(__('pages.common.media_video'))
        ->toContain(__('pages.common.play_media'));
});

function makePublicPpdbPresentationItem(
    string $audience,
    int $sortOrder,
    string $titleId,
    string $titleEn,
    string $titleAr,
): PpdbShowcaseItem {
    return PpdbShowcaseItem::query()->create([
        'audience' => $audience,
        'title_id' => $titleId,
        'title_en' => $titleEn,
        'title_ar' => $titleAr,
        'description_id' => 'Deskripsi '.$titleId,
        'description_en' => 'Description '.$titleEn,
        'description_ar' => 'وصف '.$titleAr,
        'media_type' => PpdbShowcaseItem::MEDIA_PHOTO,
        'media_url' => '/storage/ppdb/showcase/proof-'.$audience.'-'.$sortOrder.'.jpg',
        'sort_order' => $sortOrder,
    ]);
}
