<?php

namespace Database\Seeders;

use App\Models\PpdbSetting;
use App\Models\PpdbShowcaseItem;
use Illuminate\Database\Seeder;

final class PpdbSeeder extends Seeder
{
    public function run(): void
    {
        $setting = PpdbSetting::query()->firstOrNew();

        $setting->fill([
            'registration_url' => PpdbSetting::DEFAULT_REGISTRATION_URL,
            'information_url' => 'https://almustaqbal.sch.id/ppdb',
            'is_active' => true,
        ])->save();

        foreach ($this->showcaseItems() as $index => $attributes) {
            $item = PpdbShowcaseItem::withTrashed()
                ->firstOrNew([
                    'audience' => $attributes['audience'],
                    'title_id' => $attributes['title_id'],
                ]);

            $item->fill(array_merge($attributes, [
                'sort_order' => $index + 1,
            ]));

            $item->save();

            if ($item->trashed()) {
                $item->restore();
            }
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function showcaseItems(): array
    {
        return [
            [
                'audience' => PpdbShowcaseItem::AUDIENCE_PARENTS,

                'title_id' => 'Kenali Al Mustaqbal',
                'title_en' => 'Discover Al Mustaqbal',
                'title_ar' => 'تعرّف على المستقبل',

                'description_id' => 'Pelajari nilai, program, dan lingkungan belajar sebelum mendaftar.',
                'description_en' => 'Explore our values, programs, and learning environment before applying.',
                'description_ar' => 'تعرّف على قيمنا وبرامجنا وبيئة التعلّم لدينا قبل التسجيل.',

                'media_type' => PpdbShowcaseItem::MEDIA_PHOTO,
                'media_url' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1400&q=82',
            ],

            [
                'audience' => PpdbShowcaseItem::AUDIENCE_PARENTS,

                'title_id' => 'Siapkan Data Pendaftaran',
                'title_en' => 'Prepare Registration Details',
                'title_ar' => 'جهّز بيانات التسجيل',

                'description_id' => 'Siapkan data calon siswa dan kontak orang tua agar proses lebih cepat.',
                'description_en' => 'Prepare student details and parent contacts for a smoother process.',
                'description_ar' => 'جهّز بيانات الطالب وبيانات التواصل مع ولي الأمر لتسهيل وتسريع إجراءات التسجيل.',

                'media_type' => PpdbShowcaseItem::MEDIA_PHOTO,
                'media_url' => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=1400&q=82',
            ],

            [
                'audience' => PpdbShowcaseItem::AUDIENCE_PARENTS,

                'title_id' => 'Kunjungi dan Kenali Lingkungan',
                'title_en' => 'Visit and Explore the Environment',
                'title_ar' => 'زُر المدرسة وتعرّف على بيئتها',

                'description_id' => 'Kenali suasana sekolah, ruang belajar, dan aktivitas siswa agar keluarga mendapat gambaran yang utuh.',
                'description_en' => 'Explore the school atmosphere, learning spaces, and student activities to get a complete picture.',
                'description_ar' => 'تعرّف على أجواء المدرسة ومساحات التعلّم وأنشطة الطلاب لتكوين صورة متكاملة عن البيئة التعليمية.',

                'media_type' => PpdbShowcaseItem::MEDIA_PHOTO,
                'media_url' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1400&q=82',
            ],

            [
                'audience' => PpdbShowcaseItem::AUDIENCE_SCHOOL,

                'title_id' => 'Verifikasi dan Pendampingan',
                'title_en' => 'Verification and Support',
                'title_ar' => 'التحقق والمتابعة',

                'description_id' => 'Tim sekolah memeriksa data dan mendampingi keluarga pada tahapan berikutnya.',
                'description_en' => 'The school team verifies details and supports families through the next steps.',
                'description_ar' => 'يتحقق فريق المدرسة من البيانات ويقدّم الدعم للأسرة خلال المراحل التالية.',

                'media_type' => PpdbShowcaseItem::MEDIA_PHOTO,
                'media_url' => 'https://images.unsplash.com/photo-1529390079861-591de354faf5?auto=format&fit=crop&w=1400&q=82',
            ],

            [
                'audience' => PpdbShowcaseItem::AUDIENCE_SCHOOL,

                'title_id' => 'Konfirmasi Hasil',
                'title_en' => 'Confirm the Result',
                'title_ar' => 'تأكيد النتيجة',

                'description_id' => 'Informasi hasil dan tindak lanjut disampaikan melalui kontak resmi sekolah.',
                'description_en' => 'Results and follow-up information are shared through official school contacts.',
                'description_ar' => 'تُرسل معلومات النتيجة والإجراءات اللاحقة عبر قنوات التواصل الرسمية للمدرسة.',

                'media_type' => PpdbShowcaseItem::MEDIA_PHOTO,
                'media_url' => 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&w=1400&q=82',
            ],

            [
                'audience' => PpdbShowcaseItem::AUDIENCE_SCHOOL,

                'title_id' => 'Orientasi Awal Siswa',
                'title_en' => 'Student Orientation',
                'title_ar' => 'التهيئة الأولية للطالب',

                'description_id' => 'Siswa dan keluarga mendapat arahan awal agar transisi menuju lingkungan belajar baru terasa lebih nyaman.',
                'description_en' => 'Students and families receive initial guidance for a smoother transition into the new learning environment.',
                'description_ar' => 'يحصل الطالب وأسرته على التوجيه الأولي لضمان انتقال أكثر سلاسة وراحة إلى بيئة التعلّم الجديدة.',

                'media_type' => PpdbShowcaseItem::MEDIA_PHOTO,
                'media_url' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1400&q=82',
            ],
        ];
    }
}
