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

trait MaintainsGalleryPageMedia
{
    private function deleteStoredPublicFile(?string $url, int|string|null $exceptItemId = null): void
    {
        if (! $url || ! str_starts_with($url, '/storage/')) {
            return;
        }

        $otherReference = GalleryPageMediaItem::withTrashed()
            ->where('media_url', $url)
            ->when(
                $exceptItemId !== null,
                fn ($query) => $query->where('id', '!=', $exceptItemId)
            )
            ->exists();

        if ($otherReference) {
            return;
        }

        $path = substr($url, strlen('/storage/'));

        if ($path === '' || str_contains($path, '..') || str_starts_with($path, '/') || str_contains($path, '\\')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    private function activeSectionOrFail(GalleryPageMediaItem $item): GalleryPageSection
    {
        $section = $item->section;

        abort_if(! $section, 404);

        return $section;
    }

    private function typeOptions(): array
    {
        return [
            'photo' => 'Foto',
            'video' => 'Video',
        ];
    }
}
