<?php

namespace App\Providers\Concerns;

use App\Models\Article;
use Illuminate\Support\Facades\Schema;

trait BuildsArticleHeroSlides
{
    /** @return array<int, array<string, mixed>> */
    private function promotedArticleHeroSlides(string $locale): array
    {
        if (! Schema::hasTable('articles') || ! Schema::hasColumn('articles', 'hero_position')) {
            return [];
        }

        return Article::query()
            ->promotedInHero()
            ->limit(Article::HERO_SPOTLIGHT_LIMIT)
            ->get([
                'id',
                'article_source',
                'article_status',
                'slug',
                'title_id',
                'title_en',
                'title_ar',
                'description_id',
                'description_en',
                'description_ar',
                'thumbnail_url',
                'link_id',
                'link_en',
                'link_ar',
                'published_at',
                'hero_position',
            ])
            ->map(fn (Article $article): array => $this->articleToHeroArray($article, $locale))
            ->all();
    }

    /** @return array<string, mixed> */
    private function articleToHeroArray(Article $article, string $locale): array
    {
        $thumbnail = $this->publicAssetUrl($article->thumbnail_url)
            ?? (string) config('media.static.seo.home_og');

        return [
            'type' => 'image',
            'render_type' => 'image',
            'media_url' => $thumbnail,
            'poster_url' => $thumbnail,
            'media_alt' => $article->titleForLocale($locale),
            'eyebrow' => $this->articleEyebrow($locale),
            'title' => $article->titleForLocale($locale),
            'title_href' => $article->linkForLocale($locale),
            'description' => $article->descriptionForLocale($locale),
            'cta' => [
                'label' => $this->articleCtaLabel($locale),
                'href' => $article->linkForLocale($locale),
                'action' => 'link',
            ],
            'focal_position' => 'center center',
            'overlay_strength' => 0.52,
            'video_mime_type' => 'video/mp4',
            'is_media_fallback' => false,
            'article_id' => $article->getKey(),
        ];
    }

    private function articleEyebrow(string $locale): string
    {
        return match ($locale) {
            'ar' => 'مقال مميز',
            'en' => 'Featured story',
            default => 'Artikel Pilihan',
        };
    }

    private function articleCtaLabel(string $locale): string
    {
        return match ($locale) {
            'ar' => 'اقرأ المقال',
            'en' => 'Read article',
            default => 'Baca Artikel',
        };
    }
}
