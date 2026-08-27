<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\GalleryItem;
use App\Models\GalleryPageSection;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

        $activeItems = GalleryItem::query()->withCount('sections')->ordered()->get();
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
                ->withCount('items')
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
            'canCreate' => true,
            'canRestoreWithoutReplacement' => true,
        ]);
    }

    public function show(GalleryItem $galleryItem): View
    {
        $galleryItem->load('sections');

        return view('admin.gallery.show', [
            'adminPageKey' => 'galeri',
            'item' => $galleryItem,
            'pageSections' => GalleryPageSection::query()->orderBy('id')->get(),
            'limits' => $this->limits(),
        ]);
    }

    public function create(): View
    {
        return view('admin.gallery.form', [
            'adminPageKey' => 'galeri',
            'mode' => 'create',
            'item' => new GalleryItem([
                'type' => 'photo',
                'category_id' => 'Kegiatan',
                'category_en' => 'Activities',
                'sort_order' => $this->nextSortOrder(),
                'is_published' => true,
                'show_on_homepage' => false,
                'show_on_gallery_page' => true,
                'published_at' => now(),
            ]),
            'pageSections' => GalleryPageSection::query()->orderBy('id')->get(),
            'limits' => $this->limits(),
            'typeOptions' => $this->typeOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $sectionIds = $data['section_ids'];
        unset($data['section_ids']);
        [$data, $newPath] = $this->applyMedia($request, $data);
        $sortOrder = $this->nextSortOrder();

        try {
            $item = DB::transaction(function () use ($data, $sectionIds, $sortOrder): GalleryItem {
                $item = new GalleryItem($data);
                $item->forceFill(['sort_order' => $sortOrder])->save();
                $this->syncSections($item, $sectionIds);

                return $item;
            });
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
        $galleryItem->load('sections');

        return view('admin.gallery.form', [
            'adminPageKey' => 'galeri',
            'mode' => 'edit',
            'item' => $galleryItem,
            'pageSections' => GalleryPageSection::query()->orderBy('id')->get(),
            'limits' => $this->limits(),
            'typeOptions' => $this->typeOptions(),
        ]);
    }

    public function update(Request $request, GalleryItem $galleryItem): RedirectResponse
    {
        $oldMediaUrl = $galleryItem->media_url;
        $data = $this->validatedData($request, $galleryItem);
        $sectionIds = $data['section_ids'];
        unset($data['section_ids']);
        [$data, $newPath, $replacesStoredFile] = $this->applyMedia($request, $data, $galleryItem);

        try {
            DB::transaction(function () use ($galleryItem, $data, $sectionIds): void {
                $galleryItem->update($data);
                $this->syncSections($galleryItem, $sectionIds);
            });
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
        $galleryItem->delete();
        $this->normalizeSortOrders();

        return redirect()
            ->route('admin.galeri')
            ->with('success', 'Item galeri dipindahkan ke arsip dan dapat dipulihkan.');
    }

    private function syncSections(GalleryItem $item, array $sectionIds): void
    {
        $placements = collect($sectionIds)
            ->mapWithKeys(fn (int|string $sectionId, int $index): array => [
                (int) $sectionId => [
                    'sort_order' => $index + 1,
                    'is_published' => true,
                ],
            ])
            ->all();

        $item->sections()->sync($placements);
    }
}
