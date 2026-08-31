<?php

/* REAL_GALLERY_CRUD_CONTROLLER_FINAL */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\MaintainsGalleryOrdering;
use App\Http\Controllers\Admin\Concerns\ManagesGalleryItems;
use App\Http\Controllers\Admin\Concerns\NormalizesGalleryVideo;
use App\Http\Controllers\Admin\Concerns\RestoresAndOrdersGalleryItems;
use App\Http\Controllers\Admin\Concerns\ValidatesGalleryItems;
use App\Http\Controllers\Controller;
use App\Models\GalleryItem;

final class GalleryAdminController extends Controller
{
    use MaintainsGalleryOrdering;
    use ManagesGalleryItems;
    use NormalizesGalleryVideo;
    use RestoresAndOrdersGalleryItems;
    use ValidatesGalleryItems;

    private const MAX_ITEMS = GalleryItem::MAX_ITEMS;

    private const MAX_PHOTO_KB = GalleryItem::MAX_PHOTO_KB;

    public function __construct()
    {
        app()->setLocale('id');
    }
}
