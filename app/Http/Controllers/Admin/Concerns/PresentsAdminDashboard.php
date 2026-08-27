<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\SecurityAuditLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

trait PresentsAdminDashboard
{
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
                        ? $entry->created_at->translatedFormat('d M Y, H:i').' WIB'
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

        return $actionLabel.' '.$subjectLabel;
    }
}
