<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Rules\SafeImageUpload;
use App\Support\ArticleContentSanitizer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

trait ManagesArticleCanvasDrafts
{
    public function start(Request $request): RedirectResponse
    {
        $draftKey = 'draft-' . Str::lower((string) Str::ulid());

        $article = Article::query()->create([
            'article_source' => Article::SOURCE_NATIVE,
            'article_status' => Article::STATUS_DRAFT,
            'slug' => $draftKey,
            'title_id' => '',
            'thumbnail_url' => Article::PLACEHOLDER_THUMBNAIL,
            'link_id' => route('artikel.native', ['article' => $draftKey]),
            'author' => trim((string) $request->user()?->name) ?: Article::DEFAULT_AUTHOR,
            'published_at' => now(),
        ]);

        return redirect()->route('admin.artikel.canvas.edit', $article);
    }

    public function edit(Article $article): View
    {
        $this->ensureNative($article);

        $categorySuggestions = Article::query()
            ->whereNotNull('tags')
            ->get(['tags'])
            ->flatMap(fn (Article $candidate): array => array_values($candidate->tags ?? []))
            ->filter(fn (mixed $tag): bool => is_string($tag) && trim($tag) !== '')
            ->map(fn (string $tag): string => trim($tag))
            ->unique(fn (string $tag): string => Str::lower($tag))
            ->sort(fn (string $left, string $right): int => strnatcasecmp($left, $right))
            ->values()
            ->all();

        return view('admin.articles.canvas', [
            'article' => $article,
            'adminPageKey' => 'artikel',
            'categorySuggestions' => $categorySuggestions,
        ]);
    }

    public function autosave(Request $request, Article $article): JsonResponse
    {
        $this->ensureNative($article);

        $data = $request->validate([
            'title_id' => ['nullable', 'string', 'max:200'],
            'title_en' => ['nullable', 'string', 'max:200'],
            'subtitle_id' => ['nullable', 'string', 'max:300'],
            'subtitle_en' => ['nullable', 'string', 'max:300'],
            'content_id' => ['nullable', 'string', 'max:2000000'],
            'content_en' => ['nullable', 'string', 'max:2000000'],
            'thumbnail_url' => ['nullable', 'string', 'max:2048'],
        ]);

        $contentId = $this->sanitizer->sanitize($data['content_id'] ?? '');
        $contentEn = $this->sanitizer->sanitize($data['content_en'] ?? '');
        $plainId = $this->sanitizer->plainText($contentId);
        $plainEn = $this->sanitizer->plainText($contentEn);
        $firstImage = $this->sanitizer->firstImageUrl($contentId)
            ?? $this->sanitizer->firstImageUrl($contentEn);
        $requestedThumbnail = $this->sanitizer->imageUrl($data['thumbnail_url'] ?? null);
        $currentThumbnail = trim((string) $article->thumbnail_url);
        $thumbnailIsPlaceholder = $currentThumbnail === ''
            || $currentThumbnail === Article::PLACEHOLDER_THUMBNAIL;
        $thumbnail = $requestedThumbnail
            ?? ($thumbnailIsPlaceholder ? $firstImage : null)
            ?? ($currentThumbnail !== '' ? $currentThumbnail : Article::PLACEHOLDER_THUMBNAIL);

        $article->update([
            'title_id' => $this->text($data['title_id'] ?? null) ?? '',
            'title_en' => $this->text($data['title_en'] ?? null),
            'subtitle_id' => $this->text($data['subtitle_id'] ?? null),
            'subtitle_en' => $this->text($data['subtitle_en'] ?? null),
            'description_id' => $plainId !== '' ? Str::limit($plainId, 300) : null,
            'description_en' => $plainEn !== '' ? Str::limit($plainEn, 300) : null,
            'content_id' => $contentId !== '' ? $contentId : null,
            'content_en' => $contentEn !== '' ? $contentEn : null,
            'word_count' => max(
                $this->sanitizer->wordCount($contentId),
                $this->sanitizer->wordCount($contentEn)
            ),
            'thumbnail_url' => $thumbnail,
        ]);

        return response()->json([
            'article_id' => $article->getKey(),
            'saved_at' => $article->updated_at?->toIso8601String(),
            'saved_label' => 'Draft · Tersimpan',
            'word_count' => $article->word_count,
            'character_count' => mb_strlen($plainId),
            'reading_minutes' => max(1, (int) ceil(max(1, $article->word_count) / 220)),
            'thumbnail_url' => $article->thumbnail_url,
        ]);
    }
}
