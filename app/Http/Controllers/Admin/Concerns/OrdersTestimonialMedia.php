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

trait OrdersTestimonialMedia
{
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
}
