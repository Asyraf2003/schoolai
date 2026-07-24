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

trait ValidatesGalleryPageMedia
{
    private function validatedSingleData(Request $request, ?GalleryPageMediaItem $mediaItem = null): array
    {
        $type = (string) $request->input('type', 'photo');
        $isPhoto = $type === 'photo';

        $needsPhotoFile = $isPhoto && (
            ! $mediaItem?->exists ||
            $mediaItem->type !== 'photo' ||
            ! $mediaItem->media_url
        );

        $validated = $request->validate([
            'type' => ['required', Rule::in(['photo', 'video'])],
            'media_file' => [
                Rule::requiredIf(fn (): bool => $needsPhotoFile),
                Rule::prohibitedIf(fn (): bool => $type === 'video'),
                'nullable',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                new SafeImageUpload,
                'max:' . self::MAX_PHOTO_KB,
            ],
            'media_url' => [
                Rule::requiredIf(fn (): bool => $type === 'video'),
                Rule::prohibitedIf(fn (): bool => $type === 'photo'),
                'nullable',
                'url',
                'max:2048',
            ],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ], [
            'media_file.required' => 'Upload foto wajib diisi untuk tipe Foto.',
            'media_file.prohibited' => 'Tipe Video tidak menerima upload file. Gunakan URL video.',
            'media_file.image' => 'File harus berupa gambar.',
            'media_file.mimes' => 'Foto harus JPG, PNG, atau WebP.',
            'media_file.max' => 'Ukuran foto maksimal 10MB.',
            'media_url.required' => 'URL video wajib diisi untuk tipe Video.',
            'media_url.prohibited' => 'Tipe Foto tidak menerima URL video. Gunakan upload foto.',
            'media_url.url' => 'URL video tidak valid.',
        ]);

        unset($validated['media_file']);

        $validated['title_id'] = null;
        $validated['title_en'] = null;
        $validated['description_id'] = null;
        $validated['description_en'] = null;
        $validated['is_published'] = $request->boolean('is_published');

        if (($validated['published_at'] ?? null) === '') {
            $validated['published_at'] = null;
        }

        return $validated;
    }

    private function applySingleMedia(Request $request, array $data, ?GalleryPageMediaItem $currentItem = null): array
    {
        if ($data['type'] === 'video') {
            $data['media_url'] = $this->normalizeVideoUrl((string) ($data['media_url'] ?? ''));

            return [$data, null, $currentItem?->type === 'photo'];
        }

        unset($data['media_url']);

        if ($request->hasFile('media_file')) {
            $path = $request->file('media_file')->store('gallery/page', 'public');

            if (! is_string($path) || $path === '') {
                throw ValidationException::withMessages([
                    'media_file' => 'Foto gagal disimpan. Silakan coba lagi.',
                ]);
            }

            $data['media_url'] = Storage::url($path);

            return [$data, $path, $currentItem?->type === 'photo'];
        }

        return [$data, null, false];
    }

    private function deleteStoredPublicPath(?string $path): void
    {
        if ($path !== null && $path !== '') {
            Storage::disk('public')->delete($path);
        }
    }
}
