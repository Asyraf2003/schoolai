<?php
/* GALLERY_PAGE_MEDIA_MEDIA_ONLY_NULLABLE_MIGRATION_FINAL */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('gallery_page_media_items')) {
            return;
        }

        Schema::table('gallery_page_media_items', function (Blueprint $table): void {
            if (Schema::hasColumn('gallery_page_media_items', 'title_id')) {
                $table->text('title_id')->nullable()->change();
            }

            if (Schema::hasColumn('gallery_page_media_items', 'title_en')) {
                $table->text('title_en')->nullable()->change();
            }

            if (Schema::hasColumn('gallery_page_media_items', 'description_id')) {
                $table->longText('description_id')->nullable()->change();
            }

            if (Schema::hasColumn('gallery_page_media_items', 'description_en')) {
                $table->longText('description_en')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        // Sengaja tidak dibalik ke NOT NULL agar data media-only tidak rusak.
    }
};
