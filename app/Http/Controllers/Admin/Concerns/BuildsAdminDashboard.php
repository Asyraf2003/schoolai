<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Article;
use App\Models\GalleryItem;
use App\Models\GalleryPageSection;
use App\Models\PpdbSetting;
use App\Models\PpdbShowcaseItem;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

trait BuildsAdminDashboard
{
    public function __invoke(): View
    {
        $articles = $this->summary(
            'articles',
            fn (): int => Article::query()->publiclyVisible()->count(),
            fn (): int => Article::query()->count(),
            fn (): int => Article::onlyTrashed()->count(),
        );

        $galleryMain = $this->summary(
            'gallery_items',
            fn (): int => GalleryItem::query()->where('is_published', true)->count(),
            fn (): int => GalleryItem::query()->count(),
            fn (): int => GalleryItem::onlyTrashed()->count(),
        );

        $gallerySections = $this->summary(
            'gallery_page_sections',
            fn (): int => GalleryPageSection::query()->where('is_published', true)->count(),
            fn (): int => GalleryPageSection::query()->count(),
            fn (): int => GalleryPageSection::onlyTrashed()->count(),
        );

        $ppdbShowcase = $this->summary(
            'ppdb_showcase_items',
            fn (): int => PpdbShowcaseItem::query()->count(),
            fn (): int => PpdbShowcaseItem::query()->count(),
            fn (): int => PpdbShowcaseItem::onlyTrashed()->count(),
        );

        $ppdbSetting = Schema::hasTable('ppdb_settings')
            ? PpdbSetting::query()->first()
            : null;

        $contentRows = [
            [
                'label' => 'Artikel',
                'active' => $articles['active'],
                'inactive' => $articles['inactive'],
                'archived' => $articles['archived'],
                'route' => 'admin.artikel',
            ],
            [
                'label' => 'Galeri Utama',
                'active' => $galleryMain['active'],
                'inactive' => $galleryMain['inactive'],
                'archived' => $galleryMain['archived'],
                'route' => 'admin.galeri',
            ],
            [
                'label' => 'Bagian Galeri',
                'active' => $gallerySections['active'],
                'inactive' => $gallerySections['inactive'],
                'archived' => $gallerySections['archived'],
                'route' => 'admin.galeri',
            ],
            [
                'label' => 'Konten PPDB',
                'active' => $ppdbShowcase['active'],
                'inactive' => $ppdbShowcase['inactive'],
                'archived' => $ppdbShowcase['archived'],
                'route' => 'admin.ppdb',
            ],
        ];

        $totalArchived = collect($contentRows)->sum('archived');

        return view('admin.dashboard', [
            'articles' => $articles,
            'galleryMain' => $galleryMain,
            'gallerySections' => $gallerySections,
            'ppdbShowcase' => $ppdbShowcase,
            'ppdbSetting' => $ppdbSetting,
            'ppdbOpen' => $ppdbSetting?->isRegistrationOpen() ?? false,
            'contentRows' => $contentRows,
            'totalArchived' => $totalArchived,
            'recentActivity' => $this->recentActivity(),
        ]);
    }
}
