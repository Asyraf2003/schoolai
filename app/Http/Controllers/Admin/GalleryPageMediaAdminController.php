<?php
/* GALLERY_PAGE_MEDIA_ADMIN_CONTROLLER_FINAL */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryPageMediaItem;
use App\Models\GalleryPageSection;
use App\Rules\SafeImageUpload;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

final class GalleryPageMediaAdminController extends Controller
{
    use \App\Http\Controllers\Admin\Concerns\ManagesGalleryPageMedia;
    use \App\Http\Controllers\Admin\Concerns\StoresGalleryPageMediaBatches;
    use \App\Http\Controllers\Admin\Concerns\ValidatesGalleryPageMedia;
    use \App\Http\Controllers\Admin\Concerns\NormalizesGalleryPageVideo;
    use \App\Http\Controllers\Admin\Concerns\MaintainsGalleryPageMedia;

    private const MAX_PHOTO_KB = GalleryPageMediaItem::MAX_PHOTO_KB;

    public function __construct()
    {
        app()->setLocale('id');
    }






































}

