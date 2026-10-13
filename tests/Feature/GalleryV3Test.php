<?php

use App\Models\GalleryItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('paginates photos and films independently while retaining the other section positions', function (): void {
    foreach (range(1, 8) as $index) {
        GalleryItem::create(['type' => 'photo', 'media_url' => 'https://media.almustaqbal.sch.id/photo-'.$index.'.webp']);
    }
    foreach (range(1, 6) as $index) {
        GalleryItem::create(['type' => 'video', 'media_url' => 'https://media.almustaqbal.sch.id/video-'.$index.'.mp4']);
    }

    $response = $this->get(route('galeri', ['photo_page' => 2, 'video_page' => 1]));

    $response->assertOk()->assertViewHas('photos', fn ($photos) => $photos->currentPage() === 2
        && $photos->count() === 2 && $photos->first()->media_url === 'https://media.almustaqbal.sch.id/photo-2.webp')
        ->assertViewHas('videos', fn ($videos) => $videos->currentPage() === 1 && $videos->count() === 4)
        ->assertViewHas('items', fn ($items) => $items->total() === 14)
        ->assertSee('photo_page=2&amp;video_page=2#films', false);
});

it('keeps the exact landing logo and three gallery header controls in every locale', function (string $locale): void {
    $response = $this->withSession(['locale' => $locale])->get(route('galeri'));

    $response->assertOk()->assertSee('src="'.config('media.static.brand.logo_nav').'"', false)
        ->assertSee('data-gallery-sound', false)->assertSee(route('home'), false)
        ->assertSee(route('artikel'), false)->assertSee(trans('gallery_v3.stories_note', [], $locale))
        ->assertDontSee('data-menu-toggle', false);
})->with(['id', 'en', 'ar']);

it('loads the shared transition only on public content routes', function (): void {
    $this->get(route('galeri'))->assertSee('data-public-transition', false);
    $this->get(route('artikel'))->assertSee('data-public-transition', false);
    $this->get('/login')->assertDontSee('data-public-transition', false);
});
