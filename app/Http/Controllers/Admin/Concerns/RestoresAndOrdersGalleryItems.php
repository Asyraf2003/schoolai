<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\GalleryItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

trait RestoresAndOrdersGalleryItems
{
    public function restore(Request $request, int $galleryItem): RedirectResponse
    {
        $data = $request->validate([
            'replacement_gallery_item_id' => ['nullable', 'integer'],
        ]);

        $replacementGalleryItemId = isset($data['replacement_gallery_item_id'])
            ? (int) $data['replacement_gallery_item_id']
            : null;

        if ($replacementGalleryItemId === null) {
            DB::transaction(function () use ($galleryItem): void {
                $archivedItem = GalleryItem::onlyTrashed()
                    ->lockForUpdate()
                    ->findOrFail($galleryItem);

                if ($archivedItem->show_on_homepage && GalleryItem::query()->homepage()->count() >= self::MAX_ITEMS) {
                    $archivedItem->show_on_homepage = false;
                }

                $archivedItem->forceFill(['sort_order' => $this->nextSortOrder()])->save();
                $archivedItem->restore();
            });

            $this->normalizeSortOrders();

            return redirect()
                ->route('admin.galeri')
                ->with('success', 'Item galeri berhasil dipulihkan.');
        }

        DB::transaction(function () use ($galleryItem, $replacementGalleryItemId): void {
            $archivedItem = GalleryItem::onlyTrashed()
                ->lockForUpdate()
                ->findOrFail($galleryItem);

            $replacementItem = GalleryItem::query()
                ->lockForUpdate()
                ->findOrFail($replacementGalleryItemId);

            $archivedIdentity = $archivedItem->replacementIdentity();
            $replacementIdentity = $replacementItem->replacementIdentity();

            if ($archivedIdentity === null || $archivedIdentity !== $replacementIdentity) {
                throw ValidationException::withMessages([
                    'replacement_gallery_item_id' => 'Item pengganti harus merupakan item galeri aktif dengan tipe dan media yang identik.',
                ]);
            }

            $replacementSortOrder = $replacementItem->sort_order;

            $replacementItem->delete();
            $archivedItem->forceFill(['sort_order' => $replacementSortOrder])->save();
            $archivedItem->restore();
        });

        $this->normalizeSortOrders();

        return redirect()
            ->route('admin.galeri')
            ->with('success', 'Item galeri lama dipulihkan dan item aktif pengganti dipindahkan ke arsip.');
    }

    public function toggle(GalleryItem $galleryItem): RedirectResponse
    {
        $galleryItem->update([
            'is_published' => ! $galleryItem->is_published,
        ]);

        return back()->with('success', 'Status item galeri berhasil diubah.');
    }

    public function moveUp(GalleryItem $galleryItem): RedirectResponse
    {
        $this->normalizeSortOrders();

        $galleryItem->refresh();

        $previousItem = GalleryItem::query()
            ->where('sort_order', '<', $galleryItem->sort_order)
            ->orderByDesc('sort_order')
            ->orderByDesc('id')
            ->first();

        if ($previousItem) {
            $this->swapSortOrder($galleryItem, $previousItem);
            $this->normalizeSortOrders();
        }

        return redirect()
            ->route('admin.galeri')
            ->with('success', 'Urutan galeri berhasil diperbarui.');
    }

    public function moveDown(GalleryItem $galleryItem): RedirectResponse
    {
        $this->normalizeSortOrders();

        $galleryItem->refresh();

        $nextItem = GalleryItem::query()
            ->where('sort_order', '>', $galleryItem->sort_order)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        if ($nextItem) {
            $this->swapSortOrder($galleryItem, $nextItem);
            $this->normalizeSortOrders();
        }

        return redirect()
            ->route('admin.galeri')
            ->with('success', 'Urutan galeri berhasil diperbarui.');
    }
}
