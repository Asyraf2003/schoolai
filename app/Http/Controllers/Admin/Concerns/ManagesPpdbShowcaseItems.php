<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Http\Controllers\Admin\PpdbSettingController;
use App\Models\PpdbShowcaseItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

trait ManagesPpdbShowcaseItems
{
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        [$data, $newPath] = $this->applyMedia($request, $data);
        $data['sort_order'] = $this->nextSortOrder($data['audience']);

        try {
            PpdbShowcaseItem::query()->create($data);
        } catch (Throwable $exception) {
            $this->deleteStoredPublicPath($newPath);

            throw $exception;
        }
        $this->normalizeSortOrders($data['audience']);

        return $this->redirectToShowcase()->with('success', 'Item konten PPDB berhasil ditambahkan.');
    }

    public function edit(PpdbShowcaseItem $ppdbShowcaseItem): View
    {
        return app(PpdbSettingController::class)->editShowcaseItem($ppdbShowcaseItem);
    }

    public function update(Request $request, PpdbShowcaseItem $ppdbShowcaseItem): RedirectResponse
    {
        $oldAudience = $ppdbShowcaseItem->audience;
        $oldMediaUrl = $ppdbShowcaseItem->media_url;
        $data = $this->validatedData($request, $ppdbShowcaseItem);
        [$data, $newPath, $replacesStoredFile] = $this->applyMedia($request, $data, $ppdbShowcaseItem);

        if ($oldAudience !== $data['audience']) {
            $data['sort_order'] = $this->nextSortOrder($data['audience']);
        }

        try {
            $ppdbShowcaseItem->update($data);
        } catch (Throwable $exception) {
            $this->deleteStoredPublicPath($newPath);

            throw $exception;
        }

        if ($replacesStoredFile) {
            $this->deleteStoredPublicFile($oldMediaUrl, $ppdbShowcaseItem->getKey());
        }
        $this->normalizeSortOrders($oldAudience);
        $this->normalizeSortOrders($data['audience']);

        return $this->redirectToShowcase()->with('success', 'Item konten PPDB berhasil diperbarui.');
    }

    public function destroy(PpdbShowcaseItem $ppdbShowcaseItem): RedirectResponse
    {
        $audience = $ppdbShowcaseItem->audience;
        $ppdbShowcaseItem->delete();
        $this->normalizeSortOrders($audience);

        return $this->redirectToShowcase()
            ->with('success', 'Item konten PPDB dipindahkan ke arsip dan dapat dipulihkan.');
    }

    public function restore(Request $request, int $ppdbShowcaseItem): RedirectResponse
    {
        $data = $request->validate([
            'replacement_ppdb_showcase_item_id' => ['nullable', 'integer'],
        ]);

        $replacementId = isset($data['replacement_ppdb_showcase_item_id'])
            ? (int) $data['replacement_ppdb_showcase_item_id']
            : null;

        if ($replacementId === null) {
            $audience = DB::transaction(function () use ($ppdbShowcaseItem): string {
                $archivedItem = PpdbShowcaseItem::onlyTrashed()
                    ->lockForUpdate()
                    ->findOrFail($ppdbShowcaseItem);

                $audience = $archivedItem->audience;
                $archivedItem->forceFill([
                    'sort_order' => $this->nextSortOrder($audience),
                ])->save();
                $archivedItem->restore();

                return $audience;
            });

            $this->normalizeSortOrders($audience);

            return $this->redirectToShowcase()->with('success', 'Item konten PPDB berhasil dipulihkan.');
        }

        $audience = DB::transaction(function () use ($ppdbShowcaseItem, $replacementId): string {
            $archivedItem = PpdbShowcaseItem::onlyTrashed()
                ->lockForUpdate()
                ->findOrFail($ppdbShowcaseItem);

            $replacementItem = PpdbShowcaseItem::query()
                ->lockForUpdate()
                ->findOrFail($replacementId);

            $archivedIdentity = $archivedItem->replacementIdentity();
            $replacementIdentity = $replacementItem->replacementIdentity();

            if ($archivedIdentity === null || $archivedIdentity !== $replacementIdentity) {
                throw ValidationException::withMessages([
                    'replacement_ppdb_showcase_item_id' => 'Item pengganti harus aktif serta memiliki target tab dan judul Indonesia yang identik.',
                ]);
            }

            $sortOrder = $replacementItem->sort_order;
            $audience = $archivedItem->audience;

            $replacementItem->delete();
            $archivedItem->forceFill(['sort_order' => $sortOrder])->save();
            $archivedItem->restore();

            return $audience;
        });

        $this->normalizeSortOrders($audience);

        return $this->redirectToShowcase()
            ->with('success', 'Item PPDB lama dipulihkan dan item aktif pengganti dipindahkan ke arsip.');
    }
}
