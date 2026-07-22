<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hero_slides', function (Blueprint $table): void {
            $table->foreignId('article_id')
                ->nullable()
                ->after('id')
                ->unique()
                ->constrained('articles')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('hero_slides', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('article_id');
        });
    }
};
