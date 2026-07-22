<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use App\Rules\SafeImageUpload;
use App\Support\HeroVideoUrl;
use App\Support\PublicUrl;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

final class HeroSlideAdminController extends Controller
{
    private const MAX_IMAGE_KB = 10240;

    public function __construct()
    {
        app()->setLocale('id');
    }

    public function index(): View
    {
        $this->normalizeSortOrders();

        return view('admin.hero.index', [
            'adminPageKey' => 'hero',
            'slides' => HeroSlide::query()->ordered()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.hero.form', [
            'adminPageKey' => 'hero',
            'mode' => 'create',
            'slide' => new HeroSlide([
                'type' => 'image',
                'focal_position' => 'center center',
                'overlay_strength' => 0.46,
                'sort_order' => $this->nextSortOrder(),
                'is_active' => true,
                'cta_action' => 'anchor',
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        [$data, $storedPaths] = $this->applyMedia($request, $data);
        $data['sort_order'] = $this->nextSortOrder();

        try {
            HeroSlide::query()->create($data);
        } catch (Throwable $exception) {
            $this->deleteStoredPaths($storedPaths);
            throw $exception;
        }

        $this->normalizeSortOrders();

        return redirect()->route('admin.hero')->with('success', 'Hero slide berhasil ditambahkan.');
    }

    public function edit(HeroSlide $heroSlide): View
    {
        return view('admin.hero.form', [
            'adminPageKey' => 'hero',
            'mode' => 'edit',
            'slide' => $heroSlide,
        ]);
    }

    public function update(Request $request, HeroSlide $heroSlide): RedirectResponse
    {
        $oldMediaUrl = $heroSlide->media_url;
        $oldPosterUrl = $heroSlide->poster_url;
        $data = $this->validatedData($request, $heroSlide);
        [$data, $storedPaths] = $this->applyMedia($request, $data, $heroSlide);

        try {
            $heroSlide->update($data);
        } catch (Throwable $exception) {
            $this->deleteStoredPaths($storedPaths);
            throw $exception;
        }

        if (($storedPaths['media'] ?? null) !== null) {
            $this->deleteStoredFile($oldMediaUrl);
        }

        if (($storedPaths['poster'] ?? null) !== null) {
            $this->deleteStoredFile($oldPosterUrl);
        }

        return redirect()->route('admin.hero')->with('success', 'Hero slide berhasil diperbarui.');
    }

    public function destroy(HeroSlide $heroSlide): RedirectResponse
    {
        $mediaUrl = $heroSlide->media_url;
        $posterUrl = $heroSlide->poster_url;
        $heroSlide->delete();
        $this->deleteStoredFile($mediaUrl);
        $this->deleteStoredFile($posterUrl);
        $this->normalizeSortOrders();

        return redirect()->route('admin.hero')->with('success', 'Hero slide berhasil dihapus.');
    }

    public function toggle(HeroSlide $heroSlide): RedirectResponse
    {
        $heroSlide->update(['is_active' => ! $heroSlide->is_active]);

        return back()->with('success', 'Status hero slide berhasil diubah.');
    }

    public function moveUp(HeroSlide $heroSlide): RedirectResponse
    {
        $this->move($heroSlide, true);

        return back()->with('success', 'Urutan hero berhasil diperbarui.');
    }

    public function moveDown(HeroSlide $heroSlide): RedirectResponse
    {
        $this->move($heroSlide, false);

        return back()->with('success', 'Urutan hero berhasil diperbarui.');
    }

    /** @return array<string, mixed> */
    private function validatedData(Request $request, ?HeroSlide $heroSlide = null): array
    {
        $type = (string) $request->input('type', 'image');

        $data = $request->validate([
            'type' => ['required', Rule::in(['image', 'video'])],
            'media_file' => [
                Rule::prohibitedIf(fn (): bool => $type === 'video'),
                'nullable',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                new SafeImageUpload,
                'max:'.self::MAX_IMAGE_KB,
            ],
            'media_url' => ['nullable', 'string', 'max:2048'],
            'poster_file' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', new SafeImageUpload, 'max:'.self::MAX_IMAGE_KB],
            'poster_url' => ['nullable', 'string', 'max:2048'],
            'media_alt_id' => ['nullable', 'string', 'max:255'],
            'media_alt_en' => ['nullable', 'string', 'max:255'],
            'media_alt_ar' => ['nullable', 'string', 'max:255'],
            'eyebrow_id' => ['nullable', 'string', 'max:160'],
            'eyebrow_en' => ['nullable', 'string', 'max:160'],
            'eyebrow_ar' => ['nullable', 'string', 'max:160'],
            'title_id' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'description_id' => ['nullable', 'string', 'max:2000'],
            'description_en' => ['nullable', 'string', 'max:2000'],
            'description_ar' => ['nullable', 'string', 'max:2000'],
            'cta_label_id' => ['nullable', 'string', 'max:160'],
            'cta_label_en' => ['nullable', 'string', 'max:160'],
            'cta_label_ar' => ['nullable', 'string', 'max:160'],
            'cta_url' => ['nullable', 'string', 'max:2048'],
            'cta_action' => ['nullable', Rule::in(['anchor', 'link', 'admission'])],
            'focal_position' => ['nullable', 'string', 'max:60', 'regex:/^[a-z0-9%.\s-]+$/i'],
            'overlay_strength' => ['nullable', 'numeric', 'min:0.28', 'max:0.88'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['focal_position'] = trim((string) ($data['focal_position'] ?? '')) ?: 'center center';
        $data['overlay_strength'] = round((float) ($data['overlay_strength'] ?? 0.46), 2);
        $data['cta_action'] = $data['cta_action'] ?? null;
        $data['cta_url'] = $this->normalizeHeroLink($data['cta_url'] ?? null);

        if ($type === 'video') {
            $normalizedVideo = HeroVideoUrl::normalize($data['media_url'] ?? null);

            if ($normalizedVideo === null) {
                throw ValidationException::withMessages([
                    'media_url' => 'Gunakan URL YouTube HTTPS atau file video publik HTTPS berformat MP4, WebM, OGG, atau OGV.',
                ]);
            }

            $data['media_url'] = $normalizedVideo;
        } elseif (! $request->hasFile('media_file')) {
            $candidate = trim((string) ($data['media_url'] ?? ''));

            if ($candidate === '') {
                $candidate = trim((string) ($heroSlide?->media_url ?? ''));
            }

            if ($candidate === '') {
                throw ValidationException::withMessages([
                    'media_file' => 'Upload gambar hero atau isi URL gambar publik.',
                ]);
            }

            $data['media_url'] = $this->normalizeMediaUrl($candidate, 'media_url');
        }

        if (! $request->hasFile('poster_file')) {
            $poster = trim((string) ($data['poster_url'] ?? ''));
            $data['poster_url'] = $poster === ''
                ? ($heroSlide?->poster_url)
                : $this->normalizeMediaUrl($poster, 'poster_url');
        }

        unset($data['media_file'], $data['poster_file']);

        return $data;
    }

    /** @param array<string, mixed> $data
     *  @return array{0: array<string, mixed>, 1: array{media: ?string, poster: ?string}}
     */
    private function applyMedia(Request $request, array $data, ?HeroSlide $heroSlide = null): array
    {
        $storedPaths = ['media' => null, 'poster' => null];

        if ($request->hasFile('media_file')) {
            $path = $request->file('media_file')->store('hero/slides', 'public');
            $storedPaths['media'] = $path;
            $data['media_url'] = '/storage/'.$path;
        } elseif (! array_key_exists('media_url', $data) && $heroSlide) {
            $data['media_url'] = $heroSlide->media_url;
        }

        if ($request->hasFile('poster_file')) {
            $path = $request->file('poster_file')->store('hero/posters', 'public');
            $storedPaths['poster'] = $path;
            $data['poster_url'] = '/storage/'.$path;
        }

        return [$data, $storedPaths];
    }

    private function normalizeMediaUrl(string $url, string $field): string
    {
        if (str_starts_with($url, '/storage/')) {
            return $url;
        }

        $normalized = PublicUrl::normalize($url);

        if ($normalized === null || strtolower((string) parse_url($normalized, PHP_URL_SCHEME)) !== 'https') {
            throw ValidationException::withMessages([
                $field => 'URL media harus menggunakan HTTPS publik yang valid.',
            ]);
        }

        return $normalized;
    }

    private function normalizeHeroLink(mixed $url): ?string
    {
        if (! is_string($url) || trim($url) === '') {
            return null;
        }

        $url = trim($url);

        if (preg_match('/^#[A-Za-z][A-Za-z0-9_-]*$/', $url) === 1) {
            return $url;
        }

        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return $url;
        }

        return PublicUrl::normalize($url, ['/admin', '/login', '/auth']);
    }

    private function nextSortOrder(): int
    {
        return (int) HeroSlide::query()->max('sort_order') + 1;
    }

    private function normalizeSortOrders(): void
    {
        HeroSlide::query()->ordered()->get()->values()->each(
            fn (HeroSlide $slide, int $index) => $slide->forceFill(['sort_order' => $index + 1])->saveQuietly()
        );
    }

    private function move(HeroSlide $heroSlide, bool $up): void
    {
        $this->normalizeSortOrders();
        $heroSlide->refresh();

        $query = HeroSlide::query()->where(
            'sort_order',
            $up ? '<' : '>',
            $heroSlide->sort_order,
        );

        $other = $up
            ? $query->orderByDesc('sort_order')->orderByDesc('id')->first()
            : $query->orderBy('sort_order')->orderBy('id')->first();

        if (! $other) {
            return;
        }

        DB::transaction(function () use ($heroSlide, $other): void {
            $current = $heroSlide->sort_order;
            $heroSlide->forceFill(['sort_order' => $other->sort_order])->save();
            $other->forceFill(['sort_order' => $current])->save();
        });

        $this->normalizeSortOrders();
    }

    /** @param array{media: ?string, poster: ?string} $paths */
    private function deleteStoredPaths(array $paths): void
    {
        foreach ($paths as $path) {
            if (is_string($path) && $path !== '') {
                Storage::disk('public')->delete($path);
            }
        }
    }

    private function deleteStoredFile(?string $url): void
    {
        if (! is_string($url) || ! str_starts_with($url, '/storage/')) {
            return;
        }

        Storage::disk('public')->delete(substr($url, strlen('/storage/')));
    }
}
