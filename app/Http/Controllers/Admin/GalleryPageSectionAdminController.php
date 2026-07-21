<?php
/* GALLERY_PAGE_SECTION_ADMIN_CONTROLLER_FINAL */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryPageSection;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class GalleryPageSectionAdminController extends Controller
{
    public function __construct()
    {
        app()->setLocale('id');
    }

    public function create(): View
    {
        return view('admin.gallery.page-sections.form', [
            'adminPageKey' => 'galeri',
            'mode' => 'create',
            'section' => new GalleryPageSection(['is_published' => true]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $section = GalleryPageSection::create($this->validatedData($request));

        return redirect()
            ->route('admin.galeri.sections.show', $section)
            ->with('success', 'Bagian galeri berhasil ditambahkan.');
    }

    public function show(GalleryPageSection $galleryPageSection): View
    {
        $galleryPageSection->load([
            'mediaItemsWithTrashed' => fn ($query) => $query->orderByDesc('published_at')->orderByDesc('id'),
        ]);

        $mediaItems = $galleryPageSection->mediaItemsWithTrashed;
        $activeMediaByIdentity = $mediaItems
            ->reject->trashed()
            ->filter(fn ($item): bool => $item->replacementIdentity() !== null)
            ->groupBy(fn ($item): string => (string) $item->replacementIdentity());

        $replacementCandidatesByArchivedId = $mediaItems
            ->filter->trashed()
            ->mapWithKeys(function ($archivedItem) use ($activeMediaByIdentity): array {
                $identity = $archivedItem->replacementIdentity();

                return [
                    $archivedItem->getKey() => $identity === null
                        ? collect()
                        : $activeMediaByIdentity->get($identity, collect())->values(),
                ];
            });

        return view('admin.gallery.page-sections.show', [
            'adminPageKey' => 'galeri',
            'section' => $galleryPageSection,
            'mediaItems' => $mediaItems,
            'replacementCandidatesByArchivedId' => $replacementCandidatesByArchivedId,
        ]);
    }

    public function edit(GalleryPageSection $galleryPageSection): View
    {
        return view('admin.gallery.page-sections.form', [
            'adminPageKey' => 'galeri',
            'mode' => 'edit',
            'section' => $galleryPageSection,
        ]);
    }

    public function update(Request $request, GalleryPageSection $galleryPageSection): RedirectResponse
    {
        $galleryPageSection->update($this->validatedData($request));

        return redirect()
            ->route('admin.galeri.sections.show', $galleryPageSection)
            ->with('success', 'Bagian galeri berhasil diperbarui.');
    }

    public function toggle(GalleryPageSection $galleryPageSection): RedirectResponse
    {
        $galleryPageSection->update([
            'is_published' => ! $galleryPageSection->is_published,
        ]);

        return back()->with('success', 'Status bagian galeri berhasil diubah.');
    }

    public function destroy(GalleryPageSection $galleryPageSection): RedirectResponse
    {
        $galleryPageSection->delete();

        return redirect()
            ->route('admin.galeri')
            ->with('success', 'Bagian galeri dipindahkan ke arsip. Semua media tetap tersimpan.');
    }

    public function restore(Request $request, int $galleryPageSection): RedirectResponse
    {
        $data = $request->validate([
            'replacement_gallery_page_section_id' => ['nullable', 'integer'],
        ]);

        $replacementId = isset($data['replacement_gallery_page_section_id'])
            ? (int) $data['replacement_gallery_page_section_id']
            : null;

        if ($replacementId === null) {
            GalleryPageSection::onlyTrashed()->findOrFail($galleryPageSection)->restore();

            return redirect()
                ->route('admin.galeri')
                ->with('success', 'Bagian galeri berhasil dipulihkan beserta seluruh medianya.');
        }

        DB::transaction(function () use ($galleryPageSection, $replacementId): void {
            $archivedSection = GalleryPageSection::onlyTrashed()
                ->lockForUpdate()
                ->findOrFail($galleryPageSection);

            $replacementSection = GalleryPageSection::query()
                ->lockForUpdate()
                ->findOrFail($replacementId);

            $archivedIdentity = $archivedSection->replacementIdentity();
            $replacementIdentity = $replacementSection->replacementIdentity();

            if ($archivedIdentity === null || $archivedIdentity !== $replacementIdentity) {
                throw ValidationException::withMessages([
                    'replacement_gallery_page_section_id' => 'Bagian pengganti harus merupakan bagian aktif dengan judul Indonesia yang identik.',
                ]);
            }

            $replacementSection->delete();
            $archivedSection->restore();
        });

        return redirect()
            ->route('admin.galeri')
            ->with('success', 'Bagian lama dipulihkan dan bagian aktif pengganti dipindahkan ke arsip. Semua media tetap tersimpan.');
    }

    private function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'title_id' => ['required', 'string'],
            'title_en' => ['nullable', 'string'],
            'title_ar' => ['nullable', 'string'],
            'description_id' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
        ], [
            'title_id.required' => 'Judul Indonesia wajib diisi.',
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        return $validated;
    }
}
