<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;

final class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->articles() as $index => $attributes) {
            $article = Article::withTrashed()->firstOrNew([
                'slug' => $attributes['slug'],
            ]);

            $article->fill(array_merge($attributes, [
                'article_source' => Article::SOURCE_NATIVE,
                'article_status' => Article::STATUS_PUBLISHED,
                'published_at' => now()->subDays($index),
                'scheduled_at' => null,
                'author' => 'Tim Al Mustaqbal',
            ]));

            $article->save();

            if ($article->trashed()) {
                $article->restore();
            }
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function articles(): array
    {
        return [
            $this->article(
                'belajar-bermakna-dimulai-dari-rasa-ingin-tahu',

                'Belajar Bermakna Dimulai dari Rasa Ingin Tahu',
                'Meaningful Learning Begins with Curiosity',
                'التعلّم الهادف يبدأ بالفضول',

                'Anak belajar paling dalam ketika berani bertanya, mencoba, lalu merefleksikan pengalamannya.',
                'Children learn most deeply when they dare to ask questions, explore, and reflect on their experiences.',
                'يتعلّم الطفل بصورة أعمق حين يجرؤ على السؤال والتجربة ثم يتأمل في خبراته.',

                ['Pendidikan', 'Program'],

                'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1800&q=82',
            ),

            $this->article(
                'prestasi-tumbuh-dari-proses-yang-konsisten',

                'Prestasi Tumbuh dari Proses yang Konsisten',
                'Achievement Grows from a Consistent Process',
                'الإنجاز ثمرة مسيرة مستمرة',

                'Prestasi bukan hanya hasil akhir, tetapi jejak disiplin, dukungan keluarga, dan keberanian untuk terus belajar.',
                'Achievement is not merely a final result, but the outcome of discipline, family support, and the courage to keep learning.',
                'الإنجاز ليس مجرد نتيجة نهائية، بل هو ثمرة الانضباط ودعم الأسرة والشجاعة على مواصلة التعلّم.',

                ['Prestasi'],

                'https://images.unsplash.com/photo-1535982330050-f1c2fb79ff78?auto=format&fit=crop&w=1800&q=82',
            ),

            $this->article(
                'qiii-menjadi-kompas-kehidupan-sekolah',

                'QIII Menjadi Kompas Kehidupan Sekolah',
                'QIII as the Compass of School Life',
                'قيم QIII بوصلة الحياة المدرسية',

                'Qurani, inspiratif, inovatif, dan integritas hadir dalam keputusan kecil yang dilakukan setiap hari.',
                'Qur’anic values, inspiration, innovation, and integrity are reflected in the small decisions made every day.',
                'تتجسّد القيم القرآنية والإلهام والابتكار والنزاهة في القرارات الصغيرة التي نتخذها كل يوم.',

                ['Program', 'Pendidikan'],

                'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1800&q=82',
            ),

            $this->article(
                'sekolah-dan-keluarga-bertumbuh-sebagai-satu-tim',

                'Sekolah dan Keluarga Bertumbuh sebagai Satu Tim',
                'School and Family Grow as One Team',
                'المدرسة والأسرة تنموان معًا كفريق واحد',

                'Kolaborasi yang jujur antara sekolah dan keluarga membantu anak tumbuh tanpa kehilangan iman dan jati dirinya.',
                'Honest collaboration between school and family helps children grow without losing their faith or identity.',
                'يساعد التعاون الصادق بين المدرسة والأسرة الطفل على النمو مع الحفاظ على إيمانه وهويته.',

                ['Kegiatan', 'Pendidikan'],

                'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=1800&q=82',
            ),

            $this->article(
                'proyek-kreatif-yang-melatih-keberanian-anak',

                'Proyek Kreatif yang Melatih Keberanian Anak',
                'Creative Projects that Build Children’s Courage',
                'مشروعات إبداعية تنمّي شجاعة الطفل',

                'Karya sederhana menjadi ruang aman bagi anak untuk menyampaikan gagasan, menerima umpan balik, dan mencoba kembali.',
                'Simple creative projects provide children with a safe space to express ideas, receive feedback, and try again.',
                'توفر المشروعات الإبداعية البسيطة للطفل مساحة آمنة للتعبير عن أفكاره وتلقي الملاحظات والمحاولة من جديد.',

                ['Kegiatan', 'Program'],

                'https://images.unsplash.com/photo-1544717297-fa95b6ee9643?auto=format&fit=crop&w=1800&q=82',
            ),

            $this->article(
                'adab-sebelum-prestasi',

                'Adab Sebelum Prestasi',
                'Character Before Achievement',
                'الأدب قبل الإنجاز',

                'Ilmu dan prestasi memperoleh makna ketika tumbuh bersama adab, tanggung jawab, dan kepedulian kepada sesama.',
                'Knowledge and achievement gain true meaning when they grow alongside character, responsibility, and care for others.',
                'يكتسب العلم والإنجاز معناهما الحقيقي حين يقترنان بالأدب والمسؤولية والاهتمام بالآخرين.',

                ['Pendidikan'],

                'https://images.unsplash.com/photo-1491841550275-ad7854e35ca6?auto=format&fit=crop&w=1800&q=82',
            ),

            $this->article(
                'eksperimen-sains-melatih-cara-berpikir',

                'Eksperimen Sains Melatih Cara Berpikir',
                'Science Experiments Train the Way We Think',
                'التجارب العلمية تنمّي مهارات التفكير',

                'Eksperimen sederhana membantu siswa belajar mengamati, membuat dugaan, menguji ide, dan berani memperbaiki kesimpulan.',
                'Simple experiments help students learn to observe, form hypotheses, test ideas, and confidently revise their conclusions.',
                'تساعد التجارب البسيطة الطلاب على تعلّم الملاحظة وصياغة الفرضيات واختبار الأفكار ومراجعة استنتاجاتهم بثقة.',

                ['Sains', 'Pendidikan'],

                'https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&w=1800&q=82',
            ),

            $this->article(
                'literasi-tumbuh-dari-kebiasaan-kecil',

                'Literasi Tumbuh dari Kebiasaan Kecil',
                'Literacy Grows from Small Habits',
                'تنمو الثقافة القرائية من العادات الصغيرة',

                'Kebiasaan membaca, bercerita, dan berdiskusi setiap hari membangun hubungan anak dengan ilmu secara alami dan berkelanjutan.',
                'Daily habits of reading, storytelling, and discussion build a natural and lasting relationship between children and knowledge.',
                'تسهم عادات القراءة والسرد والنقاش اليومية في بناء علاقة طبيعية ومستدامة بين الطفل والمعرفة.',

                ['Literasi', 'Pendidikan'],

                'https://images.unsplash.com/photo-1491841550275-ad7854e35ca6?auto=format&fit=crop&w=1800&q=82',
            ),
        ];
    }

    /**
     * @param  array<int, string>  $tags
     * @return array<string, mixed>
     */
    private function article(
        string $slug,
        string $titleId,
        string $titleEn,
        string $titleAr,
        string $descriptionId,
        string $descriptionEn,
        string $descriptionAr,
        array $tags,
        string $thumbnail,
    ): array {
        return [
            'slug' => $slug,

            'title_id' => $titleId,
            'title_en' => $titleEn,
            'title_ar' => $titleAr,

            'subtitle_id' => $descriptionId,
            'subtitle_en' => $descriptionEn,
            'subtitle_ar' => $descriptionAr,

            'description_id' => $descriptionId,
            'description_en' => $descriptionEn,
            'description_ar' => $descriptionAr,

            'content_id' => implode('', [
                '<p>'.$descriptionId.'</p>',
                '<p>Konten dummy ini dapat diganti melalui canvas artikel admin.</p>',
            ]),

            'content_en' => implode('', [
                '<p>'.$descriptionEn.'</p>',
                '<p>This sample content can be replaced through the article canvas in the admin panel.</p>',
            ]),

            'content_ar' => implode('', [
                '<p>'.$descriptionAr.'</p>',
                '<p>يمكن استبدال هذا المحتوى التجريبي من خلال محرر المقالات في لوحة الإدارة.</p>',
            ]),

            'tags' => $tags,

            'word_count' => 38,

            'thumbnail_url' => $thumbnail,

            'link_id' => url('/artikel/'.$slug),
            'link_en' => url('/artikel/'.$slug),
            'link_ar' => url('/artikel/'.$slug),
        ];
    }
}
