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

trait ManagesGalleryItems
{
    public function __invoke(): View
    {
        return $this->index();
    }

    public function index(): View
    {
        $this->normalizeSortOrdersIfNeeded();

        $activeItems = GalleryItem::query()->ordered()->get();
        $archivedItems = GalleryItem::onlyTrashed()
            ->orderByDesc('deleted_at')
            ->orderByDesc('id')
            ->get();
        $publishedCount = $activeItems->where('is_published', true)->count();
        $activeItemsByIdentity = $activeItems
            ->filter(fn (GalleryItem $item): bool => $item->replacementIdentity() !== null)
            ->groupBy(fn (GalleryItem $item): string => (string) $item->replacementIdentity());

        $replacementCandidatesByArchivedId = $archivedItems->mapWithKeys(function (GalleryItem $archivedItem) use ($activeItemsByIdentity, $publishedCount): array {
            $identity = $archivedItem->replacementIdentity();
            $candidates = $identity === null
                ? collect()
                : $activeItemsByIdentity->get($identity, collect());

            $safeCandidates = $candidates
                ->filter(fn (GalleryItem $candidate): bool => ! (
                    $candidate->is_published &&
                    ! $archivedItem->is_published &&
                    $publishedCount <= 1
                ))
                ->values();

            return [$archivedItem->getKey() => $safeCandidates];
        });

        $pageSections = Schema::hasTable('gallery_page_sections')
            ? GalleryPageSection::query()
                ->withCount('mediaItems')
                ->orderBy('id')
                ->get()
            : collect();

        return view('admin.gallery.index', [
            'adminPageKey' => 'galeri',
            'activeItems' => $activeItems,
            'archivedItems' => $archivedItems,
            'replacementCandidatesByArchivedId' => $replacementCandidatesByArchivedId,
            'pageSections' => $pageSections,
            'limits' => $this->limits(),
            'canCreate' => $activeItems->count() < self::MAX_ITEMS,
            'canRestoreWithoutReplacement' => $activeItems->count() < self::MAX_ITEMS,
        ]);
    }

    public function show(GalleryItem $galleryItem): View
    {
        return view('admin.gallery.show', [
            'adminPageKey' => 'galeri',
            'item' => $galleryItem,
            'limits' => $this->limits(),
        ]);
    }

    public function create(): View|RedirectResponse
    {
        if (GalleryItem::query()->count() >= self::MAX_ITEMS) {
            return redirect()
                ->route('admin.galeri')
                ->withErrors(['title_id' => 'Maksimal hanya boleh 6 item galeri aktif.']);
        }

        return view('admin.gallery.form', [
            'adminPageKey' => 'galeri',
            'mode' => 'create',
            'item' => new GalleryItem([
                'type' => 'photo',
                'category_id' => 'Kegiatan',
                'category_en' => 'Activities',
                'sort_order' => $this->nextSortOrder(),
                'is_published' => true,
                'published_at' => now(),
            ]),
            'limits' => $this->limits(),
            'typeOptions' => $this->typeOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (GalleryItem::query()->count() >= self::MAX_ITEMS) {
            throw ValidationException::withMessages([
                'title_id' => 'Maksimal hanya boleh 6 item galeri aktif.',
            ]);
        }

        $data = $this->validatedData($request);
        [$data, $newPath] = $this->applyMedia($request, $data);
        $sortOrder = $this->nextSortOrder();

        try {
            $item = new GalleryItem($data);
            $item->forceFill(['sort_order' => $sortOrder])->save();
        } catch (Throwable $exception) {
            $this->deleteStoredPublicPath($newPath);

            throw $exception;
        }

        $this->normalizeSortOrders();

        return redirect()
            ->route('admin.galeri.show', $item)
            ->with('success', 'Item galeri berhasil ditambahkan.');
    }

    public function edit(GalleryItem $galleryItem): View
    {
        return view('admin.gallery.form', [
            'adminPageKey' => 'galeri',
            'mode' => 'edit',
            'item' => $galleryItem,
            'limits' => $this->limits(),
            'typeOptions' => $this->typeOptions(),
        ]);
    }

    public function update(Request $request, GalleryItem $galleryItem): RedirectResponse
    {
        $oldMediaUrl = $galleryItem->media_url;
        $data = $this->validatedData($request, $galleryItem);
        [$data, $newPath, $replacesStoredFile] = $this->applyMedia($request, $data, $galleryItem);

        try {
            $galleryItem->update($data);
        } catch (Throwable $exception) {
            $this->deleteStoredPublicPath($newPath);

            throw $exception;
        }

        if ($replacesStoredFile) {
            $this->deleteStoredPublicFile($oldMediaUrl, $galleryItem->getKey());
        }

        $this->normalizeSortOrders();

        return redirect()
            ->route('admin.galeri.show', $galleryItem)
            ->with('success', 'Item galeri berhasil diperbarui.');
    }

    public function destroy(GalleryItem $galleryItem): RedirectResponse
    {
        if ($galleryItem->is_published && GalleryItem::query()->where('is_published', true)->count() <= 1) {
            return back()->withErrors([
                'delete' => 'Minimal harus ada 1 item galeri yang aktif.',
            ]);
        }

        $galleryItem->delete();
        $this->normalizeSortOrders();

        return redirect()
            ->route('admin.galeri')
            ->with('success', 'Item galeri dipindahkan ke arsip dan dapat dipulihkan.');
    }
}
