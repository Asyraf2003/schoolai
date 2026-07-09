<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PpdbSetting;
use App\Models\PpdbShowcaseItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

final class PpdbSettingController extends Controller
{
    public function __construct()
    {
        app()->setLocale('id');
    }

    public function edit(): View
    {
        return $this->editView(new PpdbShowcaseItem([
            'audience' => PpdbShowcaseItem::AUDIENCE_PARENTS,
            'media_type' => PpdbShowcaseItem::MEDIA_PHOTO,
        ]), 'create');
    }

    public function update(Request $request): RedirectResponse
    {
        $validator = validator($request->all(), [
            'registration_url' => ['required', 'url', 'max:2048'],
            'information_url' => ['nullable', 'url', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'registration_url.required' => 'Link formulir PPDB wajib diisi.',
            'registration_url.url' => 'Link formulir PPDB tidak valid.',
            'registration_url.max' => 'Link formulir PPDB terlalu panjang.',
            'information_url.url' => 'Link info, brosur, atau PDF PPDB tidak valid.',
            'information_url.max' => 'Link info, brosur, atau PDF PPDB terlalu panjang.',
        ]);

        $validator->after(function (Validator $validator) use ($request): void {
            foreach ([
                'registration_url' => 'Link formulir PPDB',
                'information_url' => 'Link info, brosur, atau PDF PPDB',
            ] as $field => $label) {
                $url = trim((string) $request->input($field));

                if ($url !== '' && ! $this->isPublicUrl($url)) {
                    $validator->errors()->add($field, $label . ' harus URL publik, bukan localhost, IP lokal, login, atau halaman admin.');
                }
            }
        });

        $validated = $validator->validate();

        $setting = $this->currentSetting();
        $setting->update([
            'registration_url' => trim((string) $validated['registration_url']),
            'information_url' => $this->nullableText($validated['information_url'] ?? null),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.ppdb')
            ->with('success', 'Pengaturan PPDB berhasil disimpan.');
    }

    public function toggle(): RedirectResponse
    {
        $setting = $this->currentSetting();
        $setting->update([
            'is_active' => ! $setting->is_active,
        ]);

        return redirect()
            ->route('admin.ppdb')
            ->with('success', $setting->fresh()?->is_active ? 'PPDB berhasil diaktifkan.' : 'PPDB berhasil dinonaktifkan.');
    }

    public function storeShowcaseItem(Request $request): RedirectResponse
    {
        $data = $this->validatedShowcaseData($request);
        $data = $this->applyShowcaseMedia($request, $data);
        $data['sort_order'] = $this->nextShowcaseSortOrder($data['audience']);

        PpdbShowcaseItem::query()->create($data);

        $this->normalizeShowcaseSortOrders($data['audience']);

        return $this->redirectToShowcase()->with('success', 'Item konten PPDB berhasil ditambahkan.');
    }

    public function editShowcaseItem(PpdbShowcaseItem $ppdbShowcaseItem): View
    {
        return $this->editView($ppdbShowcaseItem, 'edit');
    }

    public function updateShowcaseItem(Request $request, PpdbShowcaseItem $ppdbShowcaseItem): RedirectResponse
    {
        $oldAudience = $ppdbShowcaseItem->audience;
        $data = $this->validatedShowcaseData($request, $ppdbShowcaseItem);
        $data = $this->applyShowcaseMedia($request, $data, $ppdbShowcaseItem);

        if ($oldAudience !== $data['audience']) {
            $data['sort_order'] = $this->nextShowcaseSortOrder($data['audience']);
        }

        $ppdbShowcaseItem->update($data);

        $this->normalizeShowcaseSortOrders($oldAudience);
        $this->normalizeShowcaseSortOrders($data['audience']);

        return $this->redirectToShowcase()->with('success', 'Item konten PPDB berhasil diperbarui.');
    }

    public function destroyShowcaseItem(PpdbShowcaseItem $ppdbShowcaseItem): RedirectResponse
    {
        $audience = $ppdbShowcaseItem->audience;
        $this->deleteStoredPublicFile($ppdbShowcaseItem->media_url);
        $ppdbShowcaseItem->delete();

        $this->normalizeShowcaseSortOrders($audience);

        return $this->redirectToShowcase()->with('success', 'Item konten PPDB berhasil dihapus.');
    }

    public function moveShowcaseItemUp(PpdbShowcaseItem $ppdbShowcaseItem): RedirectResponse
    {
        $this->normalizeShowcaseSortOrders($ppdbShowcaseItem->audience);
        $ppdbShowcaseItem->refresh();

        $previousItem = PpdbShowcaseItem::query()
            ->forAudience($ppdbShowcaseItem->audience)
            ->where('sort_order', '<', $ppdbShowcaseItem->sort_order)
            ->orderByDesc('sort_order')
            ->orderByDesc('id')
            ->first();

        if ($previousItem) {
            $this->swapShowcaseSortOrder($ppdbShowcaseItem, $previousItem);
            $this->normalizeShowcaseSortOrders($ppdbShowcaseItem->audience);
        }

        return $this->redirectToShowcase()->with('success', 'Urutan konten PPDB berhasil diperbarui.');
    }

    public function moveShowcaseItemDown(PpdbShowcaseItem $ppdbShowcaseItem): RedirectResponse
    {
        $this->normalizeShowcaseSortOrders($ppdbShowcaseItem->audience);
        $ppdbShowcaseItem->refresh();

        $nextItem = PpdbShowcaseItem::query()
            ->forAudience($ppdbShowcaseItem->audience)
            ->where('sort_order', '>', $ppdbShowcaseItem->sort_order)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        if ($nextItem) {
            $this->swapShowcaseSortOrder($ppdbShowcaseItem, $nextItem);
            $this->normalizeShowcaseSortOrders($ppdbShowcaseItem->audience);
        }

        return $this->redirectToShowcase()->with('success', 'Urutan konten PPDB berhasil diperbarui.');
    }

    private function editView(PpdbShowcaseItem $showcaseItemForm, string $showcaseFormMode): View
    {
        $this->normalizeShowcaseSortOrdersIfNeeded();

        return view('admin.ppdb.edit', [
            'adminPageKey' => 'ppdb',
            'setting' => $this->currentSetting(),
            'showcaseItems' => $this->showcaseItems(),
            'showcaseItemForm' => $showcaseItemForm,
            'showcaseFormMode' => $showcaseFormMode,
            'audienceOptions' => $this->audienceOptions(),
            'mediaTypeOptions' => $this->mediaTypeOptions(),
            'showcaseLimits' => $this->showcaseLimits(),
        ]);
    }

    private function currentSetting(): PpdbSetting
    {
        return PpdbSetting::query()->firstOrCreate([], [
            'registration_url' => PpdbSetting::DEFAULT_REGISTRATION_URL,
            'information_url' => null,
            'is_active' => true,
        ]);
    }

    private function showcaseItems()
    {
        if (! Schema::hasTable('ppdb_showcase_items')) {
            return collect();
        }

        return PpdbShowcaseItem::query()->ordered()->get();
    }

    private function validatedShowcaseData(Request $request, ?PpdbShowcaseItem $currentItem = null): array
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

    private function applyShowcaseMedia(Request $request, array $data, ?PpdbShowcaseItem $currentItem = null): array
    {
        if ($data['media_type'] === PpdbShowcaseItem::MEDIA_VIDEO) {
            $this->deleteStoredPublicFile($currentItem?->media_url);
            $data['media_url'] = $this->normalizeVideoUrl((string) ($data['media_url'] ?? ''));

            return $data;
        }

        unset($data['media_url']);

        if ($request->hasFile('media_file')) {
            $this->deleteStoredPublicFile($currentItem?->media_url);
            $data['media_url'] = Storage::url($request->file('media_file')->store('ppdb/showcase', 'public'));

            return $data;
        }

        $data['media_url'] = $currentItem?->media_type === PpdbShowcaseItem::MEDIA_PHOTO
            ? $currentItem->media_url
            : null;

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
                'media_url' => 'URL tidak valid.',
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
            'media_url' => 'URL belum didukung. Gunakan YouTube, TikTok, Instagram, atau Vimeo.',
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

        if ($path === '' || str_contains($path, '..') || str_starts_with($path, '/') || str_contains($path, '\\')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    private function nextShowcaseSortOrder(string $audience): int
    {
        return ((int) PpdbShowcaseItem::query()
            ->forAudience($audience)
            ->max('sort_order')) + 1;
    }

    private function normalizeShowcaseSortOrdersIfNeeded(): void
    {
        if (! Schema::hasTable('ppdb_showcase_items')) {
            return;
        }

        foreach (PpdbShowcaseItem::AUDIENCES as $audience) {
            $orders = PpdbShowcaseItem::query()
                ->forAudience($audience)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->pluck('sort_order')
                ->values()
                ->all();

            foreach ($orders as $index => $order) {
                if ((int) $order !== $index + 1) {
                    $this->normalizeShowcaseSortOrders($audience);
                    break;
                }
            }
        }
    }

    private function normalizeShowcaseSortOrders(string $audience): void
    {
        if (! Schema::hasTable('ppdb_showcase_items')) {
            return;
        }

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

    private function swapShowcaseSortOrder(PpdbShowcaseItem $firstItem, PpdbShowcaseItem $secondItem): void
    {
        $firstSortOrder = $firstItem->sort_order;

        $firstItem->forceFill(['sort_order' => $secondItem->sort_order])->save();
        $secondItem->forceFill(['sort_order' => $firstSortOrder])->save();
    }

    private function redirectToShowcase(): RedirectResponse
    {
        return redirect()->to(route('admin.ppdb') . '#ppdb-showcase-admin');
    }

    private function audienceOptions(): array
    {
        return [
            PpdbShowcaseItem::AUDIENCE_PARENTS => 'Untuk orang tua',
            PpdbShowcaseItem::AUDIENCE_SCHOOL => 'Untuk sekolah',
        ];
    }

    private function mediaTypeOptions(): array
    {
        return [
            PpdbShowcaseItem::MEDIA_PHOTO => 'Foto upload',
            PpdbShowcaseItem::MEDIA_VIDEO => 'URL video / embed',
        ];
    }

    private function showcaseLimits(): array
    {
        return [
            'max_photo_mb' => 10,
            'max_photo_kb' => PpdbShowcaseItem::MAX_PHOTO_KB,
        ];
    }

    private function isPublicUrl(string $url): bool
    {
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = '/' . ltrim((string) parse_url($url, PHP_URL_PATH), '/');

        if (! in_array($scheme, ['http', 'https'], true) || $host === '') {
            return false;
        }

        if (
            $host === 'localhost' ||
            $host === '127.0.0.1' ||
            $host === '::1' ||
            str_ends_with($host, '.local') ||
            str_starts_with($host, '10.') ||
            str_starts_with($host, '192.168.') ||
            preg_match('/^172\.(1[6-9]|2\d|3[0-1])\./', $host) === 1
        ) {
            return false;
        }

        return ! (
            $path === '/admin' ||
            str_starts_with($path, '/admin/') ||
            $path === '/login' ||
            str_starts_with($path, '/auth/')
        );
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
