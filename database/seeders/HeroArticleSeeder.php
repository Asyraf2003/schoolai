<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\HeroSlide;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

final class HeroArticleSeeder extends Seeder
{
    private const FEATURED_VIDEO_PATH = '/media/hero/202607290837.mp4';
    private const FEATURED_VIDEO_POSTER = '/images/hero-video-poster.svg';
    private const CREATIVE_PROJECT_SLUG = 'proyek-kreatif-yang-melatih-keberanian-anak';
    private const CREATIVE_PROJECT_IMAGE_PATH = '/media/seed/hero/creative-project.webp';

    public function run(): void
    {
        if (! Schema::hasColumn('hero_slides', 'article_id')) {
            return;
        }

        HeroSlide::query()
            ->whereNull('article_id')
            ->delete();

        $slugs = [
            'belajar-bermakna-dimulai-dari-rasa-ingin-tahu',
            'prestasi-tumbuh-dari-proses-yang-konsisten',
            'qiii-menjadi-kompas-kehidupan-sekolah',
            'sekolah-dan-keluarga-bertumbuh-sebagai-satu-tim',
            self::CREATIVE_PROJECT_SLUG,
        ];

        $articles = Article::query()
            ->whereIn('slug', $slugs)
            ->latestPublished()
            ->get()
            ->sortBy(
                fn (Article $article): int => array_search(
                    $article->slug,
                    $slugs,
                    true,
                ),
            );

        foreach ($articles->values() as $index => $article) {
            $isVideo = $index === 0;
            $isCreativeProject = $article->slug === self::CREATIVE_PROJECT_SLUG;

            $slide = HeroSlide::query()->firstOrNew([
                'article_id' => $article->getKey(),
            ]);

            $slide->fill([
                'type' => $isVideo ? 'video' : 'image',

                'media_url' => $isVideo
                    ? self::FEATURED_VIDEO_PATH
                    : ($isCreativeProject
                        ? self::CREATIVE_PROJECT_IMAGE_PATH
                        : $article->thumbnail_url),

                'poster_url' => $isVideo
                    ? self::FEATURED_VIDEO_POSTER
                    : $article->thumbnail_url,

                'media_alt_id' => $article->title_id,
                'media_alt_en' => $article->title_en,
                'media_alt_ar' => $article->title_ar,

                'eyebrow_id' => 'Artikel Pilihan',
                'eyebrow_en' => 'Featured Article',
                'eyebrow_ar' => 'مقال مختار',

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
