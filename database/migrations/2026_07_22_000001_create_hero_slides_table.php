<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_slides', function (Blueprint $table): void {
            $table->id();
            $table->string('type', 20)->default('image');
            $table->text('media_url');
            $table->text('poster_url')->nullable();
            $table->string('media_alt_id', 255)->nullable();
            $table->string('media_alt_en', 255)->nullable();
            $table->string('media_alt_ar', 255)->nullable();
            $table->string('eyebrow_id', 160)->nullable();
            $table->string('eyebrow_en', 160)->nullable();
            $table->string('eyebrow_ar', 160)->nullable();
            $table->string('title_id', 255);
            $table->string('title_en', 255)->nullable();
            $table->string('title_ar', 255)->nullable();
            $table->text('description_id')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->string('cta_label_id', 160)->nullable();
            $table->string('cta_label_en', 160)->nullable();
            $table->string('cta_label_ar', 160)->nullable();
            $table->text('cta_url')->nullable();
            $table->string('cta_action', 40)->nullable();
            $table->string('focal_position', 60)->default('center center');
            $table->decimal('overlay_strength', 4, 2)->default(0.46);
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        $heroes = [];

        foreach (['id', 'en', 'ar'] as $locale) {
            $base = require base_path("lang/{$locale}/home.php");
            $parityPath = base_path("lang/{$locale}/home_parity.php");
            $parity = file_exists($parityPath) ? require $parityPath : [];
            $heroes[$locale] = array_replace_recursive($base, $parity)['hero'] ?? [];
        }

        $slideKeys = [];

        foreach ($heroes as $hero) {
            $slides = is_array($hero['slides'] ?? null) ? $hero['slides'] : [];
            $slideKeys = array_merge($slideKeys, array_keys($slides));
        }

        $slideKeys = array_values(array_unique($slideKeys));
        sort($slideKeys);
        $now = now();
        $sortOrder = 1;

        foreach ($slideKeys as $key) {
            $id = $heroes['id']['slides'][$key] ?? [];
            $en = $heroes['en']['slides'][$key] ?? [];
            $ar = $heroes['ar']['slides'][$key] ?? [];
            $reference = is_array($id) && $id !== [] ? $id : (is_array($en) ? $en : $ar);

            if (! is_array($reference) || ! is_string($reference['media'] ?? null) || trim($reference['media']) === '') {
                continue;
            }

            DB::table('hero_slides')->insert([
                'type' => in_array(($reference['type'] ?? null), ['image', 'video'], true) ? $reference['type'] : 'image',
                'media_url' => trim((string) $reference['media']),
                'poster_url' => self::nullableString($reference['poster'] ?? null),
                'media_alt_id' => self::nullableString($id['media_alt'] ?? null),
                'media_alt_en' => self::nullableString($en['media_alt'] ?? null),
                'media_alt_ar' => self::nullableString($ar['media_alt'] ?? null),
                'eyebrow_id' => self::nullableString($id['eyebrow'] ?? null),
                'eyebrow_en' => self::nullableString($en['eyebrow'] ?? null),
                'eyebrow_ar' => self::nullableString($ar['eyebrow'] ?? null),
                'title_id' => self::nullableString($id['title'] ?? null) ?: 'Al Mustaqbal School',
                'title_en' => self::nullableString($en['title'] ?? null),
                'title_ar' => self::nullableString($ar['title'] ?? null),
                'description_id' => self::nullableString($id['description'] ?? null),
                'description_en' => self::nullableString($en['description'] ?? null),
                'description_ar' => self::nullableString($ar['description'] ?? null),
                'cta_label_id' => self::nullableString($id['cta']['label'] ?? null),
                'cta_label_en' => self::nullableString($en['cta']['label'] ?? null),
                'cta_label_ar' => self::nullableString($ar['cta']['label'] ?? null),
                'cta_url' => self::nullableString($reference['cta']['href'] ?? null),
                'cta_action' => self::nullableString($reference['cta']['action'] ?? null),
                'focal_position' => self::nullableString($reference['focal_position'] ?? null) ?: 'center center',
                'overlay_strength' => is_numeric($reference['overlay_strength'] ?? null)
                    ? max(0.28, min(0.88, (float) $reference['overlay_strength']))
                    : 0.46,
                'sort_order' => $sortOrder++,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_slides');
    }

    private static function nullableString(mixed $value): ?string
    {
        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }
};
