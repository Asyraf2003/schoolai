<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\PpdbSetting;
use App\Models\PpdbShowcaseItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;

trait PresentsPpdbShowcase
{
    public function moveShowcaseItemUp(PpdbShowcaseItem $ppdbShowcaseItem): RedirectResponse
    {
        $this->normalizeShowcaseSortOrders($ppdbShowcaseItem->audience);
        $ppdbShowcaseItem->refresh();

        $previousItem = PpdbShowcaseItem::query()
            ->forAudience($ppdbShowcaseItem->audience)
            ->where('sort_order', '<', $ppdbShowcaseItem->sort_order)
            ->orderByDesc('sort_order')
            ->orderByDesc('id')
            ->first();

        if ($previousItem) {
            $this->swapShowcaseSortOrder($ppdbShowcaseItem, $previousItem);
            $this->normalizeShowcaseSortOrders($ppdbShowcaseItem->audience);
        }

        return $this->redirectToShowcase()->with('success', 'Urutan konten PPDB berhasil diperbarui.');
    }

    public function moveShowcaseItemDown(PpdbShowcaseItem $ppdbShowcaseItem): RedirectResponse
    {
        $this->normalizeShowcaseSortOrders($ppdbShowcaseItem->audience);
        $ppdbShowcaseItem->refresh();

        $nextItem = PpdbShowcaseItem::query()
            ->forAudience($ppdbShowcaseItem->audience)
            ->where('sort_order', '>', $ppdbShowcaseItem->sort_order)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        if ($nextItem) {
            $this->swapShowcaseSortOrder($ppdbShowcaseItem, $nextItem);
            $this->normalizeShowcaseSortOrders($ppdbShowcaseItem->audience);
        }

        return $this->redirectToShowcase()->with('success', 'Urutan konten PPDB berhasil diperbarui.');
    }

    private function editView(PpdbShowcaseItem $showcaseItemForm, string $showcaseFormMode): View
    {
        $this->normalizeShowcaseSortOrdersIfNeeded();

        return view('admin.ppdb.edit', [
            'adminPageKey' => 'ppdb',
            'setting' => $this->currentSetting(),
            'showcaseItems' => $this->showcaseItems(),
            'showcaseItemForm' => $showcaseItemForm,
            'showcaseFormMode' => $showcaseFormMode,
            'audienceOptions' => $this->audienceOptions(),
            'mediaTypeOptions' => $this->mediaTypeOptions(),
            'showcaseLimits' => $this->showcaseLimits(),
        ]);
    }

    private function currentSetting(): PpdbSetting
    {
        return PpdbSetting::query()->firstOrCreate([], [
            'registration_url' => PpdbSetting::DEFAULT_REGISTRATION_URL,
            'information_url' => null,
            'is_active' => true,
        ]);
    }

    private function showcaseItems()
    {
        if (! Schema::hasTable('ppdb_showcase_items')) {
            return collect();
        }

        return PpdbShowcaseItem::query()->ordered()->get();
    }
}
