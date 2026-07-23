<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->cleanupLegacyGalleryItems();
        $this->cleanupLegacyGalleryPageSections();
        $this->cleanupLegacyPpdbShowcaseItems();
    }

    public function down(): void
    {
        // Legacy placeholder content is intentionally not restored.
    }

    private function cleanupLegacyGalleryItems(): void
    {
        if (! Schema::hasTable('gallery_items')) {
            return;
        }

        $legacyMediaUrls = [
            'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1600&q=82',
            'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1600&q=82',
            'https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=1600&q=82',
            'https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=1600&q=82',
            'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1600&q=82',
            'https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&w=1600&q=82',
        ];

        DB::table('gallery_items')
            ->whereIn('media_url', $legacyMediaUrls)
            ->whereNull('title_ar')
            ->whereNull('category_ar')
            ->whereNull('caption_ar')
            ->delete();
    }

    private function cleanupLegacyGalleryPageSections(): void
    {
        if (
            ! Schema::hasTable('gallery_page_sections')
            || ! Schema::hasTable('gallery_page_media_items')
        ) {
            return;
        }

        $legacyTitles = [
            'Info Terbaru',
            'Program',
            'Prestasi',
            'Fasilitas',
            'Testimoni',
            'Laporan',
            'Media',
        ];

        DB::table('gallery_page_sections')
            ->whereIn('title_id', $legacyTitles)
            ->whereNull('title_ar')
            ->whereNotExists(function ($query): void {
                $query
                    ->selectRaw('1')
                    ->from('gallery_page_media_items')
                    ->whereColumn(
                        'gallery_page_media_items.gallery_page_section_id',
                        'gallery_page_sections.id'
                    );
            })
            ->delete();
    }

    private function cleanupLegacyPpdbShowcaseItems(): void
    {
        if (! Schema::hasTable('ppdb_showcase_items')) {
            return;
        }

        $legacyTitles = [
            'Konsultasi Keluarga',
            'Observasi Anak',
            'Orientasi Sekolah',
            'Siapkan Link Pendaftaran',
            'Bagikan Informasi PPDB',
            'Pantau Calon Murid',
        ];

        DB::table('ppdb_showcase_items')
            ->whereIn('title_id', $legacyTitles)
            ->whereNull('title_ar')
            ->where(function ($query): void {
                $query
                    ->whereNull('media_url')
                    ->orWhere('media_url', '');
            })
            ->delete();
    }
};
