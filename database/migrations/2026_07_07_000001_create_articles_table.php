<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table): void {
            $table->id();
            $table->string('title_id', 200);
            $table->string('title_en', 200)->nullable();
            $table->string('thumbnail_url', 2048);
            $table->string('link_id', 2048);
            $table->string('link_en', 2048)->nullable();
            $table->string('author', 120)->default('Admin');
            $table->date('published_date');
            $table->timestamps();

            $table->index(['published_date', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
