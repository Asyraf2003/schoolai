<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ManagesArticleCanvasDrafts;
use App\Http\Controllers\Admin\Concerns\ManagesArticleCanvasMedia;
use App\Http\Controllers\Admin\Concerns\PublishesArticleCanvas;
use App\Http\Controllers\Controller;
use App\Support\ArticleContentSanitizer;

final class ArticleCanvasAdminController extends Controller
{
    use ManagesArticleCanvasDrafts;
    use ManagesArticleCanvasMedia;
    use PublishesArticleCanvas;

    private const MAX_IMAGE_KB = 10240;

    public function __construct(private readonly ArticleContentSanitizer $sanitizer)
    {
        app()->setLocale('id');
    }

}
