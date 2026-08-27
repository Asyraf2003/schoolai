<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gallery_items', function (Blueprint $table): void {
            $table->boolean('show_on_homepage')->default(true)->after('is_published');
            $table->boolean('show_on_gallery_page')->default(true)->after('show_on_homepage');
            $table->unsignedBigInteger('legacy_gallery_page_media_item_id')
                ->nullable()
                ->unique()
                ->after('show_on_gallery_page');
        });

        Schema::create('gallery_item_gallery_page_section', function (Blueprint $table): void {
            $table->foreignId('gallery_item_id');
            $table->foreignId('gallery_page_section_id');
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->primary(
                ['gallery_item_id', 'gallery_page_section_id'],
                'gallery_item_section_primary',
            );
            $table->index(
                ['gallery_page_section_id', 'sort_order'],
                'gallery_section_item_order_idx',
            );
            $table->foreign('gallery_item_id', 'gallery_item_section_item_fk')
                ->references('id')
                ->on('gallery_items')
                ->cascadeOnDelete();
            $table->foreign('gallery_page_section_id', 'gallery_item_section_section_fk')
                ->references('id')
                ->on('gallery_page_sections')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_item_gallery_page_section');

        Schema::table('gallery_items', function (Blueprint $table): void {
            $table->dropUnique(['legacy_gallery_page_media_item_id']);
            $table->dropColumn([
                'show_on_homepage',
                'show_on_gallery_page',
                'legacy_gallery_page_media_item_id',
            ]);
        });
    }
};
