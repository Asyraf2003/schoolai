<?php

namespace App\View\Composers;

use App\Models\Article;
use App\Support\PublicUrl;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\View\View;

final class HomeArticlesComposer
{
    public function __construct(private Translator $translator) {}

    public function compose(View $view): void
    {
        $preview = $this->translator->get('home_article_preview');
        $preview = is_array($preview) ? $preview : [];
        $locale = app()->getLocale();
        $categoryFallback = (string) ($preview['category_fallback'] ?? 'Artikel');

        $items = Article::query()
            ->latestPublished()
            ->limit(3)
            ->get([
                'id',
                'article_source',
                'slug',
                'title_id',
                'title_en',
                'title_ar',
                'subtitle_id',
                'subtitle_en',
                'subtitle_ar',
                'description_id',
                'description_en',
                'description_ar',
                'tags',
                'word_count',
                'thumbnail_url',
                'link_id',
                'link_en',
                'link_ar',
                'published_at',
            ])
            ->map(fn (Article $article): array => [
                'category' => $this->category($article, $categoryFallback),
                'meta' => $this->meta($article),
                'title' => $article->titleForLocale($locale),
                'description' => $article->descriptionForLocale($locale)
                    ?: $article->subtitleForLocale($locale),
                'media_url' => $this->mediaUrl($article->thumbnail_url),
                'href' => $this->href($article, $locale),
            ])
            ->all();

        $view->with([
            'articleHeadingLines' => array_values($preview['heading_lines'] ?? []),
            'articleDescription' => (string) ($preview['description'] ?? ''),
            'articleItems' => $items,
            'articleReadLabel' => (string) ($preview['read_label'] ?? ''),
            'articleCtaLabel' => (string) ($preview['cta_label'] ?? ''),
        ]);
    }

    private function category(Article $article, string $fallback): string
    {
        foreach ($article->tags ?? [] as $tag) {
            if (is_string($tag) && trim($tag) !== '') {
                return trim($tag);
            }
        }

        return $fallback;
    }

    private function meta(Article $article): string
    {
        $parts = [];

        if ($article->isNative()) {
            $parts[] = (string) $this->translator->get('runtime.article.min_read', [
                'count' => max(1, (int) ceil(max(1, $article->word_count) / 220)),
            ]);
        }

        if ($article->published_at !== null) {
            $parts[] = $article->published_at->translatedFormat('j M Y');
        }

        return implode(' · ', array_filter($parts));
    }

    private function href(Article $article, string $locale): string
    {
        $href = $article->linkForLocale($locale);

        if ($article->isNative()) {
            return $href;
        }

        return PublicUrl::normalize($href, ['/admin', '/login', '/auth']) ?? route('artikel');
    }

    private function mediaUrl(?string $url): string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return asset(ltrim(Article::PLACEHOLDER_THUMBNAIL, '/'));
        }

        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return PublicUrl::normalize($url)
                ?? asset(ltrim(Article::PLACEHOLDER_THUMBNAIL, '/'));
        }

        return asset(ltrim($url, '/'));
    }
}
