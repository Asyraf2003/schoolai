<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Support\PublicUrl;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

final class ArticlePageController extends Controller
{
    public function __invoke(Request $request): View
    {
        $page = __('pages.artikel');

        if (! is_array($page)) {
            $page = [];
        }

        $articles = $this->publishedArticles();
        $categories = $this->categories($articles);
        $requestedCategory = Str::limit(trim((string) $request->query('kategori')), 40, '');
        $activeCategory = collect($categories)->first(
            fn (string $category): bool => mb_strtolower($category) === mb_strtolower($requestedCategory)
        );

        if (is_string($activeCategory)) {
            $articles = $articles->filter(
                fn (Article $article): bool => collect($article->tags ?? [])->contains(
                    fn (mixed $tag): bool => is_string($tag)
                        && mb_strtolower(trim($tag)) === mb_strtolower($activeCategory)
                )
            )->values();
        }

        return view('pages.artikel', [
            'page' => $page,
            'articles' => $this->articleItems($articles),
            'categories' => $categories,
            'activeCategory' => $activeCategory,
        ]);
    }

    /** @return Collection<int, Article> */
    private function publishedArticles(): Collection
    {
        if (! Schema::hasTable('articles')) {
            return collect();
        }

        $locale = app()->getLocale();

        return Article::query()
            ->latestPublished()
            ->get()
            ->filter(fn (Article $article): bool => $article->isNative()
                || $this->publicArticleUrl($article->linkForLocale($locale)) !== null)
            ->values();
    }

    /**
     * @param  Collection<int, Article>  $articles
     * @return array<int, string>
     */
    private function categories(Collection $articles): array
    {
        return $articles
            ->flatMap(fn (Article $article): array => array_values($article->tags ?? []))
            ->filter(fn (mixed $tag): bool => is_string($tag) && trim($tag) !== '')
            ->map(fn (string $tag): string => trim($tag))
            ->unique(fn (string $tag): string => mb_strtolower($tag))
            ->sort(fn (string $left, string $right): int => strnatcasecmp($left, $right))
            ->values()
            ->all();
    }

    /**
     * @param  Collection<int, Article>  $articles
     * @return array<int, array<string, mixed>>
     */
    private function articleItems(Collection $articles): array
    {
        $locale = app()->getLocale();

        return $articles
            ->map(function (Article $article, int $index) use ($locale): array {
                $publishedAt = $article->published_at;

                return [
                    'number' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                    'title' => $article->titleForLocale($locale),
                    'description' => $article->descriptionForLocale($locale),
                    'author' => $article->authorForDisplay(),
                    'date' => $publishedAt
                        ? $publishedAt->translatedFormat('j F Y, H:i').' WIB'
                        : '',
                    'published_at' => $publishedAt?->toIso8601String() ?? '',
                    'reading_time' => $article->isNative()
                        ? __('runtime.article.min_read', [
                            'count' => max(1, (int) ceil(max(1, $article->word_count) / 220)),
                        ])
                        : null,
                    'categories' => array_values($article->tags ?? []),
                    'href' => $article->isNative()
                        ? $article->linkForLocale($locale)
                        : $this->publicArticleUrl($article->linkForLocale($locale)),
                    'external' => ! $article->isNative(),
                    'thumbnail_url' => $this->publicAssetUrl($article->thumbnail_url) ?? $article->thumbnail_url,
                ];
            })
            ->all();
    }

    private function publicArticleUrl(string $url): ?string
    {
        return PublicUrl::normalize($url, ['/admin', '/login', '/auth']);
    }

    private function publicAssetUrl(mixed $path): ?string
    {
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        $path = trim($path);

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return PublicUrl::normalize($path);
        }

        $relativePath = ltrim($path, '/');

        if (str_starts_with($relativePath, 'storage/')) {
            return asset($relativePath);
        }

        if (! file_exists(public_path($relativePath))) {
            return null;
        }

        return asset($relativePath);
    }
}
