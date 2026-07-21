<?php

namespace App\Http\Middleware;

use App\Models\Article;
use App\Support\ArticleContentSanitizer;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

final class PersistArabicArticleCanvas
{
    public function __construct(private readonly ArticleContentSanitizer $sanitizer)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->routeIs('admin.artikel.canvas.autosave')) {
            return $next($request);
        }

        $input = $request->all();
        $hasArabicPayload = array_key_exists('title_ar', $input)
            || array_key_exists('subtitle_ar', $input)
            || array_key_exists('content_ar', $input);

        if (! $hasArabicPayload) {
            return $next($request);
        }

        $data = $request->validate([
            'title_ar' => ['nullable', 'string', 'max:200'],
            'subtitle_ar' => ['nullable', 'string', 'max:300'],
            'content_ar' => ['nullable', 'string', 'max:2000000'],
        ]);

        $response = $next($request);

        if (! $response->isSuccessful()) {
            return $response;
        }

        $article = $request->route('article');

        if (! $article instanceof Article) {
            $article = Article::query()->findOrFail($article);
        }

        abort_unless($article->isNative(), 404);

        $updates = [];

        if (array_key_exists('title_ar', $data)) {
            $updates['title_ar'] = $this->text($data['title_ar'] ?? null);
        }

        if (array_key_exists('subtitle_ar', $data)) {
            $updates['subtitle_ar'] = $this->text($data['subtitle_ar'] ?? null);
        }

        if (array_key_exists('content_ar', $data)) {
            $contentAr = $this->sanitizer->sanitize($data['content_ar'] ?? '');
            $plainAr = $this->sanitizer->plainText($contentAr);

            $updates['content_ar'] = $contentAr !== '' ? $contentAr : null;
            $updates['description_ar'] = $plainAr !== '' ? Str::limit($plainAr, 300) : null;

            $currentThumbnail = trim((string) $article->thumbnail_url);
            $thumbnailIsPlaceholder = $currentThumbnail === ''
                || $currentThumbnail === Article::PLACEHOLDER_THUMBNAIL;

            if ($thumbnailIsPlaceholder) {
                $firstArabicImage = $this->sanitizer->firstImageUrl($contentAr);

                if ($firstArabicImage !== null) {
                    $updates['thumbnail_url'] = $firstArabicImage;
                }
            }
        }

        if ($updates !== []) {
            $article->update($updates);
        }

        $article->refresh();

        $contentId = (string) ($article->content_id ?? '');
        $contentEn = (string) ($article->content_en ?? '');
        $contentAr = (string) ($article->content_ar ?? '');

        $article->update([
            'word_count' => max(
                $this->sanitizer->wordCount($contentId),
                $this->sanitizer->wordCount($contentEn),
                $this->sanitizer->wordCount($contentAr),
            ),
        ]);

        $plainLengths = [
            mb_strlen($this->sanitizer->plainText($contentId)),
            mb_strlen($this->sanitizer->plainText($contentEn)),
            mb_strlen($this->sanitizer->plainText($contentAr)),
        ];

        if ($response instanceof JsonResponse) {
            $payload = $response->getData(true);
            $payload['word_count'] = $article->word_count;
            $payload['character_count'] = max($plainLengths);
            $payload['reading_minutes'] = max(1, (int) ceil(max(1, $article->word_count) / 220));
            $payload['thumbnail_url'] = $article->thumbnail_url;
            $response->setData($payload);
        }

        return $response;
    }

    private function text(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
