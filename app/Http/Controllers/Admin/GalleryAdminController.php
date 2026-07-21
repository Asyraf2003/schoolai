<?php
/* REAL_GALLERY_CRUD_CONTROLLER_FINAL */

namespace App\Http\Controllers\Admin;

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
            'title_ar' => ['nullable', 'string', 'max:160'],
            'type' => ['required', Rule::in(['photo', 'video'])],
            'category_id' => ['required', 'string', 'max:80'],
            'category_en' => ['nullable', 'string', 'max:80'],
            'category_ar' => ['nullable', 'string', 'max:80'],
            'caption_id' => ['nullable', 'string', 'max:1000'],
            'caption_en' => ['nullable', 'string', 'max:1000'],
            'caption_ar' => ['nullable', 'string', 'max:1000'],
            'media_file' => [
                Rule::requiredIf(fn (): bool => $needsPhotoFile),
                Rule::prohibitedIf(fn (): bool => $type === 'video'),
                'nullable',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                new SafeImageUpload,
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

        foreach (['title_en', 'title_ar', 'category_en', 'category_ar', 'caption_id', 'caption_en', 'caption_ar'] as $field) {
            $value = $validated[$field] ?? null;
            $validated[$field] = is_string($value) && trim($value) !== '' ? trim($value) : null;
        }

        $validated['title'] = $validated['title_id'];
        $validated['category'] = $validated['category_id'];
        $validated['caption'] = $validated['caption_id'];
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
            $data['media_url'] = $this->normalizeVideoUrl((string) ($data['media_url'] ?? ''));

            return [$data, null, $currentItem?->type === 'photo'];
        }

        unset($data['media_url']);

        if ($request->hasFile('media_file')) {
            $path = $request->file('media_file')->store('gallery/photos', 'public');

            if (! is_string($path) || $path === '') {
                throw ValidationException::withMessages([
                    'media_file' => 'Foto gagal disimpan. Silakan coba lagi.',
                ]);
            }

            $data['media_url'] = Storage::url($path);

            return [$data, $path, $currentItem?->type === 'photo'];
        }

        return [$data, null, false];
    }

    private function deleteStoredPublicPath(?string $path): void
    {
        if ($path !== null && $path !== '') {
            Storage::disk('public')->delete($path);
        }
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

        if ($this->hostMatches($host, 'tiktok.com') && preg_match('~(?:^|/)(?:video|player/v1|embed/v2)/(\d+)(?:/|$)~', $path, $match)) {
            return 'https://www.tiktok.com/player/v1/' . $match[1];
        }

        if ($this->hostMatches($host, 'instagram.com') && preg_match('~^(p|reel|tv)/([^/]+)~', $path, $match)) {
            return 'https://www.instagram.com/' . $match[1] . '/' . rawurlencode($match[2]) . '/embed';
        }

        if ($scheme === 'https' && $this->hostMatches($host, 'facebook.com')) {
            $facebookUrl = $path === 'plugins/video.php'
                ? trim((string) ($query['href'] ?? ''))
                : $url;
            $facebookVideoId = $this->facebookVideoId($facebookUrl);

            if ($facebookVideoId !== null) {
                return $this->facebookReelEmbedUrl($facebookVideoId);
            }
        }

        if ($this->hostMatches($host, 'vimeo.com') && preg_match('~^(?:video/)?(\d+)$~', $path, $match)) {
            return 'https://player.vimeo.com/video/' . $match[1];
        }

        throw ValidationException::withMessages([
            'media_url' => 'URL video belum didukung. Gunakan YouTube, TikTok, Instagram, Vimeo, atau Facebook Reel/Watch. Link Facebook share/r belum didukung.',
        ]);
    }

    private function facebookVideoId(string $url): ?string
    {
        if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        if ($scheme !== 'https' || ! $this->hostMatches($host, 'facebook.com')) {
            return null;
        }

        if (preg_match('~^reel/(\d+)$~', $path, $match)) {
            return $match[1];
        }

        $watchId = (string) ($query['v'] ?? '');

        return $path === 'watch' && preg_match('~^\d+$~', $watchId)
            ? $watchId
            : null;
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

    private function deleteStoredPublicFile(?string $url, int|string|null $exceptItemId = null): void
    {
        if (! $url || ! str_starts_with($url, '/storage/')) {
            return;
        }

        $otherReference = GalleryItem::withTrashed()
            ->where('media_url', $url)
            ->when(
                $exceptItemId !== null,
                fn ($query) => $query->where('id', '!=', $exceptItemId)
            )
            ->exists();

        if ($otherReference) {
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
