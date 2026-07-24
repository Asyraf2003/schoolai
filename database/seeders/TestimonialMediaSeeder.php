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
            'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=82',
            'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1200&q=82',
            'https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=1200&q=82',
            'https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=1200&q=82',
            'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1200&q=82',
            'https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&w=1200&q=82',
            'https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?auto=format&fit=crop&w=1200&q=82',
            'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1200&q=82',
            'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1200&q=82',
            'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?auto=format&fit=crop&w=1200&q=82',
            'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=1200&q=82',
            'https://images.unsplash.com/photo-1588072432836-e10032774350?auto=format&fit=crop&w=1200&q=82',
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
