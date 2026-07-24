<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use App\Models\GalleryPageSection;
use App\Rules\SafeImageUpload;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

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
                GalleryItem::query()->lockForUpdate()->get();

                if (GalleryItem::query()->count() >= self::MAX_ITEMS) {
                    throw ValidationException::withMessages([
                        'replacement_gallery_item_id' => 'Galeri utama sudah memiliki 6 item aktif. Pilih Pulihkan & Gantikan pada media yang identik.',
                    ]);
                }

                $archivedItem = GalleryItem::onlyTrashed()
                    ->lockForUpdate()
                    ->findOrFail($galleryItem);

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

            $publishedOthers = GalleryItem::query()
                ->where('is_published', true)
                ->where($replacementItem->getKeyName(), '!=', $replacementItem->getKey())
                ->count();

            if ($replacementItem->is_published && ! $archivedItem->is_published && $publishedOthers < 1) {
                throw ValidationException::withMessages([
                    'replacement_gallery_item_id' => 'Penggantian ditolak karena akan menghilangkan satu-satunya item galeri yang terbit.',
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
        if ($galleryItem->is_published && GalleryItem::query()->where('is_published', true)->count() <= 1) {
            return back()->withErrors([
                'is_published' => 'Minimal harus ada 1 item galeri yang aktif.',
            ]);
        }

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
