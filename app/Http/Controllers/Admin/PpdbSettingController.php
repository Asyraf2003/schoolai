<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\MaintainsPpdbShowcaseOrdering;
use App\Http\Controllers\Admin\Concerns\ManagesPpdbSettings;
use App\Http\Controllers\Admin\Concerns\NormalizesPpdbShowcaseVideo;
use App\Http\Controllers\Admin\Concerns\PresentsPpdbShowcase;
use App\Http\Controllers\Admin\Concerns\ValidatesPpdbShowcase;
use App\Http\Controllers\Controller;

final class PpdbSettingController extends Controller
{
    use MaintainsPpdbShowcaseOrdering;
    use ManagesPpdbSettings;
    use NormalizesPpdbShowcaseVideo;
    use PresentsPpdbShowcase;
    use ValidatesPpdbShowcase;

    public function __construct()
    {
        app()->setLocale('id');
    }
}
