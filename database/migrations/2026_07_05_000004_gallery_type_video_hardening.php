<?php
/* GALLERY_TYPE_VIDEO_HARDENING_FINAL */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('gallery_items')) {
            return;
        }

        if (Schema::hasColumn('gallery_items', 'media_url')) {
            Schema::table('gallery_items', function (Blueprint $table): void {
                $table->string('media_url', 2048)->nullable()->change();
            });
        }

        if (Schema::hasColumn('gallery_items', 'type')) {
            DB::table('gallery_items')
                ->whereIn('type', ['embed', 'video', 'reel'])
                ->update(['type' => 'video']);

            DB::table('gallery_items')
                ->whereNotIn('type', ['photo', 'video'])
                ->update(['type' => 'photo']);
        }

        if (Schema::hasColumn('gallery_items', 'media_url') && Schema::hasColumn('gallery_items', 'thumbnail_url')) {
            DB::table('gallery_items')
                ->whereNull('media_url')
                ->whereNotNull('thumbnail_url')
                ->update(['media_url' => DB::raw('thumbnail_url')]);
        }

        foreach (['thumbnail_url', 'duration_seconds', 'fallback_icon', 'accent'] as $column) {
            if (Schema::hasColumn('gallery_items', $column)) {
                Schema::table('gallery_items', function (Blueprint $table) use ($column): void {
                    $table->dropColumn($column);
                });
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('gallery_items')) {
            return;
        }

        if (Schema::hasColumn('gallery_items', 'type')) {
            DB::table('gallery_items')
                ->where('type', 'video')
                ->update(['type' => 'embed']);
        }
    }
};
