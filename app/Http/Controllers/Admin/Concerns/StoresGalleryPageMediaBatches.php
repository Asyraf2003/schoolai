<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Http\Controllers\Controller;
use App\Models\GalleryPageMediaItem;
use App\Models\GalleryPageSection;
use App\Rules\SafeImageUpload;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

trait StoresGalleryPageMediaBatches
{
    private function storeMany(Request $request, GalleryPageSection $section): int
    {
        $type = (string) $request->input('type', 'photo');

        if ($type === 'photo') {
            $validated = $request->validate([
                'type' => ['required', Rule::in(['photo'])],
                'media_files' => ['required', 'array', 'min:1'],
                'media_files.*' => [
                    'required',
                    'file',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    new SafeImageUpload,
                    'max:' . self::MAX_PHOTO_KB,
                ],
                'media_urls' => ['nullable'],
                'is_published' => ['nullable', 'boolean'],
                'published_at' => ['nullable', 'date'],
            ], [
                'media_files.required' => 'Minimal pilih 1 foto.',
                'media_files.*.image' => 'Semua file harus berupa gambar.',
                'media_files.*.mimes' => 'Foto harus JPG, PNG, atau WebP.',
                'media_files.*.max' => 'Ukuran tiap foto maksimal 10MB.',
            ]);

            $storedPaths = [];

            try {
                return DB::transaction(function () use ($request, $section, $validated, &$storedPaths): int {
                    $created = 0;

                    foreach ($request->file('media_files', []) as $file) {
                        $path = $file->store('gallery/page', 'public');

                        if (! is_string($path) || $path === '') {
                            throw ValidationException::withMessages([
                                'media_files' => 'Salah satu foto gagal disimpan. Silakan coba lagi.',
                            ]);
                        }

                        $storedPaths[] = $path;
                        $section->mediaItems()->create([
                            'type' => 'photo',
                            'media_url' => Storage::url($path),
                            'is_published' => $request->boolean('is_published'),
                            'published_at' => $validated['published_at'] ?? null,
                            'title_id' => null,
                            'title_en' => null,
                            'description_id' => null,
                            'description_en' => null,
                        ]);

                        $created++;
                    }

                    return $created;
                });
            } catch (Throwable $exception) {
                foreach ($storedPaths as $path) {
                    $this->deleteStoredPublicPath($path);
                }

                throw $exception;
            }
        }

        $validated = $request->validate([
            'type' => ['required', Rule::in(['video'])],
            'media_urls' => ['required', 'string', 'max:20000'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ], [
            'media_urls.required' => 'Minimal tempel 1 URL video/embed.',
        ]);

        $urls = collect(preg_split('/\R+/', (string) $validated['media_urls']))
            ->map(fn (string $url): string => trim($url))
            ->filter()
            ->unique()
            ->values();

        if ($urls->isEmpty()) {
            throw ValidationException::withMessages([
                'media_urls' => 'Minimal tempel 1 URL video/embed.',
            ]);
        }

        return DB::transaction(function () use ($urls, $section, $request, $validated): int {
            $created = 0;

            foreach ($urls as $url) {
                $section->mediaItems()->create([
                    'type' => 'video',
                    'media_url' => $this->normalizeVideoUrl($url),
                    'is_published' => $request->boolean('is_published'),
                    'published_at' => $validated['published_at'] ?? null,
                    'title_id' => null,
                    'title_en' => null,
                    'description_id' => null,
                    'description_en' => null,
                ]);

                $created++;
            }

            return $created;
        });
    }
}
