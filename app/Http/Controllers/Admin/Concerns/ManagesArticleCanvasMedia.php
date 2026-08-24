<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Article;
use App\Rules\SafeImageUpload;
use App\Support\Media\R2MediaStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

trait ManagesArticleCanvasMedia
{
    public function uploadImage(Request $request, Article $article): JsonResponse
    {
        $this->ensureNative($article);

        $data = $request->validate([
            'purpose' => ['nullable', 'in:content,thumbnail'],
            'image' => [
                'required',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                new SafeImageUpload,
                'max:'.self::MAX_IMAGE_KB,
            ],
        ], [
            'image.required' => 'Pilih gambar yang akan dimasukkan.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Gambar harus JPG, PNG, atau WebP.',
            'image.max' => 'Ukuran gambar maksimal 10MB.',
        ]);

        $purpose = $data['purpose'] ?? 'content';
        $owner = $purpose === 'thumbnail' ? 'articles/thumbnails' : 'articles/content';
        $stored = app(R2MediaStorage::class)->store($data['image'], $owner, $article->getKey());
        $url = $stored['url'];

        if ($purpose === 'thumbnail') {
            $oldThumbnailUrl = $article->thumbnail_url;

            try {
                $article->update(['thumbnail_url' => $url]);
            } catch (Throwable $exception) {
                app(R2MediaStorage::class)->deleteKey($stored['key']);

                throw $exception;
            }

            if (! Article::withTrashed()->where('thumbnail_url', $oldThumbnailUrl)->exists()) {
                app(R2MediaStorage::class)->deleteOwnedUrl($oldThumbnailUrl);
            }
        }

        return response()->json([
            'url' => $url,
            'name' => $data['image']->getClientOriginalName(),
            'purpose' => $purpose,
            'thumbnail_url' => $article->thumbnail_url,
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
                ->withHeaders(['Authorization' => 'Client-ID '.$accessKey])
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
}
