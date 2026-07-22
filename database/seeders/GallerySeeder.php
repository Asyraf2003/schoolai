<?php

namespace Database\Seeders;

use App\Models\GalleryItem;
use App\Models\GalleryPageMediaItem;
use App\Models\GalleryPageSection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

final class GallerySeeder extends Seeder
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
            $item->forceFill(['sort_order' => $index + 1]);
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
            ['https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1400&q=82', 'Kegiatan', 'Activities', 'الأنشطة'],
            ['https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1400&q=82', 'Pembelajaran', 'Learning', 'التعلّم'],
            ['https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=1400&q=82', 'Prestasi', 'Achievements', 'الإنجازات'],
            ['https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=1400&q=82', 'Program', 'Programs', 'البرامج'],
            ['https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1400&q=82', 'Kreativitas', 'Creativity', 'الإبداع'],
            ['https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&w=1400&q=82', 'Sains', 'Science', 'العلوم'],
        ];

        $items = [];

        foreach ($media as $index => [$image, $categoryId, $categoryEn, $categoryAr]) {
            $number = $index + 1;
            $items[] = [
                'title' => 'Momen Al Mustaqbal '.$number,
                'title_id' => 'Momen Al Mustaqbal '.$number,
                'title_en' => 'Al Mustaqbal Moment '.$number,
                'title_ar' => 'لحظة من المستقبل '.$number,
                'type' => 'photo',
                'category' => $categoryId,
                'category_id' => $categoryId,
                'category_en' => $categoryEn,
                'category_ar' => $categoryAr,
                'caption' => 'Dokumentasi dummy untuk pratinjau galeri sekolah.',
                'caption_id' => 'Dokumentasi dummy untuk pratinjau galeri sekolah.',
                'caption_en' => 'Dummy documentation for the school gallery preview.',
                'caption_ar' => 'محتوى تجريبي لمعاينة معرض المدرسة.',
                'media_url' => $image,
                'is_published' => true,
                'published_at' => now()->subDays($index),
            ];
        }

        return $items;
    }

    private function seedGalleryPageSections(): void
    {
        if (! Schema::hasTable('gallery_page_sections') || ! Schema::hasTable('gallery_page_media_items')) {
            return;
        }

        foreach ($this->pageSections() as $sectionIndex => $sectionData) {
            $mediaItems = $sectionData['media'];
            unset($sectionData['media']);

            $section = GalleryPageSection::withTrashed()->firstOrNew([
                'title_id' => $sectionData['title_id'],
            ]);
            $section->fill($sectionData);
            $section->save();

            if ($section->trashed()) {
                $section->restore();
            }

            foreach ($mediaItems as $mediaIndex => $media) {
                $item = GalleryPageMediaItem::withTrashed()->firstOrNew([
                    'gallery_page_section_id' => $section->getKey(),
                    'media_url' => $media['media_url'],
                ]);
                $item->fill([
                    'gallery_page_section_id' => $section->getKey(),
                    'type' => $media['type'],
                    'media_url' => $media['media_url'],
                    'is_published' => true,
                    'published_at' => now()->subDays(($sectionIndex * 4) + $mediaIndex),
                ]);
                $item->save();

                if ($item->trashed()) {
                    $item->restore();
                }
            }
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function pageSections(): array
    {
        return [
            [
                'title_id' => 'Belajar dengan Rasa Ingin Tahu',
                'title_en' => 'Learning with Curiosity',
                'title_ar' => 'التعلّم بدافع الفضول',
                'description_id' => 'Momen ketika siswa mengamati, mencoba, bertanya, dan menemukan jawaban melalui pengalaman belajar nyata.',
                'description_en' => 'Moments where students observe, experiment, ask questions, and discover answers through real learning experiences.',
                'description_ar' => 'لحظات يلاحظ فيها الطلاب ويجرّبون ويسألون ويكتشفون الإجابات من خلال خبرات تعلّم حقيقية.',
                'is_published' => true,
                'media' => [
                    ['type' => 'photo', 'media_url' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&w=1600&q=82'],
                    ['type' => 'photo', 'media_url' => 'https://images.unsplash.com/photo-1532187863486-abf9dbad1b69?auto=format&fit=crop&w=1600&q=82'],
                    ['type' => 'photo', 'media_url' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1600&q=82'],
                    ['type' => 'video', 'media_url' => 'https://www.youtube.com/embed/n5cW4FpGvhI'],
                ],
            ],
            [
                'title_id' => 'Karya, Seni, dan Keberanian',
                'title_en' => 'Creativity, Art, and Courage',
                'title_ar' => 'الإبداع والفن والشجاعة',
                'description_id' => 'Ruang bagi siswa untuk berkarya, tampil, menyampaikan gagasan, dan membangun keberanian dengan cara yang bermakna.',
                'description_en' => 'A space for students to create, perform, express ideas, and build courage in meaningful ways.',
                'description_ar' => 'مساحة للطلاب للإبداع والعرض والتعبير عن أفكارهم وبناء الشجاعة بطرق هادفة.',
                'is_published' => true,
                'media' => [
                    ['type' => 'photo', 'media_url' => 'https://images.unsplash.com/photo-1544717297-fa95b6ee9643?auto=format&fit=crop&w=1600&q=82'],
                    ['type' => 'photo', 'media_url' => 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?auto=format&fit=crop&w=1600&q=82'],
                    ['type' => 'photo', 'media_url' => 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=1600&q=82'],
                    ['type' => 'photo', 'media_url' => 'https://images.unsplash.com/photo-1503095396549-807759245b35?auto=format&fit=crop&w=1600&q=82'],
                ],
            ],
            [
                'title_id' => 'Kebersamaan dan Karakter',
                'title_en' => 'Togetherness and Character',
                'title_ar' => 'التعاون وبناء الشخصية',
                'description_id' => 'Kebersamaan di sekolah menjadi tempat bertumbuhnya adab, tanggung jawab, kepedulian, dan rasa saling menghargai.',
                'description_en' => 'School togetherness becomes a place where character, responsibility, care, and mutual respect can grow.',
                'description_ar' => 'تصبح الحياة المشتركة في المدرسة بيئة ينمو فيها الأدب والمسؤولية والاهتمام والاحترام المتبادل.',
                'is_published' => true,
                'media' => [
                    ['type' => 'photo', 'media_url' => 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=1600&q=82'],
                    ['type' => 'photo', 'media_url' => 'https://images.unsplash.com/photo-1529390079861-591de354faf5?auto=format&fit=crop&w=1600&q=82'],
                    ['type' => 'photo', 'media_url' => 'https://images.unsplash.com/photo-1506869640319-fe1a24fd76dc?auto=format&fit=crop&w=1600&q=82'],
                    ['type' => 'photo', 'media_url' => 'https://images.unsplash.com/photo-1516627145497-ae6968895b74?auto=format&fit=crop&w=1600&q=82'],
                ],
            ],
        ];
    }
}
