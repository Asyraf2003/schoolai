<?php
/* REAL_GALLERY_CRUD_CONTROLLER_FINAL */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use App\Models\GalleryPageSection;
use App\Rules\SafeImageUpload;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

final class GalleryAdminController extends Controller
{
    use \App\Http\Controllers\Admin\Concerns\ManagesGalleryItems;
    use \App\Http\Controllers\Admin\Concerns\RestoresAndOrdersGalleryItems;
    use \App\Http\Controllers\Admin\Concerns\ValidatesGalleryItems;
    use \App\Http\Controllers\Admin\Concerns\NormalizesGalleryVideo;
    use \App\Http\Controllers\Admin\Concerns\MaintainsGalleryOrdering;

    private const MAX_ITEMS = GalleryItem::MAX_ITEMS;
    private const MAX_PHOTO_KB = GalleryItem::MAX_PHOTO_KB;

    public function __construct()
    {
        app()->setLocale('id');
    }






















































}

