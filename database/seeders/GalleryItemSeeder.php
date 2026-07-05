<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GalleryItemSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('gallery_items')) {
            $this->command?->warn('Tabel gallery_items belum ada. Jalankan migration dulu.');

            return;
        }

        $now = now();

        $items = [
            [
                'title_id' => 'Kenapa 95% Investor Gagal?',
                'title_en' => 'Why 95% of Investors Fail?',
                'youtube_id' => '1U6NGyPT1f8',
                'category_id' => 'Bisnis & Keuangan / Pasar Modal',
                'category_en' => 'Business & Finance / Capital Market',
                'caption_id' => 'Timothy Ronald membedakan aktivitas investasi dan spekulasi berdasarkan prinsip Benjamin Graham, serta menjelaskan mengapa 95% investor pemula menemui kegagalan akibat herding mentality dan ketidakmampuan mengendalikan emosi [00:05:50], [00:17:47], [00:24:39]. Melalui konsep Mr. Market dan Margin of Safety, video ini mengupas fundamental pasar modal yang harus dipahami agar tidak terjebak dalam siklus kerugian jangka pendek [00:09:23], [00:12:33], [00:19:45].',
                'caption_en' => "Timothy Ronald breaks down the core differences between investment and speculation based on Benjamin Graham's principles, explaining why 95% of retail investors fail due to herding mentality and poor emotional control [00:05:50], [00:17:47], [00:24:39]. Leveraging the concepts of Mr. Market and the Margin of Safety, this video highlights capital market fundamentals required to avoid short-term market traps [00:09:23], [00:12:33], [00:19:45].",
            ],
            [
                'title_id' => 'Mentalitas Pemenang: Kenapa 99% Orang Gagal',
                'title_en' => "Winner's Mentality: Why 99% of People Fail",
                'youtube_id' => '9ZwNQ_X2F7o',
                'category_id' => 'Pengembangan Diri / Motivasi & Bisnis',
                'category_en' => 'Self-Development / Motivation & Business',
                'caption_id' => 'Video ini mengupas tuntas perbedaan cara mengelola energi dan pola pikir antara kelompok 99% kelas ekonomi bawah dengan 1% individu beraset tinggi [00:01:08], [00:03:03], [00:27:26]. Timothy Ronald menjelaskan pentingnya memahami konsep pengungkit atau leverage—baik berupa modal, tenaga kerja, maupun media—untuk memutus rantai pertukaran waktu dan uang secara linear [00:15:49], [00:16:52], [00:20:48].',
                'caption_en' => 'This video thoroughly examines the distinct differences in energy management and mindset between the 99% macro-population and the top 1% high-net-worth individuals [00:01:08], [00:03:03], [00:27:26]. Timothy Ronald emphasizes mastering the concept of leverage—whether capital, labor, or media—to permanently break away from linear time-for-money trade-offs [00:15:49], [00:16:52], [00:20:48].',
            ],
            [
                'title_id' => '5 Industri Masa Depan Yang Bikin Lu Kaya (Jangan Sampai Telat)',
                'title_en' => "5 Future Industries That Will Make You Rich (Don't Be Late)",
                'youtube_id' => 'mB171ropLQs',
                'category_id' => 'Bisnis & Keuangan / Teknologi Masa Depan',
                'category_en' => 'Business & Finance / Future Technology',
                'caption_id' => 'Bukan seberapa keras Anda mendayung, melainkan kapal apa yang Anda naiki; Timothy Ronald memetakan lima industri masa depan yang akan mencetak kekayaan besar dalam 20 hingga 30 tahun mendatang [00:00:21], [00:01:07]. Kelima sektor tersebut meliputi kecerdasan buatan & robotik humanoid, blockchain, energi bersih, bioteknologi perpanjangan umur, serta ekonomi ruang angkasa [00:06:22], [00:12:32], [00:18:31], [00:21:04], [00:23:44].',
                'caption_en' => 'It is not about how hard you row, but which boat you are in; Timothy Ronald maps out the five future industries poised to generate massive wealth over the next 20 to 30 years [00:00:21], [00:01:07]. These crucial sectors include artificial intelligence & humanoid robotics, blockchain, clean energy, longevity biotechnology, and the space economy [00:06:22], [00:12:32], [00:18:31], [00:21:04], [00:23:44].',
            ],
            [
                'title_id' => 'Video Ini Gua Buat Untuk Gen Z',
                'title_en' => 'This Video Is Made for Gen Z',
                'youtube_id' => 'wLUvRv6kNGg',
                'category_id' => 'Pengembangan Diri / Sosial & Karier',
                'category_en' => 'Self-Development / Social & Career',
                'caption_id' => 'Ditujukan khusus untuk generasi Z yang menghadapi dunia dengan perubahan teknologi cepat namun penuh distraksi, video ini mendorong transisi mental dari sekadar konsumen menjadi produsen ekonomi [00:00:00], [00:02:21], [00:05:03]. Timothy Ronald membagikan karakter penting berupa kerja keras eksponensial dan rasa ingin tahu tinggi guna membangun kontrol penuh (*agency*) atas arah hidup Anda sendiri [00:11:40], [00:13:17], [00:15:19].',
                'caption_en' => 'Tailored specifically for Generation Z navigating a fast-paced yet highly distracted world, this video pushes for a mental transition from being mere consumers to active economic producers [00:00:00], [00:02:21], [00:05:03]. Timothy Ronald shares key traits of relentless hard work and intense curiosity to build true agency over your own life design [00:11:40], [00:13:17], [00:15:19].',
            ],
            [
                'title_id' => '11 Pelajaran Hidup Termahal di 2025',
                'title_en' => '11 Most Expensive Life Lessons in 2025',
                'youtube_id' => 'stNTfLJSf_w',
                'category_id' => 'Pengembangan Diri / Strategi Kehidupan',
                'category_en' => 'Self-Development / Life Strategy',
                'caption_id' => 'Merangkum catatan evaluasi pribadinya sepanjang tahun 2025, Timothy Ronald memaparkan 11 refleksi mendalam mengenai simplifikasi bisnis, penerimaan terhadap realita market, dan pentingnya fokus pada spesialisasi satu bidang dibanding generalisasi [00:00:08], [00:01:36], [00:17:01]. Melalui ketenangan berpikir, video ini mengajarkan bagaimana mempertajam kualitas keputusan (*judgement*) finansial jangka panjang [00:18:43], [00:24:33], [00:25:25].',
                'caption_en' => 'Condensing his personal evaluation logs from 2025, Timothy Ronald presents 11 profound reflections regarding business simplification, accepting market realities, and the power of specialization over generalization [00:00:08], [00:01:36], [00:17:01]. By cultivating absolute inner calmness, this video guides viewers on how to sharpen the quality of long-term financial judgement [00:18:43], [00:24:33], [00:25:25].',
            ],
            [
                'title_id' => 'Berani Ambil Aksi',
                'title_en' => 'Dare to Take Action',
                'youtube_id' => 'gvxK2JXSCHo',
                'category_id' => 'Pengembangan Diri / Motivasi Bisnis',
                'category_en' => 'Self-Development / Business Motivation',
                'caption_id' => 'Menolak mentalitas pecundang yang pasif, Timothy Ronald menekankan rumus sukses mutlak yang menggabungkan ketajaman analisa dengan keberanian eksekusi tanpa penundaan [00:02:00], [00:09:21], [00:13:42]. Video ini menantang anak muda untuk berhenti mencari alasan, memanfaatkan waktu selagi muda via akumulasi *time value of money*, serta merubah realitas hidup melalui aksi agresif yang nyata [00:08:00], [00:11:21], [00:14:21].',
                'caption_en' => 'Rejecting a passive and defensive mindset, Timothy Ronald emphasizes the ultimate success formula that combines sharp analysis with immediate, fearless execution [00:02:00], [00:09:21], [00:13:42]. This video challenges young audiences to stop making excuses, leverage the time value of money, and transform their current standard of living through aggressive, real-world action [00:08:00], [00:11:21], [00:14:21].',
            ],
        ];

        $columns = Schema::getColumnListing('gallery_items');

        $rows = collect($items)->map(function (array $item, int $index) use ($now, $columns): array {
            $row = [
                'title' => $item['title_id'],
                'title_id' => $item['title_id'],
                'title_en' => $item['title_en'],
                'type' => 'video',
                'category' => $item['category_id'],
                'category_id' => $item['category_id'],
                'category_en' => $item['category_en'],
                'caption' => $item['caption_id'],
                'caption_id' => $item['caption_id'],
                'caption_en' => $item['caption_en'],
                'media_url' => 'https://www.youtube.com/embed/' . $item['youtube_id'],
                'sort_order' => $index + 1,
                'is_published' => true,
                'published_at' => Carbon::parse('2026-07-05 00:00:00')->addMinutes($index),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            return array_intersect_key($row, array_flip($columns));
        })->all();

        DB::transaction(function () use ($rows): void {
            DB::table('gallery_items')->delete();
            DB::table('gallery_items')->insert($rows);
        });

        $this->command?->info('GalleryItemSeeder berhasil mengisi 6 video YouTube.');
    }
}
