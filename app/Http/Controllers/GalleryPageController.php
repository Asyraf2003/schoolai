<?php

/* PUBLIC_GALERI_WALL_CONTROLLER_FINAL */

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\PresentsGalleryMedia;
use App\Models\GalleryItem;
use App\Models\GalleryPageSection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;

final class GalleryPageController extends Controller
{
    use PresentsGalleryMedia;

    public function __invoke(): View
    {
        $page = __('pages.galeri');

        return view('pages.galeri', [
            'page' => is_array($page) ? $page : [],
            'galleryItems' => $this->galleryItems(),
            'gallerySections' => $this->gallerySections(),
        ]);
    }

    private function galleryItems(): array
    {
        if (! Schema::hasTable('gallery_items')) {
            return [];
        }

        $locale = app()->getLocale();

        return GalleryItem::query()
            ->where('is_published', true)
            ->galleryPage()
            ->ordered()
            ->get()
            ->map(fn (GalleryItem $item): array => $this->presentItem($item, $locale))
            ->filter(fn (array $item): bool => trim((string) ($item['title'] ?? '')) !== '')
            ->values()
            ->all();
    }

    private function gallerySections(): array
    {
        if (! Schema::hasTable('gallery_page_sections') || ! Schema::hasTable('gallery_item_gallery_page_section')) {
            return [];
        }

        $locale = app()->getLocale();

        return GalleryPageSection::query()
            ->where('is_published', true)
            ->with([
                'items' => fn ($query) => $query
                    ->where('gallery_items.is_published', true)
                    ->wherePivot('is_published', true),
            ])
            ->orderBy('id')
            ->get()
            ->map(function (GalleryPageSection $section) use ($locale): array {
                return [
                    'id' => $section->id,
                    'anchor' => 'gallery-section-'.$section->id,
                    'title' => $section->titleForLocale($locale),
                    'description' => $section->descriptionForLocale($locale),
                    'items' => $section->items
                        ->map(fn (GalleryItem $item): array => $this->presentItem($item, $locale, true))
                        ->filter(fn (array $item): bool => (string) ($item['media_url'] ?? '') !== '')
                        ->values()
                        ->all(),
                ];
            })
            ->filter(fn (array $section): bool => ! empty($section['items']))
            ->values()
            ->all();
    }
}
