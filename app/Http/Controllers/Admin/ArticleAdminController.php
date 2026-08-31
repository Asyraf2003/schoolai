<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ManagesArticles;
use App\Http\Controllers\Admin\Concerns\ValidatesArticleAdministration;
use App\Http\Controllers\Controller;

final class ArticleAdminController extends Controller
{
    use ManagesArticles;
    use ValidatesArticleAdministration;

    private const MAX_THUMBNAIL_KB = 10240;

    public function __construct()
    {
        app()->setLocale('id');
    }
}
