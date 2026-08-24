<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\TestimonialMedia;
use App\Support\Media\R2MediaStorage;
use App\Support\TestimonialVideoUrl;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

trait StoresTestimonialMedia
{
    private function applyMedia(Request $request, array $data, ?TestimonialMedia $current = null): array
    {
        if ($data['source'] === 'embed') {
            $url = TestimonialVideoUrl::normalize((string) ($data['media_url'] ?? ''));

            if ($url === null) {
                throw ValidationException::withMessages([
                    'media_url' => 'URL video belum didukung. Gunakan YouTube, Vimeo, TikTok, Instagram, atau Facebook Reel/Watch.',
                ]);
            }

            $data['media_url'] = $url;

            return [$data, null, $current?->source === 'upload'];
        }

        unset($data['media_url']);

        if ($request->hasFile('media_file')) {
            $owner = $data['type'] === 'photo' ? 'testimonials/photos' : 'testimonials/videos';
            $stored = app(R2MediaStorage::class)->store(
                $request->file('media_file'),
                $owner,
                $current?->getKey(),
            );
            $data['media_url'] = $stored['url'];

            return [$data, $stored['key'], $current?->source === 'upload'];
        } elseif ($current?->exists && $current->source === 'upload') {
            $data['media_url'] = $current->media_url;
        }

        return [$data, null, false];
    }

    private function deleteStoredKey(?string $key): void
    {
        app(R2MediaStorage::class)->deleteKey($key);
    }

    private function deleteStoredFile(?string $url, int|string|null $exceptItemId = null): void
    {
        if (! $url) {
            return;
        }

        $otherReference = TestimonialMedia::withTrashed()
            ->where('media_url', $url)
            ->when(
                $exceptItemId !== null,
                fn ($query) => $query->where('id', '!=', $exceptItemId)
            )
            ->exists();

        if (! $otherReference) {
            app(R2MediaStorage::class)->deleteOwnedUrl($url);
        }
    }

    private function nextSortOrder(): int
    {
        return min(((int) TestimonialMedia::query()->max('sort_order')) + 1, TestimonialMedia::MAX_ITEMS);
    }

    private function normalizeSortOrders(): void
    {
        TestimonialMedia::query()->ordered()->get()->values()
            ->each(function (TestimonialMedia $item, int $index): void {
                $expected = $index + 1;
                if ($item->sort_order !== $expected) {
                    $item->forceFill(['sort_order' => $expected])->save();
                }
            });
    }

    private function swapSortOrder(TestimonialMedia $first, TestimonialMedia $second): void
    {
        $firstOrder = $first->sort_order;
        $first->forceFill(['sort_order' => $second->sort_order])->save();
        $second->forceFill(['sort_order' => $firstOrder])->save();
    }
}
