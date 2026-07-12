<?php

namespace App\Http\Controllers;

use App\Models\Article;
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
            ->filter(fn (Article $article): bool => $this->publicArticleUrl($article->linkForLocale($locale)) !== null)
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
                    'href' => $this->publicArticleUrl($article->linkForLocale($locale)),
                    'thumbnail_url' => $this->publicAssetUrl($article->thumbnail_url) ?? $article->thumbnail_url,
                ];
            })
            ->all();
    }

    private function publicArticleUrl(string $url): ?string
    {
        $url = trim($url);

        if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = '/' . ltrim((string) parse_url($url, PHP_URL_PATH), '/');

        if (! in_array($scheme, ['http', 'https'], true) || $host === '') {
            return null;
        }

        if (
            $host === 'localhost' ||
            $host === '127.0.0.1' ||
            $host === '::1' ||
            str_ends_with($host, '.local') ||
            str_starts_with($host, '10.') ||
            str_starts_with($host, '192.168.') ||
            preg_match('/^172\.(1[6-9]|2\d|3[0-1])\./', $host) === 1
        ) {
            return null;
        }

        if (
            $path === '/admin' ||
            str_starts_with($path, '/admin/') ||
            $path === '/login' ||
            str_starts_with($path, '/auth/')
        ) {
            return null;
        }

        return $url;
    }

    private function publicAssetUrl(mixed $path): ?string
    {
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        $path = trim($path);

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
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
