<?php
/* SIMPLIFY_GALLERY_ITEMS_TABLE_FINAL */

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

        DB::table('gallery_items')
            ->where('type', 'reel')
            ->update(['type' => 'video']);

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

        Schema::table('gallery_items', function (Blueprint $table): void {
            if (! Schema::hasColumn('gallery_items', 'thumbnail_url')) {
                $table->string('thumbnail_url')->nullable();
            }

            if (! Schema::hasColumn('gallery_items', 'duration_seconds')) {
                $table->unsignedSmallInteger('duration_seconds')->nullable();
            }

            if (! Schema::hasColumn('gallery_items', 'fallback_icon')) {
                $table->string('fallback_icon', 16)->default('📸');
            }

            if (! Schema::hasColumn('gallery_items', 'accent')) {
                $table->string('accent', 32)->default('#19aee6');
            }
        });
    }
};
