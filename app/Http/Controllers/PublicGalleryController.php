<?php

namespace App\Http\Controllers;

use App\Models\GalleryItem;
use App\Models\InstagramGalleryItem;
use App\Services\PpdbAccess;
use App\View\Presenters\SiteNavbarPresenter;
use Illuminate\View\View;

final class PublicGalleryController extends Controller
{
    public function __invoke(PpdbAccess $ppdb, SiteNavbarPresenter $navbar): View
    {
        return view('pages.gallery', [
            'items' => GalleryItem::query()->orderByDesc('id')->paginate(24)->withQueryString()->fragment('all-posts'),
            'photos' => GalleryItem::query()->where('type', 'photo')->orderByDesc('id')
                ->paginate(6, ['*'], 'photo_page')->withQueryString()->fragment('photos'),
            'videos' => GalleryItem::query()->where('type', 'video')->orderByDesc('id')
                ->paginate(4, ['*'], 'video_page')->withQueryString()->fragment('films'),
            'collections' => GalleryItem::query()->where('type', 'photo')->orderByDesc('id')->limit(5)->get(),
            'navigation' => $navbar->present([]),
            'instagramPosts' => InstagramGalleryItem::query()->orderByDesc('posted_at')->paginate(18, ['*'], 'ig_page')->withQueryString()->fragment('instagram'),
            'ppdbUrl' => $ppdb->current()->publicRegistrationUrl(),
        ]);
    }
}
