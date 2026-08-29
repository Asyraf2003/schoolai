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
            ['Fasilitas Multimedia & Lab IT', 'Multimedia Facilities & IT Lab', 'مرافق الوسائط المتعددة ومختبر تقنية المعلومات', 'Perangkat multimedia dan ruang teknologi untuk mendukung literasi digital serta pembelajaran berbasis IT.', 'Multimedia tools and technology spaces supporting digital literacy and IT-enabled learning.', 'أدوات ومساحات تقنية تدعم الثقافة الرقمية والتعلم المعتمد على تقنية المعلومات.', 'labit'],
            ['Mushallah', 'Mushallah', 'المصلى', 'Ruang ibadah yang mendukung pembiasaan shalat berjamaah dan kegiatan keislaman siswa.', 'A dedicated worship space supporting congregational prayer and students’ Islamic routines.', 'مساحة للعبادة تدعم الصلاة جماعة والعادات الإسلامية اليومية للطلاب.', 'mushalla'],
            ['Aula Multifungsi', 'Multifunction Hall', 'قاعة متعددة الأغراض', 'Ruang bersama untuk kegiatan sekolah, presentasi, pertemuan, dan aktivitas komunitas.', 'A shared venue for school activities, presentations, meetings, and community events.', 'قاعة للأنشطة المدرسية والعروض والاجتماعات والفعاليات المجتمعية.', 'aula'],
            ['Kolam Renang', 'Swimming Pool', 'المسبح', 'Fasilitas aktivitas air untuk mendukung latihan renang, kebugaran, dan keberanian anak.', 'An aquatic facility supporting swimming practice, fitness, confidence, and water skills.', 'مرفق مائي يدعم تدريب السباحة واللياقة والثقة ومهارات السلامة في الماء.', 'renang'],
            ['Perpustakaan & Laboratorium', 'Library & Laboratory', 'المكتبة والمختبر', 'Ruang untuk membaca, bereksperimen, mengamati, dan membangun kebiasaan belajar mandiri.', 'Spaces for reading, experimenting, observing, and building independent learning habits.', 'مساحات للقراءة والتجربة والملاحظة وبناء عادات التعلم المستقل.', 'perpustakaan'],
            ['Konseling Psikologis', 'Psychological Counseling', 'الإرشاد النفسي', 'Layanan pendampingan psikologis untuk membantu perkembangan anak dan komunikasi sekolah dengan keluarga.', 'Psychological support that helps children’s development and strengthens school-family communication.', 'دعم نفسي يساعد نمو الطفل ويعزز التواصل بين المدرسة والأسرة.', 'psikolog'],
            ['Kelas Orang Tua', 'Parents Class', 'صف الوالدين', 'Ruang belajar bersama orang tua untuk menyelaraskan pendampingan anak di sekolah dan di rumah.', 'A learning space for parents to align support for children at school and at home.', 'مساحة تعلم للوالدين لتوحيد أساليب دعم الطفل في المدرسة والمنزل.', 'parenting'],
            ['Layanan Kesehatan Gigi', 'Dental Health Care', 'رعاية صحة الأسنان', 'Dukungan edukasi dan perhatian terhadap kesehatan gigi sebagai bagian dari kebiasaan hidup sehat.', 'Education and support for dental health as part of healthy daily habits.', 'توعية ودعم لصحة الأسنان ضمن العادات اليومية الصحية.', 'gigianak'],
            ['Sekolah Full Day', 'Full Day School Service', 'خدمة الدوام المدرسي الكامل', 'Pendampingan kegiatan anak dalam jadwal sekolah yang lebih panjang, terstruktur, aman, dan terarah.', 'Structured and supervised support for children across a longer, safe, and purposeful school day.', 'رعاية منظمة وآمنة للطفل خلال يوم مدرسي أطول وأكثر تكاملًا.', 'fullday'],
        ];

        return array_map(
            static fn (array $facility, int $index): array => [
                'title' => $facility[0], 'title_id' => $facility[0],
                'title_en' => $facility[1], 'title_ar' => $facility[2],
                'type' => 'photo', 'category' => 'Fasilitas',
                'category_id' => 'Fasilitas', 'category_en' => 'Facilities',
                'category_ar' => 'المرافق', 'caption' => $facility[3],
                'caption_id' => $facility[3], 'caption_en' => $facility[4],
                'caption_ar' => $facility[5],
                'media_url' => config('media.static.school_life.'.$facility[6]),
                'is_published' => true, 'show_on_homepage' => true,
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
        $moments = [
            ['haji', 'Manasik Haji', 'Hajj Practice', 'مناسك الحج', 'Program', 'Programs', 'البرامج'],
            ['haji2', 'Praktik Manasik Haji', 'Hajj Practice Session', 'تطبيق مناسك الحج', 'Program', 'Programs', 'البرامج'],
            ['ibadah', 'Pembiasaan Ibadah', 'Worship Habits', 'التعويد على العبادة', 'Kegiatan', 'Activities', 'الأنشطة'],
            ['pidato', 'Khitobah Siswa', 'Student Khitobah', 'خطابة الطلاب', 'Kreativitas', 'Creativity', 'الإبداع'],
            ['renang2', 'Latihan Renang', 'Swimming Practice', 'تدريب السباحة', 'Kegiatan', 'Activities', 'الأنشطة'],
            ['solatjamaah', 'Salat Berjamaah', 'Congregational Prayer', 'الصلاة جماعة', 'Karakter', 'Character', 'الشخصية'],
            ['taekwondo', 'Taekwondo', 'Taekwondo', 'التايكوندو', 'Prestasi', 'Achievements', 'الإنجازات'],
            ['tahfiz', 'Tahfidz Al-Qur’an', 'Qur’an Memorization', 'تحفيظ القرآن', 'Pembelajaran', 'Learning', 'التعلّم'],
        ];

        return array_map(static fn (array $item, int $index): array => [
            'title' => $item[1], 'title_id' => $item[1],
            'title_en' => $item[2], 'title_ar' => $item[3],
            'type' => 'photo', 'category' => $item[4],
            'category_id' => $item[4], 'category_en' => $item[5],
            'category_ar' => $item[6],
            'caption' => 'Cuplikan kegiatan belajar, ibadah, karya, dan kebersamaan siswa di Al Mustaqbal.',
            'caption_id' => 'Cuplikan kegiatan belajar, ibadah, karya, dan kebersamaan siswa di Al Mustaqbal.',
            'caption_en' => 'A glimpse of learning, worship, creativity, and student life at Al Mustaqbal.',
            'caption_ar' => 'لمحات من التعلّم والعبادة والإبداع وحياة الطلاب في مدرسة المستقبل.',
            'media_url' => config('media.static.school_life.'.$item[0]),
            'is_published' => true, 'show_on_homepage' => false,
            'show_on_gallery_page' => true,
            'published_at' => now()->subDays(30 + $index),
        ], $moments, array_keys($moments));
    }
}
