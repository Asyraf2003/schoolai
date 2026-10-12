<?php

use App\Models\GalleryItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('promotes the five seeded Gallery rows to motion media in place', function (): void {
    $this->seed();

    $base = rtrim((string) config('media.public_url'), '/');
    $motion = [
        'Fasilitas Multimedia & Lab IT' => ['labit', 'it-v1.mp4'],
        'Mushallah' => ['mushalla', 'musola-v1.mp4'],
        'Aula Multifungsi' => ['aula', 'aula-v1.mp4'],
        'Kolam Renang' => ['renang', 'renang-v1.mp4'],
        'Taekwondo' => ['taekwondo', 'tekwondo-v1.mp4'],
    ];

    $ids = [];

    foreach ($motion as $title => [$legacyKey, $filename]) {
        $item = GalleryItem::query()->where('title_id', $title)->firstOrFail();
        $ids[$title] = $item->getKey();
        $item->forceFill([
            'type' => 'photo',
            'media_url' => $base.'/site/school-life/'.$legacyKey.'-v1.webp',
            'show_on_homepage' => $legacyKey !== 'taekwondo',
        ])->save();
    }

    GalleryItem::query()
        ->whereIn('title_id', [
            'Perpustakaan & Laboratorium',
            'Konseling Psikologis',
            'Kelas Orang Tua',
            'Layanan Kesehatan Gigi',
            'Sekolah Full Day',
        ])
        ->update(['show_on_homepage' => true]);

    $migration = require database_path(
        'migrations/2026_08_30_020000_promote_gallery_motion_media.php'
    );
    $migration->up();

    $homepage = GalleryItem::query()->homepage()->ordered()->get();

    expect($homepage)->toHaveCount(5)
        ->and($homepage->every(fn (GalleryItem $item): bool => $item->is_video))
        ->toBeTrue();

    foreach ($motion as $title => [, $filename]) {
        $item = GalleryItem::query()->where('title_id', $title)->firstOrFail();

        expect($item->getKey())->toBe($ids[$title])
            ->and($item->media_url)->toBe($base.'/gallery/media/'.$filename)
            ->and($item->show_on_homepage)->toBeTrue();
    }
});
