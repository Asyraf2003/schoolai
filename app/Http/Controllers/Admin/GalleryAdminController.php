<?php
/* REAL_GALLERY_CRUD_CONTROLLER_FINAL */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        return view('admin.gallery.index', [
            'adminPageKey' => 'galeri',
            'items' => GalleryItem::query()->ordered()->get(),
            'limits' => $this->limits(),
            'dbMap' => $this->databaseMap(),
            'canCreate' => GalleryItem::query()->count() < self::MAX_ITEMS,
        ]);
    }

    public function create(): View|RedirectResponse
    {
        if (GalleryItem::query()->count() >= self::MAX_ITEMS) {
            return redirect()
                ->route('admin.galeri')
                ->withErrors(['title' => 'Maksimal hanya boleh 6 item galeri. Hapus atau edit item yang sudah ada.']);
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

        GalleryItem::query()->create($data);

        return redirect()
            ->route('admin.galeri')
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

        $galleryItem->update($data);

        return redirect()
            ->route('admin.galeri')
            ->with('success', 'Item galeri berhasil diperbarui.');
    }

    public function destroy(GalleryItem $galleryItem): RedirectResponse
    {
        if ($galleryItem->is_published && GalleryItem::query()->where('is_published', true)->count() <= 1) {
            return back()->withErrors([
                'delete' => 'Minimal harus ada 1 item galeri yang published.',
            ]);
        }

        $galleryItem->delete();

        return redirect()
            ->route('admin.galeri')
            ->with('success', 'Item galeri berhasil dihapus.');
    }

    private function validatedData(Request $request, ?GalleryItem $galleryItem = null): array
    {
        $type = (string) $request->input('type', 'photo');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'type' => ['required', Rule::in(['photo', 'video', 'reel'])],
            'category' => ['required', 'string', 'max:80'],
            'caption' => ['nullable', 'string', 'max:1000'],
            'thumbnail_url' => ['nullable', 'string', 'max:255'],
            'media_url' => ['nullable', 'string', 'max:255'],
            'duration_seconds' => [
                Rule::requiredIf(fn (): bool => in_array($type, ['video', 'reel'], true)),
                'nullable',
                'integer',
                'min:1',
                'max:' . self::MAX_VIDEO_SECONDS,
            ],
            'sort_order' => ['required', 'integer', 'min:1', 'max:' . self::MAX_ITEMS],
            'is_published' => ['nullable', 'boolean'],
            'fallback_icon' => ['required', 'string', 'max:16'],
            'accent' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'published_at' => ['nullable', 'date'],
        ]);

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

    private function databaseMap(): array
    {
        return [
            ['field' => 'id', 'type' => 'bigint unsigned', 'note' => 'Primary key.'],
            ['field' => 'title', 'type' => 'varchar(160)', 'note' => 'Judul item galeri. Wajib.'],
            ['field' => 'type', 'type' => 'varchar(16)', 'note' => 'photo, video, atau reel.'],
            ['field' => 'category', 'type' => 'varchar(80)', 'note' => 'Kategori tampilan publik.'],
            ['field' => 'caption', 'type' => 'text nullable', 'note' => 'Deskripsi singkat.'],
            ['field' => 'thumbnail_url', 'type' => 'varchar(255) nullable', 'note' => 'Path/URL thumbnail.'],
            ['field' => 'media_url', 'type' => 'varchar(255) nullable', 'note' => 'Path/URL foto atau video.'],
            ['field' => 'duration_seconds', 'type' => 'unsigned smallint nullable', 'note' => 'Wajib untuk video/reel. Max 180.'],
            ['field' => 'sort_order', 'type' => 'unsigned tinyint', 'note' => 'Urutan tampil. Max 6 item.'],
            ['field' => 'is_published', 'type' => 'boolean', 'note' => 'Status tampil di publik.'],
            ['field' => 'published_at', 'type' => 'timestamp nullable', 'note' => 'Tanggal publikasi.'],
        ];
    }
}
