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

trait PublishesArticleCanvas
{
    public function publish(Request $request, Article $article): JsonResponse
    {
        $this->ensureNative($article);

        $data = $request->validate([
            'tags' => ['nullable', 'array', 'max:5'],
            'tags.*' => ['string', 'max:40', 'distinct'],
            'author' => ['nullable', 'string', 'max:120'],
            'published_at' => ['nullable', 'date'],
            'publish_mode' => ['required', 'in:now,schedule'],
            'scheduled_at' => ['nullable', 'required_if:publish_mode,schedule', 'date'],
        ]);

        if (trim((string) $article->title_id) === '') {
            throw ValidationException::withMessages([
                'title_id' => 'Judul Indonesia wajib diisi sebelum artikel diterbitkan.',
            ]);
        }

        if ($this->sanitizer->plainText($article->content_id) === '' && ! str_contains((string) $article->content_id, '<img')) {
            throw ValidationException::withMessages([
                'content_id' => 'Isi artikel Indonesia belum ada.',
            ]);
        }

        $publishedAt = isset($data['published_at'])
            ? Carbon::parse((string) $data['published_at'], config('app.timezone'))
            : now();
        $status = Article::STATUS_PUBLISHED;

        if ($data['publish_mode'] === 'now' && $publishedAt->greaterThan(now()->addMinutes(5))) {
            throw ValidationException::withMessages([
                'published_at' => 'Untuk waktu terbit di masa depan, pilih opsi Jadwalkan.',
            ]);
        }

        if ($data['publish_mode'] === 'schedule') {
            $publishedAt = Carbon::parse((string) $data['scheduled_at'], config('app.timezone'));

            if ($publishedAt->lessThanOrEqualTo(now())) {
                throw ValidationException::withMessages([
                    'scheduled_at' => 'Jadwal publikasi harus berada di masa depan.',
                ]);
            }

            $status = Article::STATUS_SCHEDULED;
        }

        $slug = $this->uniqueSlug($article->title_id, $article);

        $article->update([
            'article_status' => $status,
            'slug' => $slug,
            'tags' => $this->normalizeTags($data['tags'] ?? []),
            'author' => $this->text($data['author'] ?? null) ?: Article::DEFAULT_AUTHOR,
            'published_at' => $publishedAt,
            'scheduled_at' => $status === Article::STATUS_SCHEDULED ? $publishedAt : null,
            'link_id' => route('artikel.native', ['article' => $slug]),
        ]);

        return response()->json([
            'message' => $status === Article::STATUS_SCHEDULED
                ? 'Artikel berhasil dijadwalkan.'
                : 'Artikel berhasil diterbitkan.',
            'status' => $status,
            'redirect' => $status === Article::STATUS_PUBLISHED
                ? route('artikel.native', ['article' => $slug])
                : route('admin.artikel'),
        ]);
    }

    private function ensureNative(Article $article): void
    {
        abort_unless($article->isNative(), 404);
    }

    private function uniqueSlug(string $title, Article $article): string
    {
        $base = Str::slug($title) ?: 'artikel';
        $slug = $base;
        $suffix = 2;

        while (Article::withTrashed()
            ->where('slug', $slug)
            ->where($article->getKeyName(), '!=', $article->getKey())
            ->exists()) {
            $slug = $base . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }

    private function text(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    /**
     * Keep category spelling consistent with categories that already exist.
     *
     * @param  array<int, mixed>  $tags
     * @return array<int, string>
     */
    private function normalizeTags(array $tags): array
    {
        $existing = Article::query()
            ->whereNotNull('tags')
            ->get(['tags'])
            ->flatMap(fn (Article $candidate): array => array_values($candidate->tags ?? []))
            ->filter(fn (mixed $value): bool => is_string($value) && trim($value) !== '')
            ->mapWithKeys(fn (string $value): array => [Str::lower(trim($value)) => trim($value)]);

        $normalized = [];

        foreach ($tags as $tag) {
            if (! is_string($tag)) {
                continue;
            }

            $tag = trim(preg_replace('/\s+/u', ' ', $tag) ?? '');
            $key = Str::lower($tag);

            if ($tag === '' || isset($normalized[$key])) {
                continue;
            }

            $normalized[$key] = $existing->get($key, $tag);

            if (count($normalized) === 5) {
                break;
            }
        }

        return array_values($normalized);
    }
}
