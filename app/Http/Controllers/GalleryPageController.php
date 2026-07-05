<?php
/* PUBLIC_GALERI_WALL_CONTROLLER_FINAL */

namespace App\Http\Controllers;

use App\Models\GalleryItem;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;

final class GalleryPageController extends Controller
{
    public function __invoke(): View
    {
        $page = __('pages.galeri');

        return view('pages.galeri', [
            'page' => is_array($page) ? $page : [],
            'galleryItems' => $this->galleryItems(),
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
            ->ordered()
            ->get()
            ->map(fn (GalleryItem $item): array => [
                'title' => $item->titleForLocale($locale),
                'type' => $item->type === 'video' ? 'video' : 'photo',
                'media_url' => $this->mediaUrl($item->media_url),
                'emoji' => $item->type === 'video' ? '▶️' : '📸',
                'badge' => $item->type === 'video' ? 'Video' : ($locale === 'en' ? 'Photo' : 'Foto'),
            ])
            ->filter(fn (array $item): bool => trim((string) ($item['title'] ?? '')) !== '')
            ->values()
            ->all();
    }

    private function mediaUrl(?string $url): ?string
    {
        if (! is_string($url) || trim($url) === '') {
            return null;
        }

        $url = trim($url);

        if (str_starts_with($url, '/storage/')) {
            return $url;
        }

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https'], true) ? $url : null;
    }
}
