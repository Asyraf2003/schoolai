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
            ->map(function (Article $article, int $index) use ($locale): array {
                $publishedDate = $article->published_date;

                return [
                    'number' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                    'title' => $article->titleForLocale($locale),
                    'description' => $article->descriptionForLocale($locale),
                    'author' => $article->authorForDisplay(),
                    'date' => $publishedDate?->translatedFormat('j F Y') ?? '',
                    'published_at' => $publishedDate?->toDateString() ?? '',
                    'href' => $article->linkForLocale($locale),
                    'thumbnail_url' => $this->publicAssetUrl($article->thumbnail_url) ?? $article->thumbnail_url,
                ];
            })
            ->all();
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
