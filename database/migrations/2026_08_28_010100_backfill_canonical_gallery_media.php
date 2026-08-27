<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('gallery_page_media_items')) {
            return;
        }

        $positions = [];

        DB::table('gallery_page_media_items')
            ->orderBy('gallery_page_section_id')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->each(function (object $legacy) use (&$positions): void {
                $mediaUrl = trim((string) $legacy->media_url);

                if ($mediaUrl === '') {
                    return;
                }

                $galleryItemId = DB::table('gallery_items')
                    ->whereNull('deleted_at')
                    ->where('type', $legacy->type)
                    ->where('media_url', $mediaUrl)
                    ->value('id');

                if ($galleryItemId === null) {
                    $galleryItemId = DB::table('gallery_items')->insertGetId([
                        'title' => $this->title($legacy),
                        'title_id' => $this->title($legacy),
                        'title_en' => $legacy->title_en,
                        'title_ar' => null,
                        'type' => $legacy->type,
                        'category' => 'Galeri Halaman',
                        'category_id' => 'Galeri Halaman',
                        'category_en' => 'Gallery Page',
                        'category_ar' => null,
                        'caption' => $legacy->description_id,
                        'caption_id' => $legacy->description_id,
                        'caption_en' => $legacy->description_en,
                        'caption_ar' => null,
                        'media_url' => $mediaUrl,
                        'sort_order' => 1,
                        'is_published' => $legacy->is_published,
                        'show_on_homepage' => false,
                        'show_on_gallery_page' => false,
                        'legacy_gallery_page_media_item_id' => $legacy->id,
                        'published_at' => $legacy->published_at,
                        'created_at' => $legacy->created_at,
                        'updated_at' => $legacy->updated_at,
                        'deleted_at' => $legacy->deleted_at,
                    ]);
                }

                $sectionId = (int) $legacy->gallery_page_section_id;
                $positions[$sectionId] = ($positions[$sectionId] ?? 0) + 1;

                DB::table('gallery_item_gallery_page_section')->insertOrIgnore([
                    'gallery_item_id' => $galleryItemId,
                    'gallery_page_section_id' => $sectionId,
                    'sort_order' => $positions[$sectionId],
                    'is_published' => $legacy->is_published && $legacy->deleted_at === null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        DB::table('gallery_items')
            ->whereNotNull('legacy_gallery_page_media_item_id')
            ->delete();
    }

    private function title(object $legacy): string
    {
        foreach ([$legacy->title_id, $legacy->title_en] as $title) {
            if (is_string($title) && trim($title) !== '') {
                return trim($title);
            }
        }

        return 'Media Galeri #'.$legacy->id;
    }
};
