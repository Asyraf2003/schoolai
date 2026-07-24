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

trait StoresTestimonialMedia
{
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
