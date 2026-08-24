<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\GalleryPageMediaItem;
use App\Models\GalleryPageSection;
use App\Support\Media\R2MediaStorage;

trait MaintainsGalleryPageMedia
{
    private function deleteStoredPublicFile(?string $url, int|string|null $exceptItemId = null): void
    {
        if (! $url) {
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

        app(R2MediaStorage::class)->deleteOwnedUrl($url);
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
