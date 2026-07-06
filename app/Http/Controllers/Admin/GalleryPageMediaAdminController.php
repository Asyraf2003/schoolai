<?php
/* GALLERY_PAGE_MEDIA_ADMIN_CONTROLLER_FINAL */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryPageMediaItem;
use App\Models\GalleryPageSection;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class GalleryPageMediaAdminController extends Controller
{
    private const MAX_PHOTO_KB = GalleryPageMediaItem::MAX_PHOTO_KB;

    public function __construct()
    {
        app()->setLocale('id');
    }

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
        $data = $this->validatedData($request);
        $data = $this->applyMedia($request, $data);

        $item = new GalleryPageMediaItem($data);
        $item->section()->associate($galleryPageSection);
        $item->save();

        return redirect()
            ->route('admin.galeri.section-media.show', $item)
            ->with('success', 'Media halaman galeri berhasil ditambahkan.');
    }

    public function show(GalleryPageMediaItem $galleryPageMediaItem): View
    {
        $galleryPageMediaItem->load('section');

        return view('admin.gallery.page-media.show', [
            'adminPageKey' => 'galeri',
            'section' => $galleryPageMediaItem->section,
            'item' => $galleryPageMediaItem,
            'mode' => 'edit',
            'typeOptions' => $this->typeOptions(),
        ]);
    }

    public function update(Request $request, GalleryPageMediaItem $galleryPageMediaItem): RedirectResponse
    {
        $data = $this->validatedData($request, $galleryPageMediaItem);
        $data = $this->applyMedia($request, $data, $galleryPageMediaItem);

        $galleryPageMediaItem->update($data);

        return redirect()
            ->route('admin.galeri.section-media.show', $galleryPageMediaItem)
            ->with('success', 'Media halaman galeri berhasil diperbarui.');
    }

    public function toggle(GalleryPageMediaItem $galleryPageMediaItem): RedirectResponse
    {
        $galleryPageMediaItem->update([
            'is_published' => ! $galleryPageMediaItem->is_published,
        ]);

        return back()->with('success', 'Status media berhasil diubah.');
    }

    public function destroy(GalleryPageMediaItem $galleryPageMediaItem): RedirectResponse
    {
        $section = $galleryPageMediaItem->section;
        $this->deleteStoredPublicFile($galleryPageMediaItem->media_url);
        $galleryPageMediaItem->delete();

        return redirect()
            ->route('admin.galeri.sections.show', $section)
            ->with('success', 'Media halaman galeri berhasil dihapus.');
    }

    private function validatedData(Request $request, ?GalleryPageMediaItem $mediaItem = null): array
    {
        $type = (string) $request->input('type', 'photo');
        $isPhoto = $type === 'photo';

        $needsPhotoFile = $isPhoto && (
            ! $mediaItem?->exists ||
            $mediaItem->type !== 'photo' ||
            ! $mediaItem->media_url
        );

        $validated = $request->validate([
            'title_id' => ['required', 'string'],
            'title_en' => ['nullable', 'string'],
            'description_id' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'type' => ['required', Rule::in(['photo', 'video'])],
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

        $validated['is_published'] = $request->boolean('is_published');

        if (($validated['published_at'] ?? null) === '') {
            $validated['published_at'] = null;
        }

        return $validated;
    }

    private function applyMedia(Request $request, array $data, ?GalleryPageMediaItem $currentItem = null): array
    {
        if ($data['type'] === 'video') {
            $this->deleteStoredPublicFile($currentItem?->media_url);
            $data['media_url'] = $this->normalizeVideoUrl((string) ($data['media_url'] ?? ''));

            return $data;
        }

        unset($data['media_url']);

        if ($request->hasFile('media_file')) {
            $this->deleteStoredPublicFile($currentItem?->media_url);
            $data['media_url'] = Storage::url($request->file('media_file')->store('gallery/page', 'public'));
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

        if ($this->hostMatches($host, 'vimeo.com') && preg_match('~^(?:video/)?(\d+)$~', $path, $match)) {
            return 'https://player.vimeo.com/video/' . $match[1];
        }

        throw ValidationException::withMessages([
            'media_url' => 'URL video belum didukung. Gunakan YouTube, TikTok, Instagram, atau Vimeo.',
        ]);
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

        if ($path === '' || str_contains($path, '..') || str_starts_with($path, '/')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    private function typeOptions(): array
    {
        return [
            'photo' => 'Foto',
            'video' => 'Video',
        ];
    }
}
