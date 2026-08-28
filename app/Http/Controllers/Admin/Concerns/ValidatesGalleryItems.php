<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\GalleryItem;
use App\Rules\SafeImageUpload;
use App\Support\Media\R2MediaStorage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

trait ValidatesGalleryItems
{
    private function validatedData(Request $request, ?GalleryItem $galleryItem = null): array
    {
        $type = (string) $request->input('type', 'photo');
        $isPhoto = $type === 'photo';

        $needsPhotoFile = $isPhoto && (
            ! $galleryItem?->exists ||
            $galleryItem->type !== 'photo' ||
            ! $galleryItem->media_url
        );

        $validated = $request->validate([
            'title_id' => ['required', 'string', 'max:160'],
            'title_en' => ['nullable', 'string', 'max:160'],
            'title_ar' => ['nullable', 'string', 'max:160'],
            'type' => ['required', Rule::in(['photo', 'video'])],
            'category_id' => ['required', 'string', 'max:80'],
            'category_en' => ['nullable', 'string', 'max:80'],
            'category_ar' => ['nullable', 'string', 'max:80'],
            'caption_id' => ['nullable', 'string', 'max:1000'],
            'caption_en' => ['nullable', 'string', 'max:1000'],
            'caption_ar' => ['nullable', 'string', 'max:1000'],
            'media_file' => [
                Rule::requiredIf(fn (): bool => $needsPhotoFile),
                Rule::prohibitedIf(fn (): bool => $type === 'video'),
                'nullable',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                new SafeImageUpload,
                'max:'.self::MAX_PHOTO_KB,
            ],
            'media_url' => [
                Rule::requiredIf(fn (): bool => $type === 'video'),
                Rule::prohibitedIf(fn (): bool => $type === 'photo'),
                'nullable',
                'url',
                'max:2048',
            ],
            'is_published' => ['nullable', 'boolean'],
            'show_on_homepage' => ['nullable', 'boolean'],
            'show_on_gallery_page' => ['nullable', 'boolean'],
            'section_ids' => ['nullable', 'array'],
            'section_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('gallery_page_sections', 'id')->whereNull('deleted_at'),
            ],
            'published_at' => ['nullable', 'date'],
        ], [
            'title_id.required' => 'Judul Indonesia wajib diisi.',
            'category_id.required' => 'Kategori Indonesia wajib diisi.',
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

        foreach (['title_en', 'title_ar', 'category_en', 'category_ar', 'caption_id', 'caption_en', 'caption_ar'] as $field) {
            $value = $validated[$field] ?? null;
            $validated[$field] = is_string($value) && trim($value) !== '' ? trim($value) : null;
        }

        $validated['title'] = $validated['title_id'];
        $validated['category'] = $validated['category_id'];
        $validated['caption'] = $validated['caption_id'];
        $validated['is_published'] = $request->boolean('is_published');
        $validated['show_on_homepage'] = $request->boolean('show_on_homepage');
        $validated['show_on_gallery_page'] = $request->boolean('show_on_gallery_page');
        $validated['section_ids'] = array_values($validated['section_ids'] ?? []);

        if (($validated['published_at'] ?? null) === '') {
            $validated['published_at'] = null;
        }

        $homepageCount = GalleryItem::query()
            ->homepage()
            ->when(
                $galleryItem?->exists,
                fn ($query) => $query->whereKeyNot($galleryItem->getKey()),
            )
            ->count();

        if ($validated['show_on_homepage'] && $homepageCount >= self::MAX_ITEMS) {
            throw ValidationException::withMessages([
                'show_on_homepage' => 'Maksimal 9 media dapat ditempatkan di homepage.',
            ]);
        }

        return $validated;
    }

    private function applyMedia(Request $request, array $data, ?GalleryItem $currentItem = null): array
    {
        if ($data['type'] === 'video') {
            $data['media_url'] = $this->normalizeVideoUrl((string) ($data['media_url'] ?? ''));

            return [$data, null, $currentItem?->type === 'photo'];
        }

        unset($data['media_url']);

        if ($request->hasFile('media_file')) {
            $stored = app(R2MediaStorage::class)->store(
                $request->file('media_file'),
                'gallery/media',
                $currentItem?->getKey(),
            );
            $data['media_url'] = $stored['url'];

            return [$data, $stored['key'], $currentItem?->type === 'photo'];
        }

        return [$data, null, false];
    }

    private function deleteStoredPublicPath(?string $path): void
    {
        if ($path !== null && $path !== '') {
            app(R2MediaStorage::class)->deleteKey($path);
        }
    }
}
