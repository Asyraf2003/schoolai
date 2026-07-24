<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Http\Controllers\Controller;
use App\Models\PpdbShowcaseItem;
use App\Rules\SafeImageUpload;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

trait MaintainsPpdbShowcaseItems
{
    private function deleteStoredPublicFile(?string $url, int|string|null $exceptItemId = null): void
    {
        if (! $url || ! str_starts_with($url, '/storage/')) {
            return;
        }

        $otherReference = PpdbShowcaseItem::withTrashed()
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

    private function nextSortOrder(string $audience): int
    {
        return ((int) PpdbShowcaseItem::query()
            ->forAudience($audience)
            ->max('sort_order')) + 1;
    }

    private function normalizeSortOrders(string $audience): void
    {
        PpdbShowcaseItem::query()
            ->forAudience($audience)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->values()
            ->each(function (PpdbShowcaseItem $item, int $index): void {
                $expectedOrder = $index + 1;

                if ($item->sort_order !== $expectedOrder) {
                    $item->forceFill(['sort_order' => $expectedOrder])->save();
                }
            });
    }

    private function swapSortOrder(PpdbShowcaseItem $firstItem, PpdbShowcaseItem $secondItem): void
    {
        $firstSortOrder = $firstItem->sort_order;

        $firstItem->forceFill(['sort_order' => $secondItem->sort_order])->save();
        $secondItem->forceFill(['sort_order' => $firstSortOrder])->save();
    }

    private function redirectToShowcase(): RedirectResponse
    {
        return redirect()->to(route('admin.ppdb') . '#ppdb-showcase-admin');
    }

    private function hostMatches(string $host, string $domain): bool
    {
        return $host === $domain || str_ends_with($host, '.' . $domain);
    }

    private function nullableText(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
