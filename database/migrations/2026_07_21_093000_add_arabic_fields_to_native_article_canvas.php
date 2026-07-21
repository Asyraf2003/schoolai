<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('articles')) {
            return;
        }

        Schema::table('articles', function (Blueprint $table): void {
            if (! Schema::hasColumn('articles', 'subtitle_ar')) {
                $table->string('subtitle_ar', 300)->nullable()->after('subtitle_en');
            }

            if (! Schema::hasColumn('articles', 'content_ar')) {
                $table->longText('content_ar')->nullable()->after('content_en');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('articles')) {
            return;
        }

        Schema::table('articles', function (Blueprint $table): void {
            $columns = array_values(array_filter(
                ['subtitle_ar', 'content_ar'],
                fn (string $column): bool => Schema::hasColumn('articles', $column),
            ));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
