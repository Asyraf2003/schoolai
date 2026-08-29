<?php

namespace App\Providers\Concerns;

use App\Http\Controllers\Admin\HeroAdminController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\View\View;

trait RegistersHeroIntegration
{
    public function boot(): void
    {
        if (! $this->app->routesAreCached()) {
            $this->registerAdminRoutes();
        }

        ViewFacade::composer('welcome', function (View $view): void {
            $this->injectDatabaseHero($view);
        });
    }

    private function registerAdminRoutes(): void
    {
        Route::middleware([
            'web',
            'auth',
            'active.account',
            'admin',
            'admin.locale',
        ])->group(function (): void {
            Route::get('/admin/hero', [HeroAdminController::class, 'index'])
                ->name('admin.hero');

            Route::put('/admin/hero/opening', [HeroAdminController::class, 'update'])
                ->name('admin.hero.update');

            Route::post('/admin/hero/articles', [HeroAdminController::class, 'promote'])
                ->name('admin.hero.articles.promote');

            Route::patch('/admin/hero/articles/order', [HeroAdminController::class, 'reorder'])
                ->name('admin.hero.articles.order');

            Route::delete('/admin/hero/articles/{article}', [HeroAdminController::class, 'unpromote'])
                ->name('admin.hero.articles.unpromote');

            Route::patch('/admin/hero/articles/{article}/move-up', [HeroAdminController::class, 'moveUp'])
                ->name('admin.hero.articles.move-up');

            Route::patch('/admin/hero/articles/{article}/move-down', [HeroAdminController::class, 'moveDown'])
                ->name('admin.hero.articles.move-down');
        });
    }
}
