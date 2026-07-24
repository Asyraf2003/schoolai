<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PpdbSetting;
use App\Models\PpdbShowcaseItem;
use App\Rules\SafeImageUpload;
use App\Support\PublicUrl;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Throwable;

final class PpdbSettingController extends Controller
{
    use \App\Http\Controllers\Admin\Concerns\ManagesPpdbSettings;
    use \App\Http\Controllers\Admin\Concerns\PresentsPpdbShowcase;
    use \App\Http\Controllers\Admin\Concerns\ValidatesPpdbShowcase;
    use \App\Http\Controllers\Admin\Concerns\NormalizesPpdbShowcaseVideo;
    use \App\Http\Controllers\Admin\Concerns\MaintainsPpdbShowcaseOrdering;

    public function __construct()
    {
        app()->setLocale('id');
    }
























































}

