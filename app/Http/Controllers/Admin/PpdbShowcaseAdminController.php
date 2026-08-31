<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\MaintainsPpdbShowcaseItems;
use App\Http\Controllers\Admin\Concerns\ManagesPpdbShowcaseItems;
use App\Http\Controllers\Admin\Concerns\ManagesPpdbShowcaseMedia;
use App\Http\Controllers\Admin\Concerns\OrdersAndValidatesPpdbShowcase;
use App\Http\Controllers\Controller;

final class PpdbShowcaseAdminController extends Controller
{
    use MaintainsPpdbShowcaseItems;
    use ManagesPpdbShowcaseItems;
    use ManagesPpdbShowcaseMedia;
    use OrdersAndValidatesPpdbShowcase;

    public function __construct()
    {
        app()->setLocale('id');
    }
}
