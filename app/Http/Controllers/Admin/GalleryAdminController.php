<?php
/* REAL_GALLERY_CRUD_CONTROLLER_FINAL */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use App\Models\GalleryPageSection;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class GalleryAdminController extends Controller
{
    private const MAX_ITEMS = GalleryItem::MAX_ITEMS;
    private const MAX_PHOTO_KB = GalleryItem::MAX_PHOTO_KB;

    public function __construct()
    {
        app()->setLocale('id');
    }

    public function __invoke(): View
    {
        return $this->index();
    }

    public function index(): View
    {
        $this->normalizeSortOrdersIfNeeded();

        $items = GalleryItem::query()->ordered()->get();

        $pageSections = Schema::hasTable('gallery_page_sections')
            ? GalleryPageSection::query()
                ->withCount('mediaItems')
                ->orderBy('id')
                ->get()
            : collect();

        return view('admin.gallery.index', [
            'adminPageKey' => 'galeri',
            'items' => $items,
            'pageSections' => $pageSections,
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
                ->withErrors(['title_id' => 'Maksimal hanya boleh 6 item galeri.']);
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
                'title_id' => 'Maksimal hanya boleh 6 item galeri.',
            ]);
        }

        $data = $this->validatedData($request);
        $data = $this->applyMedia($request, $data);
        $sortOrder = $this->nextSortOrder();

        $item = new GalleryItem($data);
        $item->forceFill(['sort_order' => $sortOrder])->save();

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
        $data = $this->validatedData($request, $galleryItem);
        $data = $this->applyMedia($request, $data, $galleryItem);

        $galleryItem->update($data);

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

        $this->deleteStoredPublicFile($galleryItem->media_url);
        $galleryItem->delete();

        $this->normalizeSortOrders();

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
            'title_id' => ['required', 'string', 'max:160'],
            'title_en' => ['nullable', 'string', 'max:160'],
            'type' => ['required', Rule::in(['photo', 'video'])],
            'category_id' => ['required', 'string', 'max:80'],
            'category_en' => ['nullable', 'string', 'max:80'],
            'caption_id' => ['nullable', 'string', 'max:1000'],
            'caption_en' => ['nullable', 'string', 'max:1000'],
            'media_file' => [
                Rule::requiredIf(fn (): bool => $needsPhotoFile),
                Rule::prohibitedIf(fn (): bool => $type === 'video'),
                'nullable',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:' . self::MAX_PHOTO_KB,
            ],
            'media_url' => [
                Rule::requiredIf(fn (): bool => $type === 'video'),
                Rule::prohibitedIf(fn (): bool => $type === 'photo'),
                'nullable',
                'url',
                'max:2048',
            ],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ], [
            'title_id.required' => 'Judul Indonesia wajib diisi.',
            'category_id.required' => 'Kategori Indonesia wajib diisi.',
            'media_file.required' => 'Upload foto wajib diisi untuk tipe Foto.',
            'media_file.prohibited' => 'Tipe Video tidak menerima upload file. Gunakan URL video.',
            'media_file.image' => 'File harus berupa gambar.',
            'media_file.mimes' => 'Foto harus JPG, PNG, atau WebP.',
            'media_file.max' => 'Ukuran foto maksimal 10MB.',
            'media_url.required' => 'URL video wajib diisi untuk tipe Video.',
            'media_url.prohibited' => 'Tipe Foto tidak menerima URL video. Gunakan upload foto.',
            'media_url.url' => 'URL video tidak valid.',
        ]);

        unset($validated['media_file']);

        $validated['title'] = $validated['title_id'];
        $validated['category'] = $validated['category_id'];
        $validated['caption'] = $validated['caption_id'] ?? null;
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
        if ($data['type'] === 'video') {
            $this->deleteStoredPublicFile($currentItem?->media_url);
            $data['media_url'] = $this->normalizeVideoUrl((string) ($data['media_url'] ?? ''));

            return $data;
        }

        unset($data['media_url']);

        if ($request->hasFile('media_file')) {
            $this->deleteStoredPublicFile($currentItem?->media_url);
            $data['media_url'] = Storage::url($request->file('media_file')->store('gallery/photos', 'public'));
        }

        return $data;
    }

    private function normalizeVideoUrl(string $url): string
    {
        $url = trim($url);
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        if (
            $url === '' ||
            $host === '' ||
            ! in_array($scheme, ['http', 'https'], true) ||
            ! filter_var($url, FILTER_VALIDATE_URL)
        ) {
            throw ValidationException::withMessages([
                'media_url' => 'URL video tidak valid.',
            ]);
        }

        if ($this->hostMatches($host, 'youtu.be') && $path !== '') {
            $parts = explode('/', $path);

            return 'https://www.youtube.com/embed/' . rawurlencode((string) $parts[0]);
        }

        if ($this->hostMatches($host, 'youtube.com')) {
            if (! empty($query['v'])) {
                return 'https://www.youtube.com/embed/' . rawurlencode((string) $query['v']);
            }

            if (preg_match('~(?:^|/)(?:shorts|embed)/([^/?#]+)~', $path, $match)) {
                return 'https://www.youtube.com/embed/' . rawurlencode($match[1]);
            }
        }

        if ($this->hostMatches($host, 'tiktok.com') && preg_match('~(?:^|/)video/(\d+)(?:/|$)~', $path, $match)) {
            return 'https://www.tiktok.com/embed/v2/' . $match[1];
        }

        if ($this->hostMatches($host, 'instagram.com') && preg_match('~^(p|reel|tv)/([^/]+)~', $path, $match)) {
            return 'https://www.instagram.com/' . $match[1] . '/' . rawurlencode($match[2]) . '/embed';
        }

        if ($this->hostMatches($host, 'facebook.com')) {
            if (preg_match('~^reel/(\d+)$~', $path, $match)) {
                return $this->facebookReelEmbedUrl($match[1]);
            }

            if ($path === 'plugins/video.php' && ! empty($query['href'])) {
                $facebookUrl = trim((string) $query['href']);
                $facebookScheme = strtolower((string) parse_url($facebookUrl, PHP_URL_SCHEME));
                $facebookHost = strtolower((string) parse_url($facebookUrl, PHP_URL_HOST));
                $facebookPath = trim((string) parse_url($facebookUrl, PHP_URL_PATH), '/');

                if (
                    $facebookScheme === 'https' &&
                    $this->hostMatches($facebookHost, 'facebook.com') &&
                    preg_match('~^reel/(\d+)$~', $facebookPath, $match)
                ) {
                    return $this->facebookReelEmbedUrl($match[1]);
                }
            }
        }

        if ($this->hostMatches($host, 'vimeo.com') && preg_match('~^(?:video/)?(\d+)$~', $path, $match)) {
            return 'https://player.vimeo.com/video/' . $match[1];
        }

        throw ValidationException::withMessages([
            'media_url' => 'URL video belum didukung. Gunakan YouTube, TikTok, Instagram, Facebook, atau Vimeo.',
        ]);
    }

    private function facebookReelEmbedUrl(string $reelId): string
    {
        $reelUrl = 'https://www.facebook.com/reel/' . rawurlencode($reelId) . '/';

        return 'https://www.facebook.com/plugins/video.php?' . http_build_query([
            'height' => 476,
            'href' => $reelUrl,
            'show_text' => 'false',
            'width' => 267,
            't' => 0,
        ], '', '&', PHP_QUERY_RFC3986);
    }

    private function hostMatches(string $host, string $domain): bool
    {
        return $host === $domain || str_ends_with($host, '.' . $domain);
    }

    private function deleteStoredPublicFile(?string $url): void
    {
        if (! $url || ! str_starts_with($url, '/storage/')) {
            return;
        }

        $path = substr($url, strlen('/storage/'));

        if ($path === '' || str_contains($path, '..') || str_starts_with($path, '/') || str_contains($path, '\\')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    private function wouldLeaveNoPublishedItem(?GalleryItem $currentItem, bool $nextPublished): bool
    {
        if ($nextPublished) {
            return false;
        }

        $query = GalleryItem::query()->where('is_published', true);

        if ($currentItem?->exists) {
            $query->where($currentItem->getKeyName(), '!=', $currentItem->getKey());
        }

        return $query->count() < 1;
    }

    private function nextSortOrder(): int
    {
        return min(((int) GalleryItem::query()->max('sort_order')) + 1, self::MAX_ITEMS);
    }

    private function normalizeSortOrdersIfNeeded(): void
    {
        $orders = GalleryItem::query()
            ->ordered()
            ->pluck('sort_order')
            ->values()
            ->all();

        foreach ($orders as $index => $order) {
            if ((int) $order !== $index + 1) {
                $this->normalizeSortOrders();
                return;
            }
        }
    }

    private function normalizeSortOrders(): void
    {
        GalleryItem::query()
            ->ordered()
            ->get()
            ->values()
            ->each(function (GalleryItem $item, int $index): void {
                $expectedOrder = $index + 1;

                if ($item->sort_order !== $expectedOrder) {
                    $item->forceFill(['sort_order' => $expectedOrder])->save();
                }
            });
    }

    private function swapSortOrder(GalleryItem $firstItem, GalleryItem $secondItem): void
    {
        $firstSortOrder = $firstItem->sort_order;

        $firstItem->forceFill(['sort_order' => $secondItem->sort_order])->save();
        $secondItem->forceFill(['sort_order' => $firstSortOrder])->save();
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
            'video' => 'Video',
        ];
    }
}
