<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gallery_items', function (Blueprint $table): void {
            $table->unsignedInteger('media_width')->nullable()->after('media_url');
            $table->unsignedInteger('media_height')->nullable()->after('media_width');
        });

        DB::table('gallery_items')
            ->where('type', 'photo')
            ->where('media_url', 'like', '%/site/school-life/aula-v1.webp')
            ->update([
                'media_width' => 1920,
                'media_height' => 1200,
            ]);
    }

    public function down(): void
    {
        Schema::table('gallery_items', function (Blueprint $table): void {
            $table->dropColumn(['media_width', 'media_height']);
        });
    }
};
