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
        return [...$this->facilityItems(), ...$this->momentItems()];
    }

    /** @return array<int, array<string, mixed>> */
    private function facilityItems(): array
    {
        $facilities = [
            ['Fasilitas Multimedia & Lab IT', 'Multimedia Facilities & IT Lab', 'مرافق الوسائط المتعددة ومختبر تقنية المعلومات', 'Perangkat multimedia dan ruang teknologi untuk mendukung literasi digital serta pembelajaran berbasis IT.', 'Multimedia tools and technology spaces supporting digital literacy and IT-enabled learning.', 'أدوات ومساحات تقنية تدعم الثقافة الرقمية والتعلم المعتمد على تقنية المعلومات.', 'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-01/cb44c802-f387-4b57-83f3-c92009c9a4cc.webp'],
            ['Mushallah', 'Mushallah', 'المصلى', 'Ruang ibadah yang mendukung pembiasaan shalat berjamaah dan kegiatan keislaman siswa.', 'A dedicated worship space supporting congregational prayer and students’ Islamic routines.', 'مساحة للعبادة تدعم الصلاة جماعة والعادات الإسلامية اليومية للطلاب.', 'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-02/c424002c-e140-4504-9d0b-0f0d75c13d1e.webp'],
            ['Aula Multifungsi', 'Multifunction Hall', 'قاعة متعددة الأغراض', 'Ruang bersama untuk kegiatan sekolah, presentasi, pertemuan, dan aktivitas komunitas.', 'A shared venue for school activities, presentations, meetings, and community events.', 'قاعة للأنشطة المدرسية والعروض والاجتماعات والفعاليات المجتمعية.', 'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-03/cd0a0ef6-4849-4826-b263-060dedf10487.webp'],
            ['Kolam Renang', 'Swimming Pool', 'المسبح', 'Fasilitas aktivitas air untuk mendukung latihan renang, kebugaran, dan keberanian anak.', 'An aquatic facility supporting swimming practice, fitness, confidence, and water skills.', 'مرفق مائي يدعم تدريب السباحة واللياقة والثقة ومهارات السلامة في الماء.', 'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-04/a60f0c43-cf46-4a3f-b3e9-cb526c9689f8.webp'],
            ['Perpustakaan & Laboratorium', 'Library & Laboratory', 'المكتبة والمختبر', 'Ruang untuk membaca, bereksperimen, mengamati, dan membangun kebiasaan belajar mandiri.', 'Spaces for reading, experimenting, observing, and building independent learning habits.', 'مساحات للقراءة والتجربة والملاحظة وبناء عادات التعلم المستقل.', 'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-05/91d73274-fa5c-49c3-93cf-1a0cf695550b.webp'],
            ['Konseling Psikologis', 'Psychological Counseling', 'الإرشاد النفسي', 'Layanan pendampingan psikologis untuk membantu perkembangan anak dan komunikasi sekolah dengan keluarga.', 'Psychological support that helps children’s development and strengthens school-family communication.', 'دعم نفسي يساعد نمو الطفل ويعزز التواصل بين المدرسة والأسرة.', 'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-06/48eb28aa-2032-4c33-a592-87064b42b70c.webp'],
            ['Kelas Orang Tua', 'Parents Class', 'صف الوالدين', 'Ruang belajar bersama orang tua untuk menyelaraskan pendampingan anak di sekolah dan di rumah.', 'A learning space for parents to align support for children at school and at home.', 'مساحة تعلم للوالدين لتوحيد أساليب دعم الطفل في المدرسة والمنزل.', 'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-07/d6ebd008-c54c-4e13-8335-22163e1de777.webp'],
            ['Layanan Kesehatan Gigi', 'Dental Health Care', 'رعاية صحة الأسنان', 'Dukungan edukasi dan perhatian terhadap kesehatan gigi sebagai bagian dari kebiasaan hidup sehat.', 'Education and support for dental health as part of healthy daily habits.', 'توعية ودعم لصحة الأسنان ضمن العادات اليومية الصحية.', 'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-08/514f399c-5fc1-4bdf-9618-5a7bae302bfd.webp'],
            ['Sekolah Full Day', 'Full Day School Service', 'خدمة الدوام المدرسي الكامل', 'Pendampingan kegiatan anak dalam jadwal sekolah yang lebih panjang, terstruktur, aman, dan terarah.', 'Structured and supervised support for children across a longer, safe, and purposeful school day.', 'رعاية منظمة وآمنة للطفل خلال يوم مدرسي أطول وأكثر تكاملًا.', 'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-09/585f4b9b-1901-49b4-b79a-35cd5765131f.webp'],
        ];

        return array_map(
            static fn (array $facility, int $index): array => [
                'title' => $facility[0],
                'title_id' => $facility[0],
                'title_en' => $facility[1],
                'title_ar' => $facility[2],
                'type' => 'photo',
                'category' => 'Fasilitas',
                'category_id' => 'Fasilitas',
                'category_en' => 'Facilities',
                'category_ar' => 'المرافق',
                'caption' => $facility[3],
                'caption_id' => $facility[3],
                'caption_en' => $facility[4],
                'caption_ar' => $facility[5],
                'media_url' => $facility[6],
                'is_published' => true,
                'show_on_homepage' => true,
                'show_on_gallery_page' => false,
                'published_at' => now()->subDays($index),
            ],
            $facilities,
            array_keys($facilities),
        );
    }

    /** @return array<int, array<string, mixed>> */
    private function momentItems(): array
    {
        $media = [
            ['https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1400&q=82', 'Kegiatan', 'Activities', 'الأنشطة'],
            ['https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1400&q=82', 'Pembelajaran', 'Learning', 'التعلّم'],
            ['https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=1400&q=82', 'Prestasi', 'Achievements', 'الإنجازات'],
            ['https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=1400&q=82', 'Program', 'Programs', 'البرامج'],
            ['https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1400&q=82', 'Kreativitas', 'Creativity', 'الإبداع'],
            ['https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&w=1400&q=82', 'Sains', 'Science', 'العلوم'],
        ];

        return array_map(
            static function (array $item, int $index): array {
                $number = $index + 1;

                return [
                    'title' => 'Momen Al Mustaqbal '.$number,
                    'title_id' => 'Momen Al Mustaqbal '.$number,
                    'title_en' => 'Al Mustaqbal Moment '.$number,
                    'title_ar' => 'لحظة من المستقبل '.$number,
                    'type' => 'photo',
                    'category' => $item[1],
                    'category_id' => $item[1],
                    'category_en' => $item[2],
                    'category_ar' => $item[3],
                    'caption' => 'Cuplikan kegiatan belajar, ibadah, karya, dan kebersamaan siswa di Al Mustaqbal.',
                    'caption_id' => 'Cuplikan kegiatan belajar, ibadah, karya, dan kebersamaan siswa di Al Mustaqbal.',
                    'caption_en' => 'A glimpse of learning, worship, creativity, and student life at Al Mustaqbal.',
                    'caption_ar' => 'لمحات من التعلّم والعبادة والإبداع وحياة الطلاب في مدرسة المستقبل.',
                    'media_url' => $item[0],
                    'is_published' => true,
                    'show_on_homepage' => false,
                    'show_on_gallery_page' => true,
                    'published_at' => now()->subDays(30 + $index),
                ];
            },
            $media,
            array_keys($media),
        );
    }
}
