<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Http\Controllers\Controller;
use App\Models\GalleryPageMediaItem;
use App\Models\GalleryPageSection;
use App\Rules\SafeImageUpload;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

trait ManagesGalleryPageMedia
{
    public function create(GalleryPageSection $galleryPageSection): View
    {
        return view('admin.gallery.page-media.form', [
            'adminPageKey' => 'galeri',
            'section' => $galleryPageSection,
            'mode' => 'create',
            'item' => new GalleryPageMediaItem([
                'type' => 'photo',
                'is_published' => true,
                'published_at' => now(),
            ]),
            'typeOptions' => $this->typeOptions(),
        ]);
    }

    public function store(Request $request, GalleryPageSection $galleryPageSection): RedirectResponse
    {
        $createdCount = $this->storeMany($request, $galleryPageSection);

        return redirect()
            ->route('admin.galeri.sections.show', $galleryPageSection)
            ->with('success', "{$createdCount} media berhasil ditambahkan.");
    }

    public function show(GalleryPageMediaItem $galleryPageMediaItem): View
    {
        $section = $this->activeSectionOrFail($galleryPageMediaItem);

        return view('admin.gallery.page-media.show', [
            'adminPageKey' => 'galeri',
            'section' => $section,
            'item' => $galleryPageMediaItem,
        ]);
    }

    public function edit(GalleryPageMediaItem $galleryPageMediaItem): View
    {
        $section = $this->activeSectionOrFail($galleryPageMediaItem);

        return view('admin.gallery.page-media.edit', [
            'adminPageKey' => 'galeri',
            'section' => $section,
            'mode' => 'edit',
            'item' => $galleryPageMediaItem,
            'typeOptions' => $this->typeOptions(),
        ]);
    }

    public function update(Request $request, GalleryPageMediaItem $galleryPageMediaItem): RedirectResponse
    {
        $this->activeSectionOrFail($galleryPageMediaItem);

        $oldMediaUrl = $galleryPageMediaItem->media_url;
        $data = $this->validatedSingleData($request, $galleryPageMediaItem);
        [$data, $newPath, $replacesStoredFile] = $this->applySingleMedia($request, $data, $galleryPageMediaItem);

        try {
            $galleryPageMediaItem->update($data);
        } catch (Throwable $exception) {
            $this->deleteStoredPublicPath($newPath);

            throw $exception;
        }

        if ($replacesStoredFile) {
            $this->deleteStoredPublicFile($oldMediaUrl, $galleryPageMediaItem->getKey());
        }

        return redirect()
            ->route('admin.galeri.section-media.show', $galleryPageMediaItem)
            ->with('success', 'Media halaman galeri berhasil diperbarui.');
    }

    public function toggle(GalleryPageMediaItem $galleryPageMediaItem): RedirectResponse
    {
        $this->activeSectionOrFail($galleryPageMediaItem);

        $galleryPageMediaItem->update([
            'is_published' => ! $galleryPageMediaItem->is_published,
        ]);

        return back()->with('success', 'Status media berhasil diubah.');
    }

    public function destroy(GalleryPageMediaItem $galleryPageMediaItem): RedirectResponse
    {
        $section = $this->activeSectionOrFail($galleryPageMediaItem);

        $galleryPageMediaItem->delete();

        return redirect()
            ->route('admin.galeri.sections.show', $section)
            ->with('success', 'Media halaman galeri dipindahkan ke arsip dan dapat dipulihkan.');
    }

    public function restore(Request $request, int $galleryPageMediaItem): RedirectResponse
    {
        $data = $request->validate([
            'replacement_gallery_page_media_item_id' => ['nullable', 'integer'],
        ]);

        $replacementId = isset($data['replacement_gallery_page_media_item_id'])
            ? (int) $data['replacement_gallery_page_media_item_id']
            : null;

        $archivedItem = GalleryPageMediaItem::onlyTrashed()->findOrFail($galleryPageMediaItem);
        $section = $archivedItem->sectionWithTrashed;

        abort_if(! $section || $section->trashed(), 404);

        if ($replacementId === null) {
            $archivedItem->restore();

            return redirect()
                ->route('admin.galeri.sections.show', $section)
                ->with('success', 'Media halaman galeri berhasil dipulihkan.');
        }

        DB::transaction(function () use ($galleryPageMediaItem, $replacementId, $section): void {
            $archivedItem = GalleryPageMediaItem::onlyTrashed()
                ->lockForUpdate()
                ->findOrFail($galleryPageMediaItem);

            $replacementItem = GalleryPageMediaItem::query()
                ->lockForUpdate()
                ->findOrFail($replacementId);

            if (
                (int) $archivedItem->gallery_page_section_id !== (int) $section->getKey() ||
                (int) $replacementItem->gallery_page_section_id !== (int) $section->getKey()
            ) {
                throw ValidationException::withMessages([
                    'replacement_gallery_page_media_item_id' => 'Media pengganti harus berada pada bagian galeri yang sama.',
                ]);
            }

            $archivedIdentity = $archivedItem->replacementIdentity();
            $replacementIdentity = $replacementItem->replacementIdentity();

            if ($archivedIdentity === null || $archivedIdentity !== $replacementIdentity) {
                throw ValidationException::withMessages([
                    'replacement_gallery_page_media_item_id' => 'Media pengganti harus merupakan media aktif dengan tipe dan sumber yang identik.',
                ]);
            }

            $replacementItem->delete();
            $archivedItem->restore();
        });

        return redirect()
            ->route('admin.galeri.sections.show', $section)
            ->with('success', 'Media lama dipulihkan dan media aktif pengganti dipindahkan ke arsip.');
    }
}
