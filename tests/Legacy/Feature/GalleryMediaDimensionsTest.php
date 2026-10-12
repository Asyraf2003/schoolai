<?php

use App\Models\GalleryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('public');

    $this->actingAs(User::query()->forceCreate([
        'name' => 'Gallery Dimension Admin',
        'email' => 'gallery-dimensions@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'role' => User::ROLE_ADMIN,
    ]));
});

it('persists intrinsic photo dimensions when gallery media is ingested', function (): void {
    $this->post(route('admin.galeri.store'), [
        'title_id' => 'Foto Landscape',
        'type' => 'photo',
        'category_id' => 'Kegiatan',
        'media_file' => UploadedFile::fake()->image('landscape.jpg', 1600, 900),
        'is_published' => '1',
        'show_on_homepage' => '1',
        'show_on_gallery_page' => '1',
        'published_at' => now()->format('Y-m-d H:i:s'),
    ])->assertRedirect();

    $item = GalleryItem::query()->where('title_id', 'Foto Landscape')->firstOrFail();

    expect($item->media_width)->toBe(1600)
        ->and($item->media_height)->toBe(900);

    $this->put(route('admin.galeri.update', $item), [
        'title_id' => 'Foto Portrait',
        'type' => 'photo',
        'category_id' => 'Kegiatan',
        'media_file' => UploadedFile::fake()->image('portrait.jpg', 900, 1600),
        'is_published' => '1',
        'show_on_homepage' => '1',
        'show_on_gallery_page' => '1',
        'published_at' => now()->format('Y-m-d H:i:s'),
    ])->assertRedirect(route('admin.galeri.show', $item));

    $item->refresh();

    expect($item->media_width)->toBe(900)
        ->and($item->media_height)->toBe(1600);
});

it('renders homepage gallery photos from item dimensions instead of a shared hardcoded ratio', function (): void {
    $blade = file_get_contents(resource_path('views/home/sections/gallery-depth.blade.php'));
    $builder = file_get_contents(app_path('Http/Controllers/Concerns/BuildsHomeArticlesAndGallery.php'));

    expect($blade)
        ->not->toContain('width="1400"')
        ->not->toContain('height="1050"')
        ->toContain("\$item['media_width']")
        ->toContain("\$item['media_height']")
        ->and($builder)
        ->toContain("'media_width' => \$item->media_width")
        ->toContain("'media_height' => \$item->media_height");

    $html = View::make('home.sections.gallery-depth', [
        'galleryHeading' => 'Gallery',
        'gallerySection' => [
            'items' => [[
                'title' => 'Aula',
                'caption' => '',
                'type' => 'photo',
                'media_url' => 'https://media.almustaqbal.sch.id/site/school-life/aula-v1.webp',
                'thumbnail_url' => 'https://media.almustaqbal.sch.id/site/school-life/aula-v1.webp',
                'media_width' => 1920,
                'media_height' => 1200,
                'is_direct_video' => false,
            ]],
            'cta' => [],
        ],
    ])->render();

    expect($html)
        ->toContain('width="1920"')
        ->toContain('height="1200"');
});

it('renders the existing video thumbnail as a static homepage poster without eager video sources', function (): void {
    foreach (['https://media.almustaqbal.sch.id/gallery/poster.webp', ''] as $poster) {
        $html = View::make('home.sections.gallery-depth', [
            'galleryHeading' => 'Gallery',
            'gallerySection' => [
                'items' => [[
                    'title' => 'School activity',
                    'caption' => '',
                    'type' => 'video',
                    'media_url' => 'https://media.almustaqbal.sch.id/gallery/activity.mp4',
                    'thumbnail_url' => $poster,
                    'is_direct_video' => true,
                ]],
                'cta' => [],
            ],
        ])->render();

        expect($html)->toContain('preload="none"')
            ->toContain('data-gallery-video-src=')
            ->not->toMatch('/\s+src="https:\/\/media\.almustaqbal\.sch\.id\/gallery\/activity\.mp4"/');

        if ($poster !== '') {
            expect($html)->toContain('poster="'.$poster.'"');
        } else {
            expect($html)->not->toContain('poster=');
        }
    }
});
