<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\HeroSlide;
use App\Rules\SafeImageUpload;
use App\Support\HeroVideoUrl;
use App\Support\PublicUrl;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

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
                Storage::disk('public')->delete($path);
            }
        }
    }

    private function deleteStoredFile(?string $url): void
    {
        if (! is_string($url) || ! str_starts_with($url, '/storage/')) {
            return;
        }

        Storage::disk('public')->delete(substr($url, strlen('/storage/')));
    }
}
