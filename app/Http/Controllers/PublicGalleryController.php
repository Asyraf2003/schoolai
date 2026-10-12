<?php

namespace App\Http\Controllers;

use App\Models\GalleryItem;
use App\Models\InstagramGalleryItem;
use App\Services\PpdbAccess;
use Illuminate\View\View;

final class PublicGalleryController extends Controller
{
    public function __invoke(PpdbAccess $ppdb): View
    {
        return view('pages.gallery', [
            'items' => GalleryItem::query()->orderByDesc('id')->paginate(24),
            'instagramPosts' => InstagramGalleryItem::query()->orderByDesc('posted_at')->paginate(18, ['*'], 'ig_page'),
            'ppdbUrl' => $ppdb->current()->publicRegistrationUrl(),
        ]);
    }
}
