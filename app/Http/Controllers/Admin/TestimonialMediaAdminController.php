<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestimonialMedia;
use App\Rules\SafeImageUpload;
use App\Support\TestimonialVideoUrl;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class TestimonialMediaAdminController extends Controller
{
    use \App\Http\Controllers\Admin\Concerns\ManagesTestimonialMedia;
    use \App\Http\Controllers\Admin\Concerns\OrdersTestimonialMedia;
    use \App\Http\Controllers\Admin\Concerns\StoresTestimonialMedia;

    public function __construct()
    {
        app()->setLocale('id');
    }






























}

