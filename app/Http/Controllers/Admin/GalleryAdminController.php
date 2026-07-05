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
    private const MAX_PHOTO_KB = GalleryItem::MAX_PHOTO_KB;

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
        $data = $this->applyMedia($request, $data);

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
        $data = $this->applyMedia($request, $data, $galleryItem);

        $galleryItem->update($data);

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

        $this->deleteStoredPublicFile($galleryItem->media_url);
        $galleryItem->delete();

        return redirect()
            ->route('admin.galeri')
            ->with('success', 'Item galeri berhasil dihapus.');
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
        $isPhoto = $type === 'photo';
        $needsPhotoFile = $isPhoto && (
            ! $galleryItem?->exists ||
            $galleryItem->type !== 'photo' ||
            ! $galleryItem->media_url
        );

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'type' => ['required', Rule::in(['photo', 'embed'])],
            'category' => ['required', 'string', 'max:80'],
            'caption' => ['nullable', 'string', 'max:1000'],
            'media_file' => [
                $needsPhotoFile ? 'required' : 'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:' . self::MAX_PHOTO_KB,
            ],
            'media_url' => [
                Rule::requiredIf(fn (): bool => $type === 'embed'),
                'nullable',
                'url',
                'max:2048',
            ],
            'sort_order' => ['required', 'integer', 'min:1', 'max:' . self::MAX_ITEMS],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        unset($validated['media_file']);

        $validated['is_published'] = $request->boolean('is_published');

        if (($validated['published_at'] ?? null) === '') {
            $validated['published_at'] = null;
        }

        if ($this->wouldLeaveNoPublishedItem($galleryItem, $validated['is_published'])) {
            throw ValidationException::withMessages([
                'is_published' => 'Minimal harus ada 1 item galeri yang aktif.',
            ]);
        }

        return $validated;
    }

    private function applyMedia(Request $request, array $data, ?GalleryItem $currentItem = null): array
    {
        if ($data['type'] === 'embed') {
            $this->deleteStoredPublicFile($currentItem?->media_url);
            $data['media_url'] = $this->normalizeEmbedUrl((string) ($data['media_url'] ?? ''));

            return $data;
        }

        unset($data['media_url']);

        if ($request->hasFile('media_file')) {
            $this->deleteStoredPublicFile($currentItem?->media_url);
            $data['media_url'] = Storage::url($request->file('media_file')->store('gallery/photos', 'public'));
        }

        return $data;
    }

    private function normalizeEmbedUrl(string $url): string
    {
        $url = trim($url);
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        if (in_array($host, ['youtu.be', 'www.youtu.be'], true) && $path !== '') {
            return 'https://www.youtube.com/embed/' . strtok($path, '/');
        }

        if (str_contains($host, 'youtube.com')) {
            if (! empty($query['v'])) {
                return 'https://www.youtube.com/embed/' . $query['v'];
            }

            if (preg_match('~(?:shorts|embed)/([^/?#]+)~', $path, $match)) {
                return 'https://www.youtube.com/embed/' . $match[1];
            }
        }

        if (str_contains($host, 'tiktok.com') && preg_match('~/video/(\d+)~', '/' . $path, $match)) {
            return 'https://www.tiktok.com/embed/v2/' . $match[1];
        }

        if (str_contains($host, 'instagram.com') && ! str_ends_with($path, 'embed')) {
            return rtrim($url, '/') . '/embed';
        }

        return $url;
    }

    private function deleteStoredPublicFile(?string $url): void
    {
        if (! $url || ! str_starts_with($url, '/storage/')) {
            return;
        }

        Storage::disk('public')->delete(substr($url, strlen('/storage/')));
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
            'max_photo_mb' => 10,
            'max_photo_kb' => self::MAX_PHOTO_KB,
        ];
    }

    private function typeOptions(): array
    {
        return [
            'photo' => 'Foto',
            'embed' => 'Embed',
        ];
    }
}
