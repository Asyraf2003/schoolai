<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\GalleryItem;
use App\Models\GalleryPageMediaItem;
use App\Models\GalleryPageSection;
use App\Models\PpdbSetting;
use App\Models\PpdbShowcaseItem;
use App\Models\SecurityAuditLog;
use App\Models\SiteStatistic;
use App\Models\TestimonialMedia;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

final class AdminDashboardController extends Controller
{
    use \App\Http\Controllers\Admin\Concerns\BuildsAdminDashboard;
    use \App\Http\Controllers\Admin\Concerns\PresentsAdminDashboard;








}

