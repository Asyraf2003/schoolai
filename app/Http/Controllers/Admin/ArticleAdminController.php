<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Rules\SafeImageUpload;
use App\Support\PublicUrl;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Throwable;

final class ArticleAdminController extends Controller
{
    use \App\Http\Controllers\Admin\Concerns\ManagesArticles;
    use \App\Http\Controllers\Admin\Concerns\ValidatesArticleAdministration;

    private const MAX_THUMBNAIL_KB = 10240;

    public function __construct()
    {
        app()->setLocale('id');
    }




























}

