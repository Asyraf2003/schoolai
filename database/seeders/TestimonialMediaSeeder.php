<?php

namespace Database\Seeders;

use App\Models\TestimonialMedia;
use Illuminate\Database\Seeder;

final class TestimonialMediaSeeder extends Seeder
{
    public function run(): void
    {
        // Arsip lama tidak boleh menghalangi dummy aktif dibuat.
        // Seeder hanya berhenti jika memang sudah ada media testimoni aktif.
        if (TestimonialMedia::query()->exists()) {
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
        ];

        foreach ($media as $index => $mediaUrl) {
            TestimonialMedia::query()->create([
                'type' => 'photo',
                'source' => 'upload',
                'media_url' => $mediaUrl,
                'sort_order' => $index + 1,
                'is_published' => true,
            ]);
        }
    }
}
