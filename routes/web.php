<?php

use Illuminate\Support\Facades\Route;

require __DIR__.'/web/public.php';
require __DIR__.'/web/auth.php';

Route::middleware([
    'auth',
    'active.account',
    'active.session',
    'admin',
    'admin.locale',
])->group(function (): void {
    require __DIR__.'/admin/core.php';
    require __DIR__.'/admin/accounts.php';
    require __DIR__.'/admin/ppdb.php';
    require __DIR__.'/admin/articles.php';
    require __DIR__.'/admin/gallery.php';
    require __DIR__.'/admin/statistics.php';
});
