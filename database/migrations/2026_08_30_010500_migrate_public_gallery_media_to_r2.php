<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hero_slides')) {
            DB::table('hero_slides')->delete();
        }

        if (! Schema::hasTable('gallery_items') || ! DB::table('gallery_items')->exists()) {
            return;
        }

        foreach ($this->facilities() as $title => $key) {
            DB::table('gallery_items')->where('title_id', $title)->update([
                'type' => 'photo',
                'media_url' => $this->url($key),
                'updated_at' => now(),
            ]);
        }

        foreach ($this->moments() as $oldTitle => $moment) {
            DB::table('gallery_items')->where('title_id', $oldTitle)->update([
                'title' => $moment[1],
                'title_id' => $moment[1],
                'title_en' => $moment[2],
                'title_ar' => $moment[3],
                'type' => 'photo',
                'category' => $moment[4],
                'category_id' => $moment[4],
                'category_en' => $moment[5],
                'category_ar' => $moment[6],
                'media_url' => $this->url($moment[0]),
                'updated_at' => now(),
            ]);
        }

        $this->ensureMoment('taekwondo', 'Taekwondo', 'Taekwondo', 'التايكوندو', 'Prestasi', 'Achievements', 'الإنجازات', 16);
        $this->ensureMoment('tahfiz', 'Tahfidz Al-Qur’an', 'Qur’an Memorization', 'تحفيظ القرآن', 'Pembelajaran', 'Learning', 'التعلّم', 17);
        $this->rebuildPlacements();
        $this->removeLegacyStockRows();
    }

    public function down(): void
    {
        // Retired stock and local-public references are intentionally not restored.
    }

    /** @return array<string, string> */
    private function facilities(): array
    {
        return [
            'Fasilitas Multimedia & Lab IT' => 'labit',
            'Mushallah' => 'mushalla',
            'Aula Multifungsi' => 'aula',
            'Kolam Renang' => 'renang',
            'Perpustakaan & Laboratorium' => 'perpustakaan',
            'Konseling Psikologis' => 'psikolog',
            'Kelas Orang Tua' => 'parenting',
            'Layanan Kesehatan Gigi' => 'gigianak',
            'Sekolah Full Day' => 'fullday',
        ];
    }

    /** @return array<string, array<int, string>> */
    private function moments(): array
    {
        return [
            'Momen Al Mustaqbal 1' => ['haji', 'Manasik Haji', 'Hajj Practice', 'مناسك الحج', 'Program', 'Programs', 'البرامج'],
            'Momen Al Mustaqbal 2' => ['haji2', 'Praktik Manasik Haji', 'Hajj Practice Session', 'تطبيق مناسك الحج', 'Program', 'Programs', 'البرامج'],
            'Momen Al Mustaqbal 3' => ['ibadah', 'Pembiasaan Ibadah', 'Worship Habits', 'التعويد على العبادة', 'Kegiatan', 'Activities', 'الأنشطة'],
            'Momen Al Mustaqbal 4' => ['pidato', 'Khitobah Siswa', 'Student Khitobah', 'خطابة الطلاب', 'Kreativitas', 'Creativity', 'الإبداع'],
            'Momen Al Mustaqbal 5' => ['renang2', 'Latihan Renang', 'Swimming Practice', 'تدريب السباحة', 'Kegiatan', 'Activities', 'الأنشطة'],
            'Momen Al Mustaqbal 6' => ['solatjamaah', 'Salat Berjamaah', 'Congregational Prayer', 'الصلاة جماعة', 'Karakter', 'Character', 'الشخصية'],
        ];
    }

    private function ensureMoment(
        string $key,
        string $titleId,
        string $titleEn,
        string $titleAr,
        string $categoryId,
        string $categoryEn,
        string $categoryAr,
        int $sortOrder,
    ): void {
        $url = $this->url($key);
        if (DB::table('gallery_items')->where('media_url', $url)->whereNull('deleted_at')->exists()) {
            return;
        }

        DB::table('gallery_items')->insert([
            'title' => $titleId,
            'title_id' => $titleId,
            'title_en' => $titleEn,
            'title_ar' => $titleAr,
            'type' => 'photo',
            'category' => $categoryId,
            'category_id' => $categoryId,
            'category_en' => $categoryEn,
            'category_ar' => $categoryAr,
            'caption' => 'Cuplikan kegiatan siswa di Al Mustaqbal.',
            'caption_id' => 'Cuplikan kegiatan siswa di Al Mustaqbal.',
            'caption_en' => 'A glimpse of student life at Al Mustaqbal.',
            'caption_ar' => 'لمحة من حياة الطلاب في مدرسة المستقبل.',
            'media_url' => $url,
            'sort_order' => $sortOrder,
            'is_published' => true,
            'show_on_homepage' => false,
            'show_on_gallery_page' => true,
            'published_at' => now()->subDays(30 + $sortOrder),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function rebuildPlacements(): void
    {
        if (! Schema::hasTable('gallery_page_sections') || ! Schema::hasTable('gallery_item_gallery_page_section')) {
            return;
        }

        $sections = [
            'Fasilitas' => ['labit', 'mushalla', 'aula', 'renang', 'perpustakaan', 'psikolog', 'parenting', 'gigianak', 'fullday'],
            'Belajar dengan Rasa Ingin Tahu' => ['labit', 'perpustakaan', 'renang2', 'haji2'],
            'Karya, Seni, dan Keberanian' => ['pidato', 'taekwondo', 'haji', 'aula'],
            'Kebersamaan dan Karakter' => ['solatjamaah', 'tahfiz', 'ibadah', 'fullday'],
        ];

        foreach ($sections as $title => $keys) {
            $sectionId = DB::table('gallery_page_sections')->where('title_id', $title)->value('id');
            if ($sectionId === null) {
                continue;
            }

            DB::table('gallery_item_gallery_page_section')->where('gallery_page_section_id', $sectionId)->delete();
            foreach ($keys as $index => $key) {
                $itemId = DB::table('gallery_items')
                    ->where('media_url', $this->url($key))
                    ->whereNull('deleted_at')
                    ->orderBy('id')
                    ->value('id');
                if ($itemId === null) {
                    continue;
                }

                DB::table('gallery_item_gallery_page_section')->insert([
                    'gallery_item_id' => $itemId,
                    'gallery_page_section_id' => $sectionId,
                    'sort_order' => $index + 1,
                    'is_published' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function removeLegacyStockRows(): void
    {
        $legacy = DB::table('gallery_items')->where(function ($query): void {
            $query->whereNotNull('legacy_gallery_page_media_item_id')
                ->orWhere('media_url', 'like', 'https://images.unsplash.com/%')
                ->orWhere('media_url', 'like', 'https://media.almustaqbal.sch.id/testimonials/backgrounds/testimonial-nature-%')
                ->orWhere('media_url', 'https://www.youtube.com/embed/n5cW4FpGvhI');
        })->pluck('id');

        if ($legacy->isNotEmpty() && Schema::hasTable('gallery_item_gallery_page_section')) {
            DB::table('gallery_item_gallery_page_section')->whereIn('gallery_item_id', $legacy)->delete();
        }
        if ($legacy->isNotEmpty()) {
            DB::table('gallery_items')->whereIn('id', $legacy)->delete();
        }

        if (Schema::hasTable('gallery_page_media_items')) {
            DB::table('gallery_page_media_items')->where(function ($query): void {
                $query->where('media_url', 'like', 'https://images.unsplash.com/%')
                    ->orWhere('media_url', 'https://www.youtube.com/embed/n5cW4FpGvhI');
            })->delete();
        }
    }

    private function url(string $key): string
    {
        return (string) config('media.static.school_life.'.$key);
    }
};
