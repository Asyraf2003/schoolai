<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Rules\SafeImageUpload;
use App\Support\ArticleContentSanitizer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

final class ArticleCanvasAdminController extends Controller
{
    use \App\Http\Controllers\Admin\Concerns\ManagesArticleCanvasDrafts;
    use \App\Http\Controllers\Admin\Concerns\ManagesArticleCanvasMedia;
    use \App\Http\Controllers\Admin\Concerns\PublishesArticleCanvas;

    private const MAX_IMAGE_KB = 10240;

    public function __construct(private readonly ArticleContentSanitizer $sanitizer)
    {
        app()->setLocale('id');
    }




















}

