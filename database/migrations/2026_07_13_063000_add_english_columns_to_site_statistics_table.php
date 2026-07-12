<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_statistics', function (Blueprint $table): void {
            $table->string('value_en', 80)
                ->nullable()
                ->after('value');

            $table->string('label_en', 120)
                ->nullable()
                ->after('label');
        });

        $englishItems = trans('home.stats.items', [], 'en');
        $englishItems = is_array($englishItems)
            ? array_values($englishItems)
            : [];

        $statistics = DB::table('site_statistics')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'value', 'label']);

        foreach ($statistics as $index => $statistic) {
            $item = $englishItems[$index] ?? [];

            $valueEn = trim(
                (string) ($item['count'] ?? '')
                . (string) ($item['suffix'] ?? '')
            );

            $labelEn = trim((string) ($item['label'] ?? ''));

            DB::table('site_statistics')
                ->where('id', $statistic->id)
                ->update([
                    'value_en' => $valueEn !== ''
                        ? $valueEn
                        : $statistic->value,
                    'label_en' => $labelEn !== ''
                        ? $labelEn
                        : $statistic->label,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('site_statistics', function (Blueprint $table): void {
            $table->dropColumn([
                'value_en',
                'label_en',
            ]);
        });
    }
};
