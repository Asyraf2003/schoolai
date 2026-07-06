<?php
/* GALLERY_PAGE_SECTION_ADMIN_CONTROLLER_FINAL */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryPageSection;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'mediaItems' => fn ($query) => $query->orderByDesc('published_at')->orderByDesc('id'),
        ]);

        return view('admin.gallery.page-sections.show', [
            'adminPageKey' => 'galeri',
            'section' => $galleryPageSection,
            'mediaItems' => $galleryPageSection->mediaItems,
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
        $galleryPageSection->load('mediaItems');

        foreach ($galleryPageSection->mediaItems as $mediaItem) {
            $this->deleteStoredPublicFile($mediaItem->media_url);
        }

        $galleryPageSection->delete();

        return redirect()
            ->route('admin.galeri')
            ->with('success', 'Bagian galeri berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'title_id' => ['required', 'string'],
            'title_en' => ['nullable', 'string'],
            'description_id' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
        ], [
            'title_id.required' => 'Judul Indonesia wajib diisi.',
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        return $validated;
    }

    private function deleteStoredPublicFile(?string $url): void
    {
        if (! $url || ! str_starts_with($url, '/storage/')) {
            return;
        }

        $path = substr($url, strlen('/storage/'));

        if ($path === '' || str_contains($path, '..') || str_starts_with($path, '/')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
