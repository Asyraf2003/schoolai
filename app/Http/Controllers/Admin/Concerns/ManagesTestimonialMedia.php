<?php

namespace App\Http\Controllers\Admin\Concerns;

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
}
