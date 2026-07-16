<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Support\PublicUrl;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;

final class ArticlePageController extends Controller
{
    public function __invoke(): View
    {
        $page = __('pages.artikel');

        if (! is_array($page)) {
            $page = [];
        }

        return view('pages.artikel', [
            'page' => $page,
            'articles' => $this->articleItems(),
        ]);
    }

    private function articleItems(): array
    {
        if (! Schema::hasTable('articles')) {
            return [];
        }

        $locale = app()->getLocale();

        return Article::query()
            ->latestPublished()
            ->get()
            ->filter(fn (Article $article): bool => $article->isNative()
                || $this->publicArticleUrl($article->linkForLocale($locale)) !== null)
            ->values()
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
