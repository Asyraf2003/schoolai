<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('articles')) {
            Schema::table('articles', function (Blueprint $table): void {
                if (! Schema::hasColumn('articles', 'title_ar')) {
                    $table->string('title_ar', 200)->nullable()->after('title_en');
                }

                if (! Schema::hasColumn('articles', 'description_ar')) {
                    $table->text('description_ar')->nullable()->after('description_en');
                }

                if (! Schema::hasColumn('articles', 'link_ar')) {
                    $table->string('link_ar', 2048)->nullable()->after('link_en');
                }
            });
        }

        if (Schema::hasTable('gallery_items')) {
            Schema::table('gallery_items', function (Blueprint $table): void {
                if (! Schema::hasColumn('gallery_items', 'title_ar')) {
                    $table->string('title_ar', 160)->nullable()->after('title_en');
                }

                if (! Schema::hasColumn('gallery_items', 'category_ar')) {
                    $table->string('category_ar', 80)->nullable()->after('category_en');
                }

                if (! Schema::hasColumn('gallery_items', 'caption_ar')) {
                    $table->text('caption_ar')->nullable()->after('caption_en');
                }
            });
        }

        if (Schema::hasTable('gallery_page_sections')) {
            Schema::table('gallery_page_sections', function (Blueprint $table): void {
                if (! Schema::hasColumn('gallery_page_sections', 'title_ar')) {
                    $table->text('title_ar')->nullable()->after('title_en');
                }

                if (! Schema::hasColumn('gallery_page_sections', 'description_ar')) {
                    $table->longText('description_ar')->nullable()->after('description_en');
                }
            });
        }

        if (Schema::hasTable('ppdb_showcase_items')) {
            Schema::table('ppdb_showcase_items', function (Blueprint $table): void {
                if (! Schema::hasColumn('ppdb_showcase_items', 'title_ar')) {
                    $table->string('title_ar', 180)->nullable()->after('title_en');
                }

                if (! Schema::hasColumn('ppdb_showcase_items', 'description_ar')) {
                    $table->text('description_ar')->nullable()->after('description_en');
                }
            });
        }

        if (Schema::hasTable('site_statistics')) {
            Schema::table('site_statistics', function (Blueprint $table): void {
                if (! Schema::hasColumn('site_statistics', 'value_ar')) {
                    $table->string('value_ar', 80)->nullable()->after('value_en');
                }

                if (! Schema::hasColumn('site_statistics', 'label_ar')) {
                    $table->string('label_ar', 120)->nullable()->after('label_en');
                }
            });
        }
    }

    public function down(): void
    {
        $this->dropColumnsIfPresent('site_statistics', [
            'label_ar',
            'value_ar',
        ]);

        $this->dropColumnsIfPresent('ppdb_showcase_items', [
            'description_ar',
            'title_ar',
        ]);

        $this->dropColumnsIfPresent('gallery_page_sections', [
            'description_ar',
            'title_ar',
        ]);

        $this->dropColumnsIfPresent('gallery_items', [
            'caption_ar',
            'category_ar',
            'title_ar',
        ]);

        $this->dropColumnsIfPresent('articles', [
            'link_ar',
            'description_ar',
            'title_ar',
        ]);
    }

    private function dropColumnsIfPresent(string $tableName, array $columns): void
    {
        if (! Schema::hasTable($tableName)) {
            return;
        }

        foreach ($columns as $column) {
            if (! Schema::hasColumn($tableName, $column)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($column): void {
                $table->dropColumn($column);
            });
        }
    }
};
