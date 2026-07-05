<?php
/* REAL_GALLERY_CRUD_CONTROLLER_FINAL */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class GalleryAdminController extends Controller
{
    private const MAX_ITEMS = GalleryItem::MAX_ITEMS;
    private const MAX_VIDEO_SECONDS = GalleryItem::MAX_VIDEO_SECONDS;

    public function __invoke(): View
    {
        return $this->index();
    }

    public function index(): View
    {
        $items = GalleryItem::query()->ordered()->get();

        return view('admin.gallery.index', [
            'adminPageKey' => 'galeri',
            'items' => $items,
            'limits' => $this->limits(),
            'canCreate' => $items->count() < self::MAX_ITEMS,
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
                ->withErrors(['title' => 'Maksimal hanya boleh 6 item galeri.']);
        }

        return view('admin.gallery.form', [
            'adminPageKey' => 'galeri',
            'mode' => 'create',
            'item' => new GalleryItem([
                'type' => 'photo',
                'category' => 'Kegiatan',
                'sort_order' => min(GalleryItem::query()->count() + 1, self::MAX_ITEMS),
                'is_published' => true,
                'fallback_icon' => '📸',
                'accent' => '#19aee6',
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
                'title' => 'Maksimal hanya boleh 6 item galeri.',
            ]);
        }

        $data = $this->validatedData($request);
        $data = $this->applyUploadedFiles($request, $data);
        $data = array_merge($data, $this->visualDefaultsForType($data['type']));

        $item = GalleryItem::query()->create($data);

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
        $data = $this->validatedData($request, $galleryItem);
        $data = $this->applyUploadedFiles($request, $data, $galleryItem);
        $data = array_merge($data, $this->visualDefaultsForType($data['type']));

        $galleryItem->update($data);

        return redirect()
            ->route('admin.galeri.show', $galleryItem)
            ->with('success', 'Item galeri berhasil diperbarui.');
    }

    public function destroy(GalleryItem $galleryItem): RedirectResponse
    {
        if ($galleryItem->is_published && GalleryItem::query()->where('is_published', true)->count() <= 1) {
            return back()->withErrors([
                'delete' => 'Minimal harus ada 1 item galeri yang published.',
            ]);
        }

        $this->deleteStoredPublicFile($galleryItem->media_url);
        $this->deleteStoredPublicFile($galleryItem->thumbnail_url);

        $galleryItem->delete();

        return redirect()
            ->route('admin.galeri')
            ->with('success', 'Item galeri berhasil dihapus.');
    }

    public function toggle(GalleryItem $galleryItem): RedirectResponse
    {
        if ($galleryItem->is_published && GalleryItem::query()->where('is_published', true)->count() <= 1) {
            return back()->withErrors([
                'is_published' => 'Minimal harus ada 1 item galeri yang published.',
            ]);
        }

        $galleryItem->update([
            'is_published' => ! $galleryItem->is_published,
        ]);

        return back()->with('success', 'Status item galeri berhasil diubah.');
    }

    public function moveUp(GalleryItem $galleryItem): RedirectResponse
    {
        $previousItem = GalleryItem::query()
            ->where('sort_order', '<', $galleryItem->sort_order)
            ->orderByDesc('sort_order')
            ->orderByDesc('id')
            ->first();

        if ($previousItem) {
            $this->swapSortOrder($galleryItem, $previousItem);
        }

        return back()->with('success', 'Posisi item galeri diperbarui.');
    }

    public function moveDown(GalleryItem $galleryItem): RedirectResponse
    {
        $nextItem = GalleryItem::query()
            ->where('sort_order', '>', $galleryItem->sort_order)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        if ($nextItem) {
            $this->swapSortOrder($galleryItem, $nextItem);
        }

        return back()->with('success', 'Posisi item galeri diperbarui.');
    }

    private function validatedData(Request $request, ?GalleryItem $galleryItem = null): array
    {
        $type = (string) $request->input('type', 'photo');
        $mediaRules = ['nullable', 'file', 'max:20480'];

        if ($type === 'photo') {
            $mediaRules[] = 'mimes:jpg,jpeg,png,webp';
        } else {
            $mediaRules[] = 'mimes:mp4,webm,mov';
        }

        if (! $galleryItem?->exists) {
            array_unshift($mediaRules, 'required');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'type' => ['required', Rule::in(['photo', 'video', 'reel'])],
            'category' => ['required', 'string', 'max:80'],
            'caption' => ['nullable', 'string', 'max:1000'],
            'media_file' => $mediaRules,
            'thumbnail_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'duration_seconds' => [
                Rule::requiredIf(fn (): bool => in_array($type, ['video', 'reel'], true)),
                'nullable',
                'integer',
                'min:1',
                'max:' . self::MAX_VIDEO_SECONDS,
            ],
            'sort_order' => ['required', 'integer', 'min:1', 'max:' . self::MAX_ITEMS],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        unset($validated['media_file'], $validated['thumbnail_file']);

        $validated['is_published'] = $request->boolean('is_published');

        if (! in_array($validated['type'], ['video', 'reel'], true)) {
            $validated['duration_seconds'] = null;
        }

        if (($validated['published_at'] ?? null) === '') {
            $validated['published_at'] = null;
        }

        if ($this->wouldLeaveNoPublishedItem($galleryItem, $validated['is_published'])) {
            throw ValidationException::withMessages([
                'is_published' => 'Minimal harus ada 1 item galeri yang published.',
            ]);
        }

        return $validated;
    }

    private function applyUploadedFiles(Request $request, array $data, ?GalleryItem $currentItem = null): array
    {
        if ($request->hasFile('media_file')) {
            $this->deleteStoredPublicFile($currentItem?->media_url);
            $data['media_url'] = Storage::url($request->file('media_file')->store('gallery/media', 'public'));
        }

        if ($request->hasFile('thumbnail_file')) {
            $this->deleteStoredPublicFile($currentItem?->thumbnail_url);
            $data['thumbnail_url'] = Storage::url($request->file('thumbnail_file')->store('gallery/thumbnails', 'public'));
        }

        return $data;
    }

    private function deleteStoredPublicFile(?string $url): void
    {
        if (! $url || ! str_starts_with($url, '/storage/')) {
            return;
        }

        Storage::disk('public')->delete(substr($url, strlen('/storage/')));
    }

    private function visualDefaultsForType(string $type): array
    {
        return match ($type) {
            'video' => [
                'fallback_icon' => '▶️',
                'accent' => '#f97316',
            ],
            'reel' => [
                'fallback_icon' => '🎬',
                'accent' => '#8b5cf6',
            ],
            default => [
                'fallback_icon' => '📸',
                'accent' => '#19aee6',
            ],
        };
    }

    private function wouldLeaveNoPublishedItem(?GalleryItem $currentItem, bool $nextPublished): bool
    {
        if ($nextPublished) {
            return false;
        }

        $query = GalleryItem::query()->where('is_published', true);

        if ($currentItem?->exists) {
            $query->whereKeyNot($currentItem->getKey());
        }

        return $query->count() < 1;
    }

    private function swapSortOrder(GalleryItem $firstItem, GalleryItem $secondItem): void
    {
        $firstSortOrder = $firstItem->sort_order;

        $firstItem->update(['sort_order' => $secondItem->sort_order]);
        $secondItem->update(['sort_order' => $firstSortOrder]);
    }

    private function limits(): array
    {
        return [
            'max_items' => self::MAX_ITEMS,
            'min_published_items' => 1,
            'max_video_seconds' => self::MAX_VIDEO_SECONDS,
            'max_video_minutes' => 3,
        ];
    }

    private function typeOptions(): array
    {
        return [
            'photo' => 'Foto',
            'video' => 'Video',
            'reel' => 'Reel',
        ];
    }
}
