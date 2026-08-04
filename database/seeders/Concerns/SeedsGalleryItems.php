<?php

namespace Database\Seeders\Concerns;

use App\Models\GalleryItem;

trait SeedsGalleryItems
{
    public function run(): void
    {
        GalleryItem::withTrashed()
            ->where('media_url', 'https://www.youtube.com/embed/kb1dXcf3QQs')
            ->forceDelete();

        foreach ($this->items() as $index => $attributes) {
            $item = GalleryItem::withTrashed()->firstOrNew([
                'media_url' => $attributes['media_url'],
            ]);

            $item->fill($attributes);

            $item->forceFill([
                'sort_order' => $index + 1,
            ]);

            $item->save();

            if ($item->trashed()) {
                $item->restore();
            }
        }

        $this->seedGalleryPageSections();
    }

    /** @return array<int, array<string, mixed>> */
    private function items(): array
    {
        $media = [
            [
                'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1400&q=82',
                'Kegiatan',
                'Activities',
                'الأنشطة',
            ],
            [
                'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1400&q=82',
                'Pembelajaran',
                'Learning',
                'التعلّم',
            ],
            [
                'https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=1400&q=82',
                'Prestasi',
                'Achievements',
                'الإنجازات',
            ],
            [
                'https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=1400&q=82',
                'Program',
                'Programs',
                'البرامج',
            ],
            [
                'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1400&q=82',
                'Kreativitas',
                'Creativity',
                'الإبداع',
            ],
            [
                'https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&w=1400&q=82',
                'Sains',
                'Science',
                'العلوم',
            ],
        ];

        $arabicNumbers = [
            1 => '1',
            2 => '2',
            3 => '3',
            4 => '4',
            5 => '5',
            6 => '6',
        ];

        $items = [];

        foreach ($media as $index => [$image, $categoryId, $categoryEn, $categoryAr]) {
            $number = $index + 1;
            $arabicNumber = $arabicNumbers[$number];

            $items[] = [
                'title' => 'Momen Al Mustaqbal '.$number,

                'title_id' => 'Momen Al Mustaqbal '.$number,
                'title_en' => 'Al Mustaqbal Moment '.$number,
                'title_ar' => 'لحظة من المستقبل '.$arabicNumber,

                'type' => 'photo',

                'category' => $categoryId,

                'category_id' => $categoryId,
                'category_en' => $categoryEn,
                'category_ar' => $categoryAr,

                'caption' => 'Cuplikan kegiatan belajar, ibadah, karya, dan kebersamaan siswa di Al Mustaqbal.',

                'caption_id' => 'Cuplikan kegiatan belajar, ibadah, karya, dan kebersamaan siswa di Al Mustaqbal.',
                'caption_en' => 'A glimpse of learning, worship, creativity, and student life at Al Mustaqbal.',
                'caption_ar' => 'لمحات من التعلّم والعبادة والإبداع وحياة الطلاب في مدرسة المستقبل.',

                'media_url' => $image,

                'is_published' => true,
                'published_at' => now()->subDays($index),
            ];
        }

        return $items;
    }
}
