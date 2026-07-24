<?php

namespace App\Providers\Concerns;

use App\Http\Controllers\Admin\HeroSlideAdminController;
use App\Models\Article;
use App\Models\HeroSlide;
use App\Models\PpdbSetting;
use App\Support\HeroVideoUrl;
use App\Support\PublicUrl;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;

trait BuildsArticleHeroSlides
{
    /** @return array<int, array<string, mixed>> */
    private function articleHeroSlides(string $locale): array
    {
        if (! Schema::hasTable('articles')) {
            return [];
        }

        if (
            Schema::hasTable('hero_slides')
            && Schema::hasColumn('hero_slides', 'article_id')
        ) {
            $placements = HeroSlide::query()
                ->with('article')
                ->activeOrdered()
                ->whereNotNull('article_id')
                ->get()
                ->filter(fn (HeroSlide $slide): bool => $slide->article?->isPubliclyVisibleNow() === true)
                ->map(fn (HeroSlide $slide): array => $slide->toHeroArray($locale))
                ->values()
                ->all();

            if ($placements !== []) {
                return $placements;
            }
        }

        return Article::query()
            ->latestPublished()
            ->limit(4)
            ->get()
            ->map(fn (Article $article): array => $this->articleToHeroArray($article, $locale))
            ->all();
    }

    /** @return array<string, mixed> */
    private function articleToHeroArray(Article $article, string $locale): array
    {
        $tag = collect($article->tags ?? [])
            ->first(fn (mixed $value): bool => is_string($value) && trim($value) !== '');

        return [
            'type' => 'image',
            'media' => $article->thumbnail_url ?: Article::PLACEHOLDER_THUMBNAIL,
            'poster' => $article->thumbnail_url ?: Article::PLACEHOLDER_THUMBNAIL,
            'media_alt' => $article->titleForLocale($locale),
            'eyebrow' => is_string($tag) && trim($tag) !== ''
                ? trim($tag)
                : $this->articleEyebrow($locale),
            'title' => $article->titleForLocale($locale),
            'description' => $article->descriptionForLocale($locale),
            'cta' => [
                'label' => $this->articleCtaLabel($locale),
                'href' => $article->linkForLocale($locale),
                'action' => 'link',
            ],
            'focal_position' => 'center center',
            'overlay_strength' => 0.52,
            'article_id' => $article->getKey(),
        ];
    }

    private function articleEyebrow(string $locale): string
    {
        return match ($locale) {
            'ar' => 'أحدث المقالات',
            'en' => 'Latest story',
            default => 'Artikel Terbaru',
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
