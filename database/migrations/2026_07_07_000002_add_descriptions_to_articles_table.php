<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table): void {
            if (! Schema::hasColumn('articles', 'description_id')) {
                $table->text('description_id')->nullable()->after('title_en');
            }

            if (! Schema::hasColumn('articles', 'description_en')) {
                $table->text('description_en')->nullable()->after('description_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table): void {
            if (Schema::hasColumn('articles', 'description_en')) {
                $table->dropColumn('description_en');
            }

            if (Schema::hasColumn('articles', 'description_id')) {
                $table->dropColumn('description_id');
            }
        });
    }
};
