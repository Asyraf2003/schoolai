<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_settings', function (Blueprint $table): void {
            $table->id();
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
            $table->timestamps();
        });

        Schema::table('articles', function (Blueprint $table): void {
            $table->unsignedSmallInteger('hero_position')->nullable()->after('thumbnail_url');
        });

        $opening = DB::table('hero_slides')
            ->whereNull('article_id')
            ->orderByDesc('is_active')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        DB::table('hero_settings')->insert($this->openingData($opening));

        $articleIds = DB::table('hero_slides')
            ->whereNotNull('article_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('article_id')
            ->unique()
            ->values();

        foreach ($articleIds as $index => $articleId) {
            DB::table('articles')
                ->where('id', $articleId)
                ->update(['hero_position' => $index + 1]);
        }

        Schema::table('articles', function (Blueprint $table): void {
            $table->unique('hero_position');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table): void {
            $table->dropUnique(['hero_position']);
            $table->dropColumn('hero_position');
        });

        Schema::dropIfExists('hero_settings');
    }

    /** @return array<string, mixed> */
    private function openingData(?object $opening): array
    {
        $fallbacks = [];

        foreach (['id', 'en', 'ar'] as $locale) {
            $home = require base_path("lang/{$locale}/home.php");
            $parityPath = base_path("lang/{$locale}/home_parity.php");
            $parity = file_exists($parityPath) ? require $parityPath : [];
            $hero = array_replace_recursive($home, $parity)['hero'] ?? [];
            $fallbacks[$locale] = collect($hero['slides'] ?? [])->first() ?? [];
        }

        $value = static fn (string $field, string $locale): ?string => self::text(
            $opening?->{$field.'_'.$locale} ?? data_get($fallbacks, $locale.'.'.$field),
        );
        $cta = static fn (string $locale): ?string => self::text(
            $opening?->{'cta_label_'.$locale} ?? data_get($fallbacks, $locale.'.cta.label'),
        );

        return [
            'eyebrow_id' => $value('eyebrow', 'id'),
            'eyebrow_en' => $value('eyebrow', 'en'),
            'eyebrow_ar' => $value('eyebrow', 'ar'),
            'title_id' => $value('title', 'id') ?: 'Al Mustaqbal School',
            'title_en' => $value('title', 'en'),
            'title_ar' => $value('title', 'ar'),
            'description_id' => $value('description', 'id'),
            'description_en' => $value('description', 'en'),
            'description_ar' => $value('description', 'ar'),
            'cta_label_id' => $cta('id'),
            'cta_label_en' => $cta('en'),
            'cta_label_ar' => $cta('ar'),
            'cta_url' => self::text($opening?->cta_url ?? data_get($fallbacks, 'id.cta.href')),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private static function text(mixed $value): ?string
    {
        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }
};
