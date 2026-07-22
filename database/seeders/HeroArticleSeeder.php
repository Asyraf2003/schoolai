<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\HeroSlide;
use App\Support\HeroVideoUrl;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

final class HeroArticleSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasColumn('hero_slides', 'article_id')) {
            return;
        }

        $articles = Article::query()
            ->whereIn('slug', [
                'belajar-bermakna-dimulai-dari-rasa-ingin-tahu',
                'prestasi-tumbuh-dari-proses-yang-konsisten',
                'qiii-menjadi-kompas-kehidupan-sekolah',
                'sekolah-dan-keluarga-bertumbuh-sebagai-satu-tim',
            ])
            ->latestPublished()
            ->get()
            ->sortBy(fn (Article $article): int => array_search($article->slug, [
                'belajar-bermakna-dimulai-dari-rasa-ingin-tahu',
                'prestasi-tumbuh-dari-proses-yang-konsisten',
                'qiii-menjadi-kompas-kehidupan-sekolah',
                'sekolah-dan-keluarga-bertumbuh-sebagai-satu-tim',
            ], true));

        foreach ($articles->values() as $index => $article) {
            $isVideo = $index === 1;
            $slide = HeroSlide::query()->firstOrNew(['article_id' => $article->getKey()]);

            $slide->fill([
                'type' => $isVideo ? 'video' : 'image',
                'media_url' => $isVideo
                    ? HeroVideoUrl::normalize('https://www.youtube.com/watch?v=kb1dXcf3QQs')
                    : $article->thumbnail_url,
                'poster_url' => $article->thumbnail_url,
                'media_alt_id' => $article->title_id,
                'media_alt_en' => $article->title_en,
                'media_alt_ar' => $article->title_ar,
                'eyebrow_id' => 'Artikel Pilihan',
                'eyebrow_en' => 'Featured Story',
                'eyebrow_ar' => 'مقال مميز',
                'title_id' => $article->title_id,
                'title_en' => $article->title_en,
                'title_ar' => $article->title_ar,
                'description_id' => $article->description_id,
                'description_en' => $article->description_en,
                'description_ar' => $article->description_ar,
                'cta_label_id' => 'Baca Artikel',
                'cta_label_en' => 'Read Article',
                'cta_label_ar' => 'اقرأ المقال',
                'cta_url' => $article->link_id,
                'cta_action' => 'link',
                'focal_position' => 'center center',
                'overlay_strength' => 0.52,
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);
            $slide->save();
        }
    }
}
