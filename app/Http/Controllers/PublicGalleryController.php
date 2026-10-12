<?php

namespace App\Http\Controllers;

use App\Models\GalleryItem;
use App\Models\InstagramGalleryItem;
use Illuminate\Contracts\View\View;

final class PublicGalleryController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.gallery', [
            'instagramPosts' => InstagramGalleryItem::query()
                ->orderByDesc('posted_at')->paginate(18),
            'schoolMedia' => GalleryItem::query()->latest()->limit(60)->get(),
        ]);
    }
}
