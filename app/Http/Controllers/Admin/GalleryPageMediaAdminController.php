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
        $createdCount = $this->storeMany($request, $galleryPageSection);

        return redirect()
            ->route('admin.galeri.sections.show', $galleryPageSection)
            ->with('success', "{$createdCount} media berhasil ditambahkan.");
    }

    public function show(GalleryPageMediaItem $galleryPageMediaItem): View
    {
        $galleryPageMediaItem->load('section');

        return view('admin.gallery.page-media.show', [
            'adminPageKey' => 'galeri',
            'section' => $galleryPageMediaItem->section,
            'item' => $galleryPageMediaItem,
        ]);
    }

    public function edit(GalleryPageMediaItem $galleryPageMediaItem): View
    {
        $galleryPageMediaItem->load('section');

        return view('admin.gallery.page-media.edit', [
            'adminPageKey' => 'galeri',
            'section' => $galleryPageMediaItem->section,
            'mode' => 'edit',
            'item' => $galleryPageMediaItem,
            'typeOptions' => $this->typeOptions(),
        ]);
    }

    public function update(Request $request, GalleryPageMediaItem $galleryPageMediaItem): RedirectResponse
    {
        $data = $this->validatedSingleData($request, $galleryPageMediaItem);
        $data = $this->applySingleMedia($request, $data, $galleryPageMediaItem);

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

    private function storeMany(Request $request, GalleryPageSection $section): int
    {
        $type = (string) $request->input('type', 'photo');

        if ($type === 'photo') {
            $validated = $request->validate([
                'type' => ['required', Rule::in(['photo'])],
                'media_files' => ['required', 'array', 'min:1'],
                'media_files.*' => [
                    'required',
                    'file',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:' . self::MAX_PHOTO_KB,
                ],
                'media_urls' => ['nullable'],
                'is_published' => ['nullable', 'boolean'],
                'published_at' => ['nullable', 'date'],
            ], [
                'media_files.required' => 'Minimal pilih 1 foto.',
                'media_files.*.image' => 'Semua file harus berupa gambar.',
                'media_files.*.mimes' => 'Foto harus JPG, PNG, atau WebP.',
                'media_files.*.max' => 'Ukuran tiap foto maksimal 10MB.',
            ]);

            $created = 0;

            foreach ($request->file('media_files', []) as $file) {
                $section->mediaItems()->create([
                    'type' => 'photo',
                    'media_url' => Storage::url($file->store('gallery/page', 'public')),
                    'is_published' => $request->boolean('is_published'),
                    'published_at' => $validated['published_at'] ?? null,
                    'title_id' => null,
                    'title_en' => null,
                    'description_id' => null,
                    'description_en' => null,
                ]);

                $created++;
            }

            return $created;
        }

        $validated = $request->validate([
            'type' => ['required', Rule::in(['video'])],
            'media_urls' => ['required', 'string', 'max:20000'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ], [
            'media_urls.required' => 'Minimal tempel 1 URL video/embed.',
        ]);

        $urls = collect(preg_split('/\R+/', (string) $validated['media_urls']))
            ->map(fn (string $url): string => trim($url))
            ->filter()
            ->unique()
            ->values();

        if ($urls->isEmpty()) {
            throw ValidationException::withMessages([
                'media_urls' => 'Minimal tempel 1 URL video/embed.',
            ]);
        }

        $created = 0;

        foreach ($urls as $url) {
            $section->mediaItems()->create([
                'type' => 'video',
                'media_url' => $this->normalizeVideoUrl($url),
                'is_published' => $request->boolean('is_published'),
                'published_at' => $validated['published_at'] ?? null,
                'title_id' => null,
                'title_en' => null,
                'description_id' => null,
                'description_en' => null,
            ]);

            $created++;
        }

        return $created;
    }

    private function validatedSingleData(Request $request, ?GalleryPageMediaItem $mediaItem = null): array
    {
        $type = (string) $request->input('type', 'photo');
        $isPhoto = $type === 'photo';

        $needsPhotoFile = $isPhoto && (
            ! $mediaItem?->exists ||
            $mediaItem->type !== 'photo' ||
            ! $mediaItem->media_url
        );

        $validated = $request->validate([
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

        $validated['title_id'] = null;
        $validated['title_en'] = null;
        $validated['description_id'] = null;
        $validated['description_en'] = null;
        $validated['is_published'] = $request->boolean('is_published');

        if (($validated['published_at'] ?? null) === '') {
            $validated['published_at'] = null;
        }

        return $validated;
    }

    private function applySingleMedia(Request $request, array $data, ?GalleryPageMediaItem $currentItem = null): array
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
                'media_urls' => 'URL video tidak valid.',
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
            'media_urls' => 'URL video belum didukung. Gunakan YouTube, TikTok, Instagram, Facebook, atau Vimeo.',
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
