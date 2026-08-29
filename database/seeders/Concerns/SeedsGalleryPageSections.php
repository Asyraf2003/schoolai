<?php

namespace Database\Seeders\Concerns;

use App\Models\GalleryItem;
use App\Models\GalleryPageSection;
use Illuminate\Support\Facades\Schema;

trait SeedsGalleryPageSections
{
    private function seedGalleryPageSections(): void
    {
        if (
            ! Schema::hasTable('gallery_page_sections')
            || ! Schema::hasTable('gallery_item_gallery_page_section')
        ) {
            return;
        }

        $this->seedFacilitiesSection();

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

            $placements = [];

            foreach ($mediaItems as $mediaIndex => $media) {
                $item = GalleryItem::withTrashed()->firstOrNew([
                    'type' => $media['type'],
                    'media_url' => $media['media_url'],
                ]);

                if (! $item->exists) {
                    $item->fill([
                        'title' => $sectionData['title_id'].' '.($mediaIndex + 1),
                        'title_id' => $sectionData['title_id'].' '.($mediaIndex + 1),
                        'title_en' => $sectionData['title_en'].' '.($mediaIndex + 1),
                        'type' => $media['type'],
                        'category' => 'Galeri Halaman',
                        'category_id' => 'Galeri Halaman',
                        'category_en' => 'Gallery Page',
                        'media_url' => $media['media_url'],
                        'is_published' => true,
                        'show_on_homepage' => false,
                        'show_on_gallery_page' => false,
                        'published_at' => now()->subDays(($sectionIndex * 4) + $mediaIndex),
                    ]);
                    $item->save();
                }

                if ($item->trashed()) {
                    $item->restore();
                }

                $placements[$item->getKey()] = [
                    'sort_order' => $mediaIndex + 1,
                    'is_published' => true,
                ];
            }

            $section->items()->sync($placements);
        }
    }

    private function seedFacilitiesSection(): void
    {
        $section = GalleryPageSection::withTrashed()->firstOrNew([
            'title_id' => 'Fasilitas',
        ]);
        $section->fill([
            'title_id' => 'Fasilitas',
            'title_en' => 'Facilities',
            'title_ar' => 'المرافق',
            'description_id' => 'Ruang dan layanan pendukung yang dirancang untuk membuat kegiatan belajar, ibadah, kesehatan, dan pendampingan keluarga berjalan lebih utuh.',
            'description_en' => 'Spaces and support services designed to strengthen learning, worship, wellbeing, physical activity, and partnership with families.',
            'description_ar' => 'مساحات وخدمات مساندة صُممت لدعم التعلم والعبادة والصحة والنشاط البدني والشراكة مع الأسرة.',
            'is_published' => true,
        ]);
        $section->save();

        if ($section->trashed()) {
            $section->restore();
        }

        $placements = GalleryItem::query()
            ->where('category_id', 'Fasilitas')
            ->homepage()
            ->ordered()
            ->get()
            ->mapWithKeys(fn (GalleryItem $item, int $index): array => [
                $item->getKey() => [
                    'sort_order' => $index + 1,
                    'is_published' => true,
                ],
            ])
            ->all();

        $section->items()->sync($placements);
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
                'description_en' => 'Moments when students observe, experiment, ask questions, and discover answers through authentic learning experiences.',
                'description_ar' => 'لحظات يلاحظ فيها الطلاب ويجرّبون ويطرحون الأسئلة ويكتشفون الإجابات من خلال تجارب تعلّم واقعية.',
                'is_published' => true,
                'media' => [
                    $this->schoolPhoto('labit'),
                    $this->schoolPhoto('perpustakaan'),
                    $this->schoolPhoto('renang2'),
                    $this->schoolPhoto('haji2'),
                ],
            ],
            [
                'title_id' => 'Karya, Seni, dan Keberanian',
                'title_en' => 'Creativity, Art, and Courage',
                'title_ar' => 'الإبداع والفن والشجاعة',
                'description_id' => 'Ruang bagi siswa untuk berkarya, tampil, menyampaikan gagasan, dan membangun keberanian dengan cara yang bermakna.',
                'description_en' => 'A space for students to create, perform, express their ideas, and build courage in meaningful ways.',
                'description_ar' => 'مساحة تتيح للطلاب الإبداع وتقديم أعمالهم والتعبير عن أفكارهم وبناء الثقة والشجاعة بأساليب هادفة.',
                'is_published' => true,
                'media' => [
                    $this->schoolPhoto('pidato'),
                    $this->schoolPhoto('taekwondo'),
                    $this->schoolPhoto('haji'),
                    $this->schoolPhoto('aula'),
                ],
            ],
            [
                'title_id' => 'Kebersamaan dan Karakter',
                'title_en' => 'Togetherness and Character',
                'title_ar' => 'التآلف وبناء الشخصية',
                'description_id' => 'Kebersamaan di sekolah menjadi tempat bertumbuhnya adab, tanggung jawab, kepedulian, dan rasa saling menghargai.',
                'description_en' => 'Life together at school nurtures good character, responsibility, care, and mutual respect.',
                'description_ar' => 'تُسهم الحياة المشتركة في المدرسة في تنمية الأدب والمسؤولية والاهتمام بالآخرين والاحترام المتبادل.',
                'is_published' => true,
                'media' => [
                    $this->schoolPhoto('solatjamaah'),
                    $this->schoolPhoto('tahfiz'),
                    $this->schoolPhoto('ibadah'),
                    $this->schoolPhoto('fullday'),
                ],
            ],
        ];
    }

    /** @return array{type: string, media_url: string} */
    private function schoolPhoto(string $key): array
    {
        return [
            'type' => 'photo',
            'media_url' => (string) config('media.static.school_life.'.$key),
        ];
    }
}
