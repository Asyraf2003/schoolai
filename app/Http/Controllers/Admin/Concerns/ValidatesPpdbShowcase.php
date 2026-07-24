<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Http\Controllers\Controller;
use App\Models\PpdbSetting;
use App\Models\PpdbShowcaseItem;
use App\Rules\SafeImageUpload;
use App\Support\PublicUrl;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Throwable;

trait ValidatesPpdbShowcase
{
    private function validatedShowcaseData(Request $request, ?PpdbShowcaseItem $currentItem = null): array
    {
        $mediaType = (string) $request->input('media_type', PpdbShowcaseItem::MEDIA_PHOTO);
        $needsPhotoFile = $mediaType === PpdbShowcaseItem::MEDIA_PHOTO && (
            ! $currentItem?->exists ||
            $currentItem->media_type !== PpdbShowcaseItem::MEDIA_PHOTO ||
            ! $currentItem->media_url
        );

        $validated = $request->validate([
            'audience' => ['required', Rule::in(PpdbShowcaseItem::AUDIENCES)],
            'title_id' => ['required', 'string', 'max:180'],
            'title_en' => ['nullable', 'string', 'max:180'],
            'description_id' => ['required', 'string', 'max:1200'],
            'description_en' => ['nullable', 'string', 'max:1200'],
            'media_type' => ['required', Rule::in(PpdbShowcaseItem::MEDIA_TYPES)],
            'media_file' => [
                Rule::requiredIf(fn (): bool => $needsPhotoFile),
                Rule::prohibitedIf(fn (): bool => $mediaType === PpdbShowcaseItem::MEDIA_VIDEO),
                'nullable',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                new SafeImageUpload,
                'max:' . PpdbShowcaseItem::MAX_PHOTO_KB,
            ],
            'media_url' => [
                Rule::requiredIf(fn (): bool => $mediaType === PpdbShowcaseItem::MEDIA_VIDEO),
                Rule::prohibitedIf(fn (): bool => $mediaType === PpdbShowcaseItem::MEDIA_PHOTO),
                'nullable',
                'url',
                'max:2048',
            ],
        ], [
            'audience.required' => 'Tujuan tampilan wajib dipilih.',
            'audience.in' => 'Tujuan tampilan PPDB tidak valid.',
            'title_id.required' => 'Judul Indonesia wajib diisi.',
            'description_id.required' => 'Deskripsi Indonesia wajib diisi.',
            'media_file.required' => 'Upload foto wajib diisi jika tipe media Foto.',
            'media_file.prohibited' => 'Tipe URL tidak menerima upload file. Gunakan kolom URL.',
            'media_file.image' => 'File harus berupa gambar.',
            'media_file.mimes' => 'Foto harus JPG, PNG, atau WebP.',
            'media_file.max' => 'Ukuran foto maksimal 10MB.',
            'media_url.required' => 'URL wajib diisi jika tipe media URL.',
            'media_url.prohibited' => 'Tipe Foto tidak menerima URL. Gunakan upload foto.',
            'media_url.url' => 'URL tidak valid.',
        ]);

        unset($validated['media_file']);

        $validated['title_id'] = trim((string) $validated['title_id']);
        $validated['title_en'] = $this->nullableText($validated['title_en'] ?? null);
        $validated['description_id'] = trim((string) $validated['description_id']);
        $validated['description_en'] = $this->nullableText($validated['description_en'] ?? null);

        return $validated;
    }

    private function applyShowcaseMedia(Request $request, array $data, ?PpdbShowcaseItem $currentItem = null): array
    {
        if ($data['media_type'] === PpdbShowcaseItem::MEDIA_VIDEO) {
            $data['media_url'] = $this->normalizeVideoUrl((string) ($data['media_url'] ?? ''));

            return [$data, null, $currentItem?->media_type === PpdbShowcaseItem::MEDIA_PHOTO];
        }

        unset($data['media_url']);

        if ($request->hasFile('media_file')) {
            $path = $request->file('media_file')->store('ppdb/showcase', 'public');

            if (! is_string($path) || $path === '') {
                throw ValidationException::withMessages([
                    'media_file' => 'Foto gagal disimpan. Silakan coba lagi.',
                ]);
            }

            $data['media_url'] = Storage::url($path);

            return [$data, $path, $currentItem?->media_type === PpdbShowcaseItem::MEDIA_PHOTO];
        }

        $data['media_url'] = $currentItem?->media_type === PpdbShowcaseItem::MEDIA_PHOTO
            ? $currentItem->media_url
            : null;

        return [$data, null, false];
    }

    private function deleteStoredPublicPath(?string $path): void
    {
        if ($path !== null && $path !== '') {
            Storage::disk('public')->delete($path);
        }
    }
}
