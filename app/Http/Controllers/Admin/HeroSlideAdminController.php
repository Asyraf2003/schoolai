<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\HeroSlide;
use App\Rules\SafeImageUpload;
use App\Support\HeroVideoUrl;
use App\Support\PublicUrl;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

final class HeroSlideAdminController extends Controller
{
    use \App\Http\Controllers\Admin\Concerns\ManagesHeroSlides;
    use \App\Http\Controllers\Admin\Concerns\ValidatesHeroSlides;
    use \App\Http\Controllers\Admin\Concerns\ManagesHeroSlideMedia;
    use \App\Http\Controllers\Admin\Concerns\MaintainsHeroSlideOrdering;

    private const MAX_IMAGE_KB = 10240;
    private const MAX_VIDEO_KB = 51200;

    public function __construct()
    {
        app()->setLocale('id');
    }








































}

