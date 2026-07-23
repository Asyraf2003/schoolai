<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestimonialMedia;
use App\Rules\SafeImageUpload;
use App\Support\TestimonialVideoUrl;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class TestimonialMediaAdminController extends Controller
{
    public function __construct()
    {
        app()->setLocale('id');
    }

    public function index(): View
    {
        $this->normalizeSortOrders();

        return view('admin.testimonials.index', [
            'adminPageKey' => 'testimoni',
            'activeItems' => TestimonialMedia::query()->ordered()->get(),
            'archivedItems' => TestimonialMedia::onlyTrashed()->orderByDesc('deleted_at')->get(),
            'canCreate' => TestimonialMedia::query()->count() < TestimonialMedia::MAX_ITEMS,
            'maxItems' => TestimonialMedia::MAX_ITEMS,
        ]);
    }

    public function create(): View|RedirectResponse
    {
        if (TestimonialMedia::query()->count() >= TestimonialMedia::MAX_ITEMS) {
            return redirect()->route('admin.testimoni.index')->withErrors([
                'media_file' => 'Maksimal hanya boleh '.TestimonialMedia::MAX_ITEMS.' media testimoni aktif.',
            ]);
        }

        return view('admin.testimonials.form', [
            'adminPageKey' => 'testimoni',
            'mode' => 'create',
            'item' => new TestimonialMedia([
                'type' => 'photo',
                'source' => 'upload',
                'is_published' => true,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (TestimonialMedia::query()->count() >= TestimonialMedia::MAX_ITEMS) {
            throw ValidationException::withMessages([
                'media_file' => 'Slot media testimoni sudah penuh.',
            ]);
        }

        $data = $this->validatedData($request);
        $data = $this->applyMedia($request, $data);
        $data['sort_order'] = $this->nextSortOrder();

        TestimonialMedia::query()->create($data);
        $this->normalizeSortOrders();

        return redirect()->route('admin.testimoni.index')
            ->with('success', 'Media testimoni berhasil ditambahkan.');
    }

    public function edit(TestimonialMedia $testimonialMedia): View
    {
        return view('admin.testimonials.form', [
            'adminPageKey' => 'testimoni',
            'mode' => 'edit',
            'item' => $testimonialMedia,
        ]);
    }

    public function update(Request $request, TestimonialMedia $testimonialMedia): RedirectResponse
    {
        $data = $this->validatedData($request, $testimonialMedia);
        $data = $this->applyMedia($request, $data, $testimonialMedia);

        $testimonialMedia->update($data);

        return redirect()->route('admin.testimoni.index')
            ->with('success', 'Media testimoni berhasil diperbarui.');
    }

    public function destroy(TestimonialMedia $testimonialMedia): RedirectResponse
    {
        $testimonialMedia->delete();
        $this->normalizeSortOrders();

        return redirect()->route('admin.testimoni.index')
            ->with('success', 'Media testimoni dipindahkan ke arsip.');
    }

    public function restore(int $testimonialMedia): RedirectResponse
    {
        if (TestimonialMedia::query()->count() >= TestimonialMedia::MAX_ITEMS) {
            return back()->withErrors(['restore' => 'Slot media testimoni aktif sudah penuh.']);
        }

        $item = TestimonialMedia::onlyTrashed()->findOrFail($testimonialMedia);
        $item->forceFill(['sort_order' => $this->nextSortOrder()])->save();
        $item->restore();
        $this->normalizeSortOrders();

        return redirect()->route('admin.testimoni.index')
            ->with('success', 'Media testimoni berhasil dipulihkan.');
    }

    public function toggle(TestimonialMedia $testimonialMedia): RedirectResponse
    {
        $testimonialMedia->update(['is_published' => ! $testimonialMedia->is_published]);

        return back()->with('success', 'Status media testimoni berhasil diubah.');
    }

    public function moveUp(TestimonialMedia $testimonialMedia): RedirectResponse
    {
        $this->normalizeSortOrders();
        $testimonialMedia->refresh();

        $other = TestimonialMedia::query()
            ->where('sort_order', '<', $testimonialMedia->sort_order)
            ->orderByDesc('sort_order')
            ->first();

        if ($other) {
            $this->swapSortOrder($testimonialMedia, $other);
            $this->normalizeSortOrders();
        }

        return back()->with('success', 'Urutan testimoni berhasil diperbarui.');
    }

    public function moveDown(TestimonialMedia $testimonialMedia): RedirectResponse
    {
        $this->normalizeSortOrders();
        $testimonialMedia->refresh();

        $other = TestimonialMedia::query()
            ->where('sort_order', '>', $testimonialMedia->sort_order)
            ->orderBy('sort_order')
            ->first();

        if ($other) {
            $this->swapSortOrder($testimonialMedia, $other);
            $this->normalizeSortOrders();
        }

        return back()->with('success', 'Urutan testimoni berhasil diperbarui.');
    }

    private function validatedData(Request $request, ?TestimonialMedia $current = null): array
    {
        $type = (string) $request->input('type', 'photo');
        $source = $type === 'photo' ? 'upload' : (string) $request->input('source', 'upload');
        $needsMedia = ! $current?->exists
            || $current->type !== $type
            || $current->source !== $source
            || ! $current->media_url;

        $rules = [
            'type' => ['required', Rule::in(['photo', 'video'])],
            'source' => ['nullable', Rule::in(['upload', 'embed'])],
            'media_url' => [
                Rule::requiredIf(fn (): bool => $source === 'embed' && $needsMedia),
                Rule::prohibitedIf(fn (): bool => $source === 'upload'),
                'nullable', 'url', 'max:2048',
            ],
            'is_published' => ['nullable', 'boolean'],
        ];

        $mediaRules = [
            Rule::requiredIf(fn (): bool => $source === 'upload' && $needsMedia),
            Rule::prohibitedIf(fn (): bool => $source === 'embed'),
            'nullable', 'file',
        ];

        if ($type === 'photo') {
            $mediaRules = array_merge($mediaRules, [
                'image', 'mimes:jpg,jpeg,png,webp', new SafeImageUpload,
                'max:'.TestimonialMedia::MAX_PHOTO_KB,
            ]);
        } else {
            $mediaRules = array_merge($mediaRules, [
                'mimetypes:video/mp4,video/webm,video/quicktime',
                'mimes:mp4,webm,mov',
                'max:'.TestimonialMedia::MAX_VIDEO_KB,
            ]);
        }

        $rules['media_file'] = $mediaRules;
        $validated = $request->validate($rules);
        unset($validated['media_file']);

        $validated['type'] = $type;
        $validated['source'] = $source;
        $validated['is_published'] = $request->boolean('is_published');

        return $validated;
    }

    private function applyMedia(Request $request, array $data, ?TestimonialMedia $current = null): array
    {
        if ($data['source'] === 'embed') {
            $url = TestimonialVideoUrl::normalize((string) ($data['media_url'] ?? ''));

            if ($url === null) {
                throw ValidationException::withMessages([
                    'media_url' => 'URL video belum didukung. Gunakan YouTube, Vimeo, TikTok, Instagram, atau Facebook Reel/Watch.',
                ]);
            }

            $data['media_url'] = $url;

            return $data;
        }

        unset($data['media_url']);

        if ($request->hasFile('media_file')) {
            $folder = $data['type'] === 'photo' ? 'testimonials/photos' : 'testimonials/videos';
            $path = $request->file('media_file')->store($folder, 'public');

            if (! is_string($path) || $path === '') {
                throw ValidationException::withMessages(['media_file' => 'Media gagal disimpan.']);
            }

            $data['media_url'] = Storage::url($path);
        } elseif ($current?->exists && $current->source === 'upload') {
            $data['media_url'] = $current->media_url;
        }

        return $data;
    }

    private function nextSortOrder(): int
    {
        return min(((int) TestimonialMedia::query()->max('sort_order')) + 1, TestimonialMedia::MAX_ITEMS);
    }

    private function normalizeSortOrders(): void
    {
        TestimonialMedia::query()->ordered()->get()->values()
            ->each(function (TestimonialMedia $item, int $index): void {
                $expected = $index + 1;
                if ($item->sort_order !== $expected) {
                    $item->forceFill(['sort_order' => $expected])->save();
                }
            });
    }

    private function swapSortOrder(TestimonialMedia $first, TestimonialMedia $second): void
    {
        $firstOrder = $first->sort_order;
        $first->forceFill(['sort_order' => $second->sort_order])->save();
        $second->forceFill(['sort_order' => $firstOrder])->save();
    }
}
