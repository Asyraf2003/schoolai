<?php

namespace Database\Seeders;

use App\Models\GalleryItem;
use Illuminate\Database\Seeder;

final class GallerySeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->items() as $index => $attributes) {
            $item = GalleryItem::withTrashed()->firstOrNew([
                'media_url' => $attributes['media_url'],
            ]);
            $item->fill($attributes);
            $item->forceFill(['sort_order' => $index + 1]);
            $item->save();

            if ($item->trashed()) {
                $item->restore();
            }
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function items(): array
    {
        $images = [
            'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1400&q=82',
            'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1400&q=82',
            'https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=1400&q=82',
            'https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=1400&q=82',
            'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1400&q=82',
        ];
        $categories = ['Kegiatan', 'Pembelajaran', 'Prestasi', 'Program', 'Kreativitas'];

        $items = [];

        foreach ($images as $index => $image) {
            $number = $index + 1;
            $category = $categories[$index];
            $items[] = [
                'title' => 'Momen Al Mustaqbal '.$number,
                'title_id' => 'Momen Al Mustaqbal '.$number,
                'title_en' => 'Al Mustaqbal Moment '.$number,
                'title_ar' => 'لحظة من المستقبل '.$number,
                'type' => 'photo',
                'category' => $category,
                'category_id' => $category,
                'category_en' => $category,
                'category_ar' => 'الحياة المدرسية',
                'caption' => 'Dokumentasi dummy untuk pratinjau galeri sekolah.',
                'caption_id' => 'Dokumentasi dummy untuk pratinjau galeri sekolah.',
                'caption_en' => 'Dummy documentation for the school gallery preview.',
                'caption_ar' => 'محتوى تجريبي لمعاينة معرض المدرسة.',
                'media_url' => $image,
                'is_published' => true,
                'published_at' => now()->subDays($index),
            ];
        }

        $items[] = [
            'title' => 'Video Kegiatan Al Mustaqbal',
            'title_id' => 'Video Kegiatan Al Mustaqbal',
            'title_en' => 'Al Mustaqbal Activity Video',
            'title_ar' => 'فيديو أنشطة المستقبل',
            'type' => 'video',
            'category' => 'Kegiatan',
            'category_id' => 'Kegiatan',
            'category_en' => 'Activities',
            'category_ar' => 'الأنشطة',
            'caption' => 'Video dummy kegiatan sekolah.',
            'caption_id' => 'Video dummy kegiatan sekolah.',
            'caption_en' => 'Dummy school activity video.',
            'caption_ar' => 'فيديو تجريبي للأنشطة المدرسية.',
            'media_url' => 'https://www.youtube.com/embed/kb1dXcf3QQs',
            'is_published' => true,
            'published_at' => now()->subDays(5),
        ];

        return $items;
    }
}
