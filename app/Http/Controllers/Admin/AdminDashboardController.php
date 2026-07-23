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

        $galleryMedia = $this->summary(
            'gallery_page_media_items',
            fn (): int => GalleryPageMediaItem::query()->where('is_published', true)->count(),
            fn (): int => GalleryPageMediaItem::query()->count(),
            fn (): int => GalleryPageMediaItem::onlyTrashed()->count(),
        );

        $testimonials = $this->summary(
            'testimonial_media',
            fn (): int => TestimonialMedia::query()->where('is_published', true)->count(),
            fn (): int => TestimonialMedia::query()->count(),
            fn (): int => TestimonialMedia::onlyTrashed()->count(),
        );

        $statistics = $this->summary(
            'site_statistics',
            fn (): int => SiteStatistic::query()->count(),
            fn (): int => SiteStatistic::query()->count(),
            fn (): int => SiteStatistic::onlyTrashed()->count(),
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
                'label' => 'Media Galeri',
                'active' => $galleryMedia['active'],
                'inactive' => $galleryMedia['inactive'],
                'archived' => $galleryMedia['archived'],
                'route' => 'admin.galeri',
            ],
            [
                'label' => 'Testimoni',
                'active' => $testimonials['active'],
                'inactive' => $testimonials['inactive'],
                'archived' => $testimonials['archived'],
                'route' => 'admin.testimoni.index',
            ],
            [
                'label' => 'Statistik Homepage',
                'active' => $statistics['active'],
                'inactive' => $statistics['inactive'],
                'archived' => $statistics['archived'],
                'route' => 'admin.stats.edit',
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
            'galleryMedia' => $galleryMedia,
            'testimonials' => $testimonials,
            'statistics' => $statistics,
            'ppdbShowcase' => $ppdbShowcase,
            'ppdbSetting' => $ppdbSetting,
            'ppdbOpen' => $ppdbSetting?->isRegistrationOpen() ?? false,
            'contentRows' => $contentRows,
            'totalArchived' => $totalArchived,
            'recentActivity' => $this->recentActivity(),
        ]);
    }

    /**
     * @return array{active:int,inactive:int,archived:int,total:int}
     */
    private function summary(
        string $table,
        callable $activeResolver,
        callable $totalResolver,
        callable $archivedResolver,
    ): array {
        if (! Schema::hasTable($table)) {
            return [
                'active' => 0,
                'inactive' => 0,
                'archived' => 0,
                'total' => 0,
            ];
        }

        $active = max(0, (int) $activeResolver());
        $total = max(0, (int) $totalResolver());
        $archived = max(0, (int) $archivedResolver());

        return [
            'active' => $active,
            'inactive' => max(0, $total - $active),
            'archived' => $archived,
            'total' => $total,
        ];
    }

    /**
     * @return Collection<int, array{label:string,actor:string,subject_id:?string,time:string,title:string}>
     */
    private function recentActivity(): Collection
    {
        if (! Schema::hasTable('security_audit_logs')) {
            return collect();
        }

        return SecurityAuditLog::query()
            ->with('actor:id,name')
            ->where('event', 'like', 'content.%')
            ->latest('id')
            ->limit(8)
            ->get()
            ->map(function (SecurityAuditLog $entry): array {
                return [
                    'label' => $this->auditLabel($entry->event),
                    'actor' => $entry->actor?->name ?: 'Sistem',
                    'subject_id' => $entry->auditable_id,
                    'time' => $entry->created_at?->diffForHumans() ?? '-',
                    'title' => $entry->created_at
                        ? $entry->created_at->translatedFormat('d M Y, H:i') . ' WIB'
                        : '-',
                ];
            });
    }

    private function auditLabel(string $event): string
    {
        $parts = explode('.', $event);
        $subject = $parts[1] ?? '';
        $action = $parts[2] ?? '';

        $subjectLabel = match ($subject) {
            'article' => 'artikel',
            'gallery_item' => 'galeri utama',
            'gallery_page_section' => 'bagian galeri',
            'gallery_page_media_item' => 'media galeri',
            'ppdb_setting' => 'pengaturan PPDB',
            'ppdb_showcase_item' => 'konten PPDB',
            'site_statistic' => 'statistik homepage',
            'testimonial_media' => 'media testimoni',
            default => 'konten website',
        };

        $actionLabel = match ($action) {
            'created' => 'Menambahkan',
            'updated' => 'Memperbarui',
            'deleted' => 'Mengarsipkan',
            'restored' => 'Memulihkan',
            default => 'Mengubah',
        };

        return $actionLabel . ' ' . $subjectLabel;
    }
}
