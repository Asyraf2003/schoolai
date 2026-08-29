<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table): void {
            $table->unsignedSmallInteger('homepage_position')
                ->nullable()
                ->after('hero_position')
                ->unique();
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table): void {
            $table->dropUnique(['homepage_position']);
            $table->dropColumn('homepage_position');
        });
    }
};
