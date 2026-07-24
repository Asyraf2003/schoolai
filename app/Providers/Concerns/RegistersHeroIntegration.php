<?php

namespace App\Providers\Concerns;

use App\Http\Controllers\Admin\HeroSlideAdminController;
use App\Models\Article;
use App\Models\HeroSlide;
use App\Models\PpdbSetting;
use App\Support\HeroVideoUrl;
use App\Support\PublicUrl;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\ServiceProvider;
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
            Route::get('/admin/hero', [HeroSlideAdminController::class, 'index'])
                ->name('admin.hero');

            Route::get('/admin/hero/create', [HeroSlideAdminController::class, 'create'])
                ->name('admin.hero.create');

            Route::post('/admin/hero', [HeroSlideAdminController::class, 'store'])
                ->name('admin.hero.store');

            Route::get('/admin/hero/{heroSlide}/edit', [HeroSlideAdminController::class, 'edit'])
                ->name('admin.hero.edit');

            Route::put('/admin/hero/{heroSlide}', [HeroSlideAdminController::class, 'update'])
                ->name('admin.hero.update');

            Route::delete('/admin/hero/{heroSlide}', [HeroSlideAdminController::class, 'destroy'])
                ->name('admin.hero.destroy');

            Route::patch('/admin/hero/{heroSlide}/toggle', [HeroSlideAdminController::class, 'toggle'])
                ->name('admin.hero.toggle');

            Route::patch('/admin/hero/{heroSlide}/move-up', [HeroSlideAdminController::class, 'moveUp'])
                ->name('admin.hero.move-up');

            Route::patch('/admin/hero/{heroSlide}/move-down', [HeroSlideAdminController::class, 'moveDown'])
                ->name('admin.hero.move-down');
        });
    }
}
