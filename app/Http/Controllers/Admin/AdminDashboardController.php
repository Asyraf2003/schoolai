<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\BuildsAdminDashboard;
use App\Http\Controllers\Admin\Concerns\PresentsAdminDashboard;
use App\Http\Controllers\Controller;

final class AdminDashboardController extends Controller
{
    use BuildsAdminDashboard;
    use PresentsAdminDashboard;
}
