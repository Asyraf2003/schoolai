<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

final class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('welcome', [
            'meta' => __('home.meta'),
            'hero' => __('home.hero'),
        ]);
    }
}
