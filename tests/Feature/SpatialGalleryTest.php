<?php

use App\Models\GalleryItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders real school fallback media as semantic links when the catalog is empty', function (string $locale): void {
    $response = $this->withSession(['locale' => $locale])->get(route('galeri'));

    $response->assertViewHas('stageItems', fn (array $items): bool => count($items) === 10
        && $items[0]['poster'] === config('media.static.school_life.labit'))
        ->assertSee('data-spatial-panel', false)->assertSee('data-gallery-viewer', false)
        ->assertSee(trans('home_parity.galeri.items.0.title', [], $locale));
    expect(GalleryItem::count())->toBe(0);
})->with(['id', 'en', 'ar']);

it('keeps stage media independent from archive pagination and excludes unsafe and deleted records', function (): void {
    foreach (range(1, 26) as $index) {
        GalleryItem::create(['type' => 'photo', 'media_url' => 'https://media.almustaqbal.sch.id/photo-'.$index.'.webp', 'alt_text' => 'School '.$index]);
    }
    GalleryItem::create(['type' => 'video', 'media_url' => 'https://media.almustaqbal.sch.id/film.mp4',
        'thumbnail_url' => 'https://media.almustaqbal.sch.id/poster.webp', 'alt_text' => '<script>unsafe caption</script>']);
    GalleryItem::create(['media_url' => 'javascript:alert(1)', 'alt_text' => 'Unsafe URL']);
    $deleted = GalleryItem::create(['media_url' => 'https://media.almustaqbal.sch.id/deleted.webp']);
    $deleted->delete();

    $this->get(route('galeri', ['page' => 2]))
        ->assertViewHas('stageItems', fn (array $items): bool => count($items) === 27
            && $items[0]['type'] === 'video' && $items[0]['poster'] === 'https://media.almustaqbal.sch.id/poster.webp'
            && $items[26]['title'] === 'School 1')
        ->assertSee('&lt;script&gt;unsafe caption&lt;/script&gt;', false)
        ->assertDontSee('<script>unsafe caption</script>', false)
        ->assertDontSee('javascript:alert(1)', false)->assertDontSee('deleted.webp', false);
});
