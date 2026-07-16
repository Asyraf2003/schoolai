<?php

namespace App\Http\Controllers\Admin;

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

final class ArticleCanvasAdminController extends Controller
{
    private const MAX_IMAGE_KB = 10240;

    public function __construct(private readonly ArticleContentSanitizer $sanitizer)
    {
        app()->setLocale('id');
    }

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

        return view('admin.articles.canvas', [
            'article' => $article,
            'adminPageKey' => 'artikel',
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
        ]);

        $contentId = $this->sanitizer->sanitize($data['content_id'] ?? '');
        $contentEn = $this->sanitizer->sanitize($data['content_en'] ?? '');
        $plainId = $this->sanitizer->plainText($contentId);
        $plainEn = $this->sanitizer->plainText($contentEn);
        $firstImage = $this->sanitizer->firstImageUrl($contentId)
            ?? $this->sanitizer->firstImageUrl($contentEn);

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
            'thumbnail_url' => $firstImage ?: $article->thumbnail_url ?: Article::PLACEHOLDER_THUMBNAIL,
        ]);

        return response()->json([
            'article_id' => $article->getKey(),
            'saved_at' => $article->updated_at?->toIso8601String(),
            'saved_label' => 'Draft · Tersimpan',
            'word_count' => $article->word_count,
            'character_count' => mb_strlen($plainId),
        ]);
    }

    public function uploadImage(Request $request, Article $article): JsonResponse
    {
        $this->ensureNative($article);

        $data = $request->validate([
            'image' => [
                'required',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                new SafeImageUpload,
                'max:' . self::MAX_IMAGE_KB,
            ],
        ], [
            'image.required' => 'Pilih gambar yang akan dimasukkan.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Gambar harus JPG, PNG, atau WebP.',
            'image.max' => 'Ukuran gambar maksimal 10MB.',
        ]);

        $path = $data['image']->store('articles/content/' . $article->getKey(), 'public');

        if (! is_string($path) || $path === '') {
            throw ValidationException::withMessages([
                'image' => 'Gambar gagal disimpan. Silakan coba lagi.',
            ]);
        }

        return response()->json([
            'url' => Storage::url($path),
            'name' => $data['image']->getClientOriginalName(),
        ], 201);
    }

    public function searchUnsplash(Request $request): JsonResponse
    {
        $data = $request->validate([
            'query' => ['required', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1', 'max:30'],
        ]);

        $accessKey = trim((string) config('services.unsplash.access_key'));

        if ($accessKey === '') {
            return response()->json([
                'message' => 'Pencarian Unsplash belum diaktifkan. Isi UNSPLASH_ACCESS_KEY di server.',
            ], 422);
        }

        try {
            $response = Http::acceptJson()
                ->withHeaders(['Authorization' => 'Client-ID ' . $accessKey])
                ->timeout(8)
                ->retry(1, 200)
                ->get('https://api.unsplash.com/search/photos', [
                    'query' => $data['query'],
                    'page' => $data['page'] ?? 1,
                    'per_page' => 12,
                    'orientation' => 'landscape',
                ])
                ->throw();
        } catch (Throwable) {
            return response()->json([
                'message' => 'Unsplash sedang tidak dapat dihubungi. Coba lagi beberapa saat.',
            ], 503);
        }

        $results = collect($response->json('results', []))
            ->map(fn (array $photo): array => [
                'id' => (string) ($photo['id'] ?? ''),
                'thumb' => (string) data_get($photo, 'urls.small', ''),
                'url' => (string) data_get($photo, 'urls.regular', ''),
                'alt' => (string) ($photo['alt_description'] ?? $photo['description'] ?? ''),
                'credit_name' => (string) data_get($photo, 'user.name', 'Unsplash'),
                'credit_url' => (string) data_get($photo, 'user.links.html', 'https://unsplash.com'),
            ])
            ->filter(fn (array $photo): bool => $photo['id'] !== '' && $photo['url'] !== '')
            ->values();

        return response()->json(['results' => $results]);
    }

    public function publish(Request $request, Article $article): JsonResponse
    {
        $this->ensureNative($article);

        $data = $request->validate([
            'tags' => ['nullable', 'array', 'max:5'],
            'tags.*' => ['string', 'max:40', 'distinct'],
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

        $publishedAt = now();
        $status = Article::STATUS_PUBLISHED;

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
            'tags' => array_values($data['tags'] ?? []),
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
}
