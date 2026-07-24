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

trait MaintainsPpdbShowcaseOrdering
{
    private function nextShowcaseSortOrder(string $audience): int
    {
        return ((int) PpdbShowcaseItem::query()
            ->forAudience($audience)
            ->max('sort_order')) + 1;
    }

    private function normalizeShowcaseSortOrdersIfNeeded(): void
    {
        if (! Schema::hasTable('ppdb_showcase_items')) {
            return;
        }

        foreach (PpdbShowcaseItem::AUDIENCES as $audience) {
            $orders = PpdbShowcaseItem::query()
                ->forAudience($audience)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->pluck('sort_order')
                ->values()
                ->all();

            foreach ($orders as $index => $order) {
                if ((int) $order !== $index + 1) {
                    $this->normalizeShowcaseSortOrders($audience);
                    break;
                }
            }
        }
    }

    private function normalizeShowcaseSortOrders(string $audience): void
    {
        if (! Schema::hasTable('ppdb_showcase_items')) {
            return;
        }

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

    private function swapShowcaseSortOrder(PpdbShowcaseItem $firstItem, PpdbShowcaseItem $secondItem): void
    {
        $firstSortOrder = $firstItem->sort_order;

        $firstItem->forceFill(['sort_order' => $secondItem->sort_order])->save();
        $secondItem->forceFill(['sort_order' => $firstSortOrder])->save();
    }

    private function redirectToShowcase(): RedirectResponse
    {
        return redirect()->to(route('admin.ppdb') . '#ppdb-showcase-admin');
    }

    private function audienceOptions(): array
    {
        return [
            PpdbShowcaseItem::AUDIENCE_PARENTS => 'Untuk orang tua',
            PpdbShowcaseItem::AUDIENCE_SCHOOL => 'Untuk sekolah',
        ];
    }

    private function mediaTypeOptions(): array
    {
        return [
            PpdbShowcaseItem::MEDIA_PHOTO => 'Foto upload',
            PpdbShowcaseItem::MEDIA_VIDEO => 'URL video / embed',
        ];
    }

    private function showcaseLimits(): array
    {
        return [
            'max_photo_mb' => 10,
            'max_photo_kb' => PpdbShowcaseItem::MAX_PHOTO_KB,
        ];
    }

    private function isPublicUrl(string $url): bool
    {
        return PublicUrl::isSafe($url, ['/admin', '/login', '/auth']);
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
