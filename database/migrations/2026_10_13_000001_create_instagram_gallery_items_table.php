<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instagram_gallery_items', function (Blueprint $table): void {
            $table->id();
            $table->string('instagram_id', 64)->unique();
            $table->string('media_type', 32);
            $table->text('caption')->nullable();
            $table->string('media_url', 2048);
            $table->string('thumbnail_url', 2048)->nullable();
            $table->string('permalink', 2048);
            $table->timestamp('posted_at')->index();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instagram_gallery_items');
    }
};
