<?php

namespace Database\Seeders;

use App\Models\SiteStatistic;
use Illuminate\Database\Seeder;

final class SiteStatisticSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['value' => '320+', 'value_en' => '320+', 'value_ar' => '+320', 'label' => 'Siswa', 'label_en' => 'Students', 'label_ar' => 'طالب'],
            ['value' => '24+', 'value_en' => '24+', 'value_ar' => '+24', 'label' => 'Prestasi', 'label_en' => 'Achievements', 'label_ar' => 'إنجازًا'],
            ['value' => '1.200+', 'value_en' => '1,200+', 'value_ar' => '+1200', 'label' => 'Jam Belajar', 'label_en' => 'Learning Hours', 'label_ar' => 'ساعة تعلّم'],
            ['value' => '18+', 'value_en' => '18+', 'value_ar' => '+18', 'label' => 'Program', 'label_en' => 'Programs', 'label_ar' => 'برنامجًا'],
        ];

        foreach ($items as $index => $attributes) {
            $statistic = SiteStatistic::withTrashed()->firstOrNew([
                'label' => $attributes['label'],
            ]);
            $statistic->fill(array_merge($attributes, ['sort_order' => $index + 1]));
            $statistic->save();

            if ($statistic->trashed()) {
                $statistic->restore();
            }
        }
    }
}
