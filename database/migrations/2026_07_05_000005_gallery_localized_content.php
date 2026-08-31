<?php

/* GALLERY_LOCALIZED_CONTENT_FINAL */

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

        foreach ([
            'title_id' => fn (Blueprint $table) => $table->string('title_id', 160)->nullable()->after('title'),
            'title_en' => fn (Blueprint $table) => $table->string('title_en', 160)->nullable()->after('title_id'),
            'category_id' => fn (Blueprint $table) => $table->string('category_id', 80)->nullable()->after('category'),
            'category_en' => fn (Blueprint $table) => $table->string('category_en', 80)->nullable()->after('category_id'),
            'caption_id' => fn (Blueprint $table) => $table->text('caption_id')->nullable()->after('caption'),
            'caption_en' => fn (Blueprint $table) => $table->text('caption_en')->nullable()->after('caption_id'),
        ] as $column => $callback) {
            if (! Schema::hasColumn('gallery_items', $column)) {
                Schema::table('gallery_items', function (Blueprint $table) use ($callback): void {
                    $callback($table);
                });
            }
        }

        if (Schema::hasColumn('gallery_items', 'title') && Schema::hasColumn('gallery_items', 'title_id')) {
            DB::table('gallery_items')
                ->whereNull('title_id')
                ->update(['title_id' => DB::raw('title')]);
        }

        if (Schema::hasColumn('gallery_items', 'category') && Schema::hasColumn('gallery_items', 'category_id')) {
            DB::table('gallery_items')
                ->whereNull('category_id')
                ->update(['category_id' => DB::raw('category')]);
        }

        if (Schema::hasColumn('gallery_items', 'caption') && Schema::hasColumn('gallery_items', 'caption_id')) {
            DB::table('gallery_items')
                ->whereNull('caption_id')
                ->update(['caption_id' => DB::raw('caption')]);
        }

        if (Schema::hasColumn('gallery_items', 'type')) {
            DB::table('gallery_items')
                ->whereIn('type', ['embed', 'reel'])
                ->update(['type' => 'video']);

            DB::table('gallery_items')
                ->whereNotIn('type', ['photo', 'video'])
                ->update(['type' => 'photo']);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('gallery_items')) {
            return;
        }

        foreach (['caption_en', 'caption_id', 'category_en', 'category_id', 'title_en', 'title_id'] as $column) {
            if (Schema::hasColumn('gallery_items', $column)) {
                Schema::table('gallery_items', function (Blueprint $table) use ($column): void {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
