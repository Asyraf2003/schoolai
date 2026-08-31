<?php

/* REAL_GALLERY_CRUD_MIGRATION_FINAL */

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const MAX_ITEMS = 6;

    public function up(): void
    {
        Schema::create('gallery_items', function (Blueprint $table): void {
            $table->id();
            $table->string('title', 160);
            $table->string('type', 16)->default('photo');
            $table->string('category', 80)->default('Umum');
            $table->text('caption')->nullable();
            $table->string('media_url', 2048)->nullable();
            $table->unsignedTinyInteger('sort_order')->default(1);
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['is_published', 'sort_order', 'published_at']);
        });

        $this->seedFromLanguageDummy();
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_items');
    }

    private function seedFromLanguageDummy(): void
    {
        $items = trans('home.galeri.items');

        if (! is_array($items)) {
            return;
        }

        $now = now();
        $rows = [];

        foreach (array_slice(array_values($items), 0, self::MAX_ITEMS) as $index => $item) {
            if (! is_array($item)) {
                continue;
            }

            $publishedAt = $now->copy()->subDays($index);

            if (! empty($item['published_at']) && is_string($item['published_at'])) {
                try {
                    $publishedAt = Carbon::parse($item['published_at']);
                } catch (Throwable) {
                    $publishedAt = $now->copy()->subDays($index);
                }
            }

            $rows[] = [
                'title' => (string) ($item['title'] ?? 'Galeri tanpa judul'),
                'type' => 'photo',
                'category' => (string) ($item['category'] ?? 'Umum'),
                'caption' => (string) ($item['caption'] ?? ''),
                'media_url' => (string) ($item['media_url'] ?? $item['thumbnail'] ?? ''),
                'sort_order' => $index + 1,
                'is_published' => true,
                'published_at' => $publishedAt,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($rows !== []) {
            DB::table('gallery_items')->insert($rows);
        }
    }
};
