<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\TestimonialMedia;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

trait ManagesTestimonialMedia
{
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
        [$data, $newKey] = $this->applyMedia($request, $data);
        $data['sort_order'] = $this->nextSortOrder();

        try {
            TestimonialMedia::query()->create($data);
        } catch (Throwable $exception) {
            $this->deleteStoredKey($newKey);

            throw $exception;
        }
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
        $oldMediaUrl = $testimonialMedia->media_url;
        $data = $this->validatedData($request, $testimonialMedia);
        [$data, $newKey, $replacesStoredFile] = $this->applyMedia($request, $data, $testimonialMedia);

        try {
            $testimonialMedia->update($data);
        } catch (Throwable $exception) {
            $this->deleteStoredKey($newKey);

            throw $exception;
        }

        if ($replacesStoredFile) {
            $this->deleteStoredFile($oldMediaUrl, $testimonialMedia->getKey());
        }

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
}
