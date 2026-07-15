<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PpdbShowcaseItem;
use App\Rules\SafeImageUpload;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

final class PpdbShowcaseAdminController extends Controller
{
    public function __construct()
    {
        app()->setLocale('id');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        [$data, $newPath] = $this->applyMedia($request, $data);
        $data['sort_order'] = $this->nextSortOrder($data['audience']);

        try {
            PpdbShowcaseItem::query()->create($data);
        } catch (Throwable $exception) {
            $this->deleteStoredPublicPath($newPath);

            throw $exception;
        }
        $this->normalizeSortOrders($data['audience']);

        return $this->redirectToShowcase()->with('success', 'Item konten PPDB berhasil ditambahkan.');
    }

    public function edit(PpdbShowcaseItem $ppdbShowcaseItem): View
    {
        return app(PpdbSettingController::class)->editShowcaseItem($ppdbShowcaseItem);
    }

    public function update(Request $request, PpdbShowcaseItem $ppdbShowcaseItem): RedirectResponse
    {
        $oldAudience = $ppdbShowcaseItem->audience;
        $oldMediaUrl = $ppdbShowcaseItem->media_url;
        $data = $this->validatedData($request, $ppdbShowcaseItem);
        [$data, $newPath, $replacesStoredFile] = $this->applyMedia($request, $data, $ppdbShowcaseItem);

        if ($oldAudience !== $data['audience']) {
            $data['sort_order'] = $this->nextSortOrder($data['audience']);
        }

        try {
            $ppdbShowcaseItem->update($data);
        } catch (Throwable $exception) {
            $this->deleteStoredPublicPath($newPath);

            throw $exception;
        }

        if ($replacesStoredFile) {
            $this->deleteStoredPublicFile($oldMediaUrl, $ppdbShowcaseItem->getKey());
        }
        $this->normalizeSortOrders($oldAudience);
        $this->normalizeSortOrders($data['audience']);

        return $this->redirectToShowcase()->with('success', 'Item konten PPDB berhasil diperbarui.');
    }

    public function destroy(PpdbShowcaseItem $ppdbShowcaseItem): RedirectResponse
    {
        $audience = $ppdbShowcaseItem->audience;
        $ppdbShowcaseItem->delete();
        $this->normalizeSortOrders($audience);

        return $this->redirectToShowcase()
            ->with('success', 'Item konten PPDB dipindahkan ke arsip dan dapat dipulihkan.');
    }

    public function restore(Request $request, int $ppdbShowcaseItem): RedirectResponse
    {
        $data = $request->validate([
            'replacement_ppdb_showcase_item_id' => ['nullable', 'integer'],
        ]);

        $replacementId = isset($data['replacement_ppdb_showcase_item_id'])
            ? (int) $data['replacement_ppdb_showcase_item_id']
            : null;

        if ($replacementId === null) {
            $audience = DB::transaction(function () use ($ppdbShowcaseItem): string {
                $archivedItem = PpdbShowcaseItem::onlyTrashed()
                    ->lockForUpdate()
                    ->findOrFail($ppdbShowcaseItem);

                $audience = $archivedItem->audience;
                $archivedItem->forceFill([
                    'sort_order' => $this->nextSortOrder($audience),
                ])->save();
                $archivedItem->restore();

                return $audience;
            });

            $this->normalizeSortOrders($audience);

            return $this->redirectToShowcase()->with('success', 'Item konten PPDB berhasil dipulihkan.');
        }

        $audience = DB::transaction(function () use ($ppdbShowcaseItem, $replacementId): string {
            $archivedItem = PpdbShowcaseItem::onlyTrashed()
                ->lockForUpdate()
                ->findOrFail($ppdbShowcaseItem);

            $replacementItem = PpdbShowcaseItem::query()
                ->lockForUpdate()
                ->findOrFail($replacementId);

            $archivedIdentity = $archivedItem->replacementIdentity();
            $replacementIdentity = $replacementItem->replacementIdentity();

            if ($archivedIdentity === null || $archivedIdentity !== $replacementIdentity) {
                throw ValidationException::withMessages([
                    'replacement_ppdb_showcase_item_id' => 'Item pengganti harus aktif serta memiliki target tab dan judul Indonesia yang identik.',
                ]);
            }

            $sortOrder = $replacementItem->sort_order;
            $audience = $archivedItem->audience;

            $replacementItem->delete();
            $archivedItem->forceFill(['sort_order' => $sortOrder])->save();
            $archivedItem->restore();

            return $audience;
        });

        $this->normalizeSortOrders($audience);

        return $this->redirectToShowcase()
            ->with('success', 'Item PPDB lama dipulihkan dan item aktif pengganti dipindahkan ke arsip.');
    }

    public function moveUp(PpdbShowcaseItem $ppdbShowcaseItem): RedirectResponse
    {
        $this->normalizeSortOrders($ppdbShowcaseItem->audience);
        $ppdbShowcaseItem->refresh();

        $previousItem = PpdbShowcaseItem::query()
            ->forAudience($ppdbShowcaseItem->audience)
            ->where('sort_order', '<', $ppdbShowcaseItem->sort_order)
            ->orderByDesc('sort_order')
            ->orderByDesc('id')
            ->first();

        if ($previousItem) {
            $this->swapSortOrder($ppdbShowcaseItem, $previousItem);
            $this->normalizeSortOrders($ppdbShowcaseItem->audience);
        }

        return $this->redirectToShowcase()->with('success', 'Urutan konten PPDB berhasil diperbarui.');
    }

    public function moveDown(PpdbShowcaseItem $ppdbShowcaseItem): RedirectResponse
    {
        $this->normalizeSortOrders($ppdbShowcaseItem->audience);
        $ppdbShowcaseItem->refresh();

        $nextItem = PpdbShowcaseItem::query()
            ->forAudience($ppdbShowcaseItem->audience)
            ->where('sort_order', '>', $ppdbShowcaseItem->sort_order)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        if ($nextItem) {
            $this->swapSortOrder($ppdbShowcaseItem, $nextItem);
            $this->normalizeSortOrders($ppdbShowcaseItem->audience);
        }

        return $this->redirectToShowcase()->with('success', 'Urutan konten PPDB berhasil diperbarui.');
    }

    private function validatedData(Request $request, ?PpdbShowcaseItem $currentItem = null): array
    {
        $mediaType = (string) $request->input('media_type', PpdbShowcaseItem::MEDIA_PHOTO);
        $needsPhotoFile = $mediaType === PpdbShowcaseItem::MEDIA_PHOTO && (
            ! $currentItem?->exists ||
            $currentItem->media_type !== PpdbShowcaseItem::MEDIA_PHOTO ||
            ! $currentItem->media_url
        );

        $validated = $request->validate([
            'audience' => ['required', Rule::in(PpdbShowcaseItem::AUDIENCES)],
            'title_id' => ['required', 'string', 'max:180'],
            'title_en' => ['nullable', 'string', 'max:180'],
            'description_id' => ['required', 'string', 'max:1200'],
            'description_en' => ['nullable', 'string', 'max:1200'],
            'media_type' => ['required', Rule::in(PpdbShowcaseItem::MEDIA_TYPES)],
            'media_file' => [
                Rule::requiredIf(fn (): bool => $needsPhotoFile),
                Rule::prohibitedIf(fn (): bool => $mediaType === PpdbShowcaseItem::MEDIA_VIDEO),
                'nullable',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                new SafeImageUpload,
                'max:' . PpdbShowcaseItem::MAX_PHOTO_KB,
            ],
            'media_url' => [
                Rule::requiredIf(fn (): bool => $mediaType === PpdbShowcaseItem::MEDIA_VIDEO),
                Rule::prohibitedIf(fn (): bool => $mediaType === PpdbShowcaseItem::MEDIA_PHOTO),
                'nullable',
                'url',
                'max:2048',
            ],
        ], [
            'audience.required' => 'Tujuan tampilan wajib dipilih.',
            'audience.in' => 'Tujuan tampilan PPDB tidak valid.',
            'title_id.required' => 'Judul Indonesia wajib diisi.',
            'description_id.required' => 'Deskripsi Indonesia wajib diisi.',
            'media_file.required' => 'Upload foto wajib diisi jika tipe media Foto.',
            'media_file.prohibited' => 'Tipe URL tidak menerima upload file. Gunakan kolom URL.',
            'media_file.image' => 'File harus berupa gambar.',
            'media_file.mimes' => 'Foto harus JPG, PNG, atau WebP.',
            'media_file.max' => 'Ukuran foto maksimal 10MB.',
            'media_url.required' => 'URL wajib diisi jika tipe media URL.',
            'media_url.prohibited' => 'Tipe Foto tidak menerima URL. Gunakan upload foto.',
            'media_url.url' => 'URL tidak valid.',
        ]);

        unset($validated['media_file']);

        $validated['title_id'] = trim((string) $validated['title_id']);
        $validated['title_en'] = $this->nullableText($validated['title_en'] ?? null);
        $validated['description_id'] = trim((string) $validated['description_id']);
        $validated['description_en'] = $this->nullableText($validated['description_en'] ?? null);

        return $validated;
    }

    private function applyMedia(Request $request, array $data, ?PpdbShowcaseItem $currentItem = null): array
    {
        if ($data['media_type'] === PpdbShowcaseItem::MEDIA_VIDEO) {
            $data['media_url'] = $this->normalizeVideoUrl((string) ($data['media_url'] ?? ''));

            return [$data, null, $currentItem?->media_type === PpdbShowcaseItem::MEDIA_PHOTO];
        }

        unset($data['media_url']);

        if ($request->hasFile('media_file')) {
            $path = $request->file('media_file')->store('ppdb/showcase', 'public');

            if (! is_string($path) || $path === '') {
                throw ValidationException::withMessages([
                    'media_file' => 'Foto gagal disimpan. Silakan coba lagi.',
                ]);
            }

            $data['media_url'] = Storage::url($path);

            return [$data, $path, $currentItem?->media_type === PpdbShowcaseItem::MEDIA_PHOTO];
        }

        $data['media_url'] = $currentItem?->media_type === PpdbShowcaseItem::MEDIA_PHOTO
            ? $currentItem->media_url
            : null;

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
            throw ValidationException::withMessages(['media_url' => 'URL tidak valid.']);
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
            'media_url' => 'URL belum didukung. Gunakan YouTube, TikTok, Instagram, atau Vimeo.',
        ]);
    }

    private function deleteStoredPublicFile(?string $url, int|string|null $exceptItemId = null): void
    {
        if (! $url || ! str_starts_with($url, '/storage/')) {
            return;
        }

        $otherReference = PpdbShowcaseItem::withTrashed()
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

    private function nextSortOrder(string $audience): int
    {
        return ((int) PpdbShowcaseItem::query()
            ->forAudience($audience)
            ->max('sort_order')) + 1;
    }

    private function normalizeSortOrders(string $audience): void
    {
        PpdbShowcaseItem::query()
            ->forAudience($audience)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->values()
            ->each(function (PpdbShowcaseItem $item, int $index): void {
                $expectedOrder = $index + 1;

                if ($item->sort_order !== $expectedOrder) {
                    $item->forceFill(['sort_order' => $expectedOrder])->save();
                }
            });
    }

    private function swapSortOrder(PpdbShowcaseItem $firstItem, PpdbShowcaseItem $secondItem): void
    {
        $firstSortOrder = $firstItem->sort_order;

        $firstItem->forceFill(['sort_order' => $secondItem->sort_order])->save();
        $secondItem->forceFill(['sort_order' => $firstSortOrder])->save();
    }

    private function redirectToShowcase(): RedirectResponse
    {
        return redirect()->to(route('admin.ppdb') . '#ppdb-showcase-admin');
    }

    private function hostMatches(string $host, string $domain): bool
    {
        return $host === $domain || str_ends_with($host, '.' . $domain);
    }

    private function nullableText(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
