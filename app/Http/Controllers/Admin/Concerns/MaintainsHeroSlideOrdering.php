<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\HeroSlide;
use App\Support\Media\R2MediaStorage;
use App\Support\PublicUrl;
use Illuminate\Support\Facades\DB;

trait MaintainsHeroSlideOrdering
{
    private function normalizeHeroLink(mixed $url): ?string
    {
        if (! is_string($url) || trim($url) === '') {
            return null;
        }

        $url = trim($url);

        if (preg_match('/^#[A-Za-z][A-Za-z0-9_-]*$/', $url) === 1) {
            return $url;
        }

        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return $url;
        }

        return PublicUrl::normalize($url, ['/admin', '/login', '/auth']);
    }

    private function nextSortOrder(): int
    {
        return (int) HeroSlide::query()->max('sort_order') + 1;
    }

    private function normalizeSortOrders(): void
    {
        HeroSlide::query()->ordered()->get()->values()->each(
            fn (HeroSlide $slide, int $index) => $slide->forceFill(['sort_order' => $index + 1])->saveQuietly()
        );
    }

    private function move(HeroSlide $heroSlide, bool $up): void
    {
        $this->normalizeSortOrders();
        $heroSlide->refresh();

        $query = HeroSlide::query()->where(
            'sort_order',
            $up ? '<' : '>',
            $heroSlide->sort_order,
        );

        $other = $up
            ? $query->orderByDesc('sort_order')->orderByDesc('id')->first()
            : $query->orderBy('sort_order')->orderBy('id')->first();

        if (! $other) {
            return;
        }

        DB::transaction(function () use ($heroSlide, $other): void {
            $current = $heroSlide->sort_order;
            $heroSlide->forceFill(['sort_order' => $other->sort_order])->save();
            $other->forceFill(['sort_order' => $current])->save();
        });

        $this->normalizeSortOrders();
    }

    /** @param array{media: ?string, poster: ?string} $paths */
    private function deleteStoredPaths(array $paths): void
    {
        foreach ($paths as $path) {
            if (is_string($path) && $path !== '') {
                app(R2MediaStorage::class)->deleteKey($path);
            }
        }
    }

    private function deleteStoredFile(?string $url): void
    {
        app(R2MediaStorage::class)->deleteOwnedUrl($url);
    }
}
