<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class NativeArticleController extends Controller
{
    public function show(Request $request, Article $article): View
    {
        $isPubliclyVisible = $article->isPubliclyVisibleNow();
        $canPreview = $request->user()?->isAdmin() === true;

        abort_unless($article->isNative() && ($isPubliclyVisible || $canPreview), 404);

        $locale = app()->getLocale();
        $content = $article->contentForLocale($locale);
        $wordCount = max(1, $article->word_count);
        $articleTags = collect($article->tags ?? [])
            ->filter(fn (mixed $tag): bool => is_string($tag) && trim($tag) !== '')
            ->map(fn (string $tag): string => mb_strtolower(trim($tag)))
            ->values();

        $relatedArticles = Article::query()
            ->where('article_source', Article::SOURCE_NATIVE)
            ->where($article->getKeyName(), '!=', $article->getKey())
            ->latestPublished()
            ->limit(24)
            ->get()
            ->sort(function (Article $left, Article $right) use ($articleTags): int {
                $leftScore = $this->sharedTagCount($left, $articleTags->all());
                $rightScore = $this->sharedTagCount($right, $articleTags->all());

                if ($leftScore !== $rightScore) {
                    return $rightScore <=> $leftScore;
                }

                return ($right->published_at?->getTimestamp() ?? 0)
                    <=> ($left->published_at?->getTimestamp() ?? 0);
            })
            ->take(3)
            ->map(fn (Article $related): array => [
                'title' => $related->titleForLocale($locale),
                'description' => $related->descriptionForLocale($locale),
                'href' => $related->linkForLocale($locale),
                'thumbnail_url' => $related->thumbnail_url ?: Article::PLACEHOLDER_THUMBNAIL,
                'categories' => array_values($related->tags ?? []),
                'reading_minutes' => max(1, (int) ceil(max(1, $related->word_count) / 220)),
            ])
            ->values()
            ->all();

        return view('pages.artikel-native', [
            'article' => $article,
            'articleTitle' => $article->titleForLocale($locale),
            'articleSubtitle' => $article->subtitleForLocale($locale),
            'articleDescription' => $article->descriptionForLocale($locale),
            'articleContent' => $content,
            'readingMinutes' => max(1, (int) ceil($wordCount / 220)),
            'relatedArticles' => $relatedArticles,
            'isAdminPreview' => ! $isPubliclyVisible && $canPreview,
        ]);
    }

    /** @param array<int, string> $normalizedTags */
    private function sharedTagCount(Article $article, array $normalizedTags): int
    {
        if ($normalizedTags === []) {
            return 0;
        }

        $candidateTags = collect($article->tags ?? [])
            ->filter(fn (mixed $tag): bool => is_string($tag))
            ->map(fn (string $tag): string => mb_strtolower(trim($tag)))
            ->all();

        return count(array_intersect($normalizedTags, $candidateTags));
    }
}
