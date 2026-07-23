<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonial_media', function (Blueprint $table): void {
            $table->id();
            $table->string('type', 20);
            $table->string('source', 20)->default('upload');
            $table->text('media_url');
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_published', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonial_media');
    }
};
