<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PpdbShowcaseItem;
use App\Rules\SafeImageUpload;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

final class PpdbShowcaseAdminController extends Controller
{
    use \App\Http\Controllers\Admin\Concerns\ManagesPpdbShowcaseItems;
    use \App\Http\Controllers\Admin\Concerns\OrdersAndValidatesPpdbShowcase;
    use \App\Http\Controllers\Admin\Concerns\ManagesPpdbShowcaseMedia;
    use \App\Http\Controllers\Admin\Concerns\MaintainsPpdbShowcaseItems;

    public function __construct()
    {
        app()->setLocale('id');
    }




































}

