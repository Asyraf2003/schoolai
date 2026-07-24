<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteStatistic;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class SiteStatisticController extends Controller
{
    use \App\Http\Controllers\Admin\Concerns\ManagesSiteStatistics;
    use \App\Http\Controllers\Admin\Concerns\MaintainsSiteStatistics;

    public function __construct()
    {
        app()->setLocale('id');
    }






















}

