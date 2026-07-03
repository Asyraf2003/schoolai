<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('site_statistics') || ! Schema::hasColumn('site_statistics', 'description')) {
            return;
        }

        Schema::table('site_statistics', function (Blueprint $table): void {
            $table->dropColumn('description');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('site_statistics') || Schema::hasColumn('site_statistics', 'description')) {
            return;
        }

        Schema::table('site_statistics', function (Blueprint $table): void {
            $table->string('description', 255)->nullable()->after('label');
        });
    }
};
