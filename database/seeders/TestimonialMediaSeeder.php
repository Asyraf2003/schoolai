<?php

namespace Database\Seeders;

use App\Models\TestimonialMedia;
use Illuminate\Database\Seeder;

final class TestimonialMediaSeeder extends Seeder
{
    public function run(): void
    {
        $activeCount = TestimonialMedia::query()->count();

        if ($activeCount >= TestimonialMedia::MAX_ITEMS) {
            return;
        }

        $media = [
            '/media/home/9.png',
            '/media/home/10.png',
            '/media/home/11.png',
            '/media/home/12.png',
            '/media/home/9.png',
            '/media/home/10.png',
            '/media/home/11.png',
            '/media/home/12.png',
            '/media/home/9.png',
            '/media/home/10.png',
            '/media/home/11.png',
            '/media/home/12.png',
        ];

        for ($index = $activeCount; $index < TestimonialMedia::MAX_ITEMS; $index++) {
            TestimonialMedia::query()->create([
                'type' => 'photo',
                'source' => 'upload',
                'media_url' => $media[$index % count($media)],
                'sort_order' => $index + 1,
                'is_published' => true,
            ]);
        }
    }
}
