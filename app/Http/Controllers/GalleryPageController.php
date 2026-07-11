<?php
/* PUBLIC_GALERI_WALL_CONTROLLER_FINAL */

namespace App\Http\Controllers;

use App\Models\GalleryItem;
use App\Models\GalleryPageMediaItem;
use App\Models\GalleryPageSection;
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
            ->ordered()
            ->get()
            ->map(function (GalleryItem $item) use ($locale): array {
                $type = $item->type === 'video' ? 'video' : 'photo';
                $mediaUrl = $this->trustedMediaUrl($type, $item->media_url);

                return [
                    'title' => $item->titleForLocale($locale),
                    'type' => $type,
                    'media_url' => $mediaUrl,
                    'thumbnail_url' => $type === 'video' ? $this->videoThumbnailUrl($mediaUrl) : $mediaUrl,
                    'emoji' => $type === 'video' ? '▶️' : '📸',
                    'badge' => $type === 'video' ? 'Video' : ($locale === 'en' ? 'Photo' : 'Foto'),
                ];
            })
            ->filter(fn (array $item): bool => trim((string) ($item['title'] ?? '')) !== '')
            ->values()
            ->all();
    }

    private function gallerySections(): array
    {
        if (! Schema::hasTable('gallery_page_sections') || ! Schema::hasTable('gallery_page_media_items')) {
            return [];
        }

        $locale = app()->getLocale();

        return GalleryPageSection::query()
            ->where('is_published', true)
            ->with([
                'mediaItems' => fn ($query) => $query
                    ->where('is_published', true)
                    ->orderByDesc('published_at')
                    ->orderByDesc('id'),
            ])
            ->orderBy('id')
            ->get()
            ->map(function (GalleryPageSection $section) use ($locale): array {
                return [
                    'title' => $section->titleForLocale($locale),
                    'description' => $section->descriptionForLocale($locale),
                    'items' => $section->mediaItems
                        ->map(function (GalleryPageMediaItem $item) use ($section, $locale): array {
                            $type = $item->type === 'video' ? 'video' : 'photo';
                            $mediaUrl = $this->trustedMediaUrl($type, $item->media_url);

                            return [
                                'title' => '',
                                'label' => $section->titleForLocale($locale),
                                'type' => $type,
                                'media_url' => $mediaUrl,
                                'thumbnail_url' => $type === 'video' ? $this->videoThumbnailUrl($mediaUrl) : $mediaUrl,
                                'emoji' => $type === 'video' ? '▶️' : '📸',
                                'badge' => '',
                            ];
                        })
                        ->filter(fn (array $item): bool => (string) ($item['media_url'] ?? '') !== '')
                        ->values()
                        ->all(),
                ];
            })
            ->filter(fn (array $section): bool => ! empty($section['items']))
            ->values()
            ->all();
    }

    private function trustedMediaUrl(string $type, ?string $url): ?string
    {
        if (! is_string($url) || trim($url) === '') {
            return null;
        }

        $url = trim($url);

        if ($type === 'photo') {
            if (str_starts_with($url, '/storage/')) {
                return $url;
            }

            if (! filter_var($url, FILTER_VALIDATE_URL)) {
                return null;
            }

            $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

            return in_array($scheme, ['http', 'https'], true) ? $url : null;
        }

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');

        if ($scheme !== 'https' || $host === '') {
            return null;
        }

        if ($host === 'www.youtube.com' && preg_match('~^embed/[^/?#]+$~', $path)) {
            return $url;
        }

        if ($host === 'www.tiktok.com' && preg_match('~^embed/v2/\d+$~', $path)) {
            return $url;
        }

        if ($host === 'www.instagram.com' && preg_match('~^(p|reel|tv)/[^/]+/embed$~', $path)) {
            return $url;
        }

        if ($host === 'player.vimeo.com' && preg_match('~^video/\d+$~', $path)) {
            return $url;
        }

        if ($host === 'www.facebook.com' && $path === 'plugins/video.php') {
            parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

            $facebookUrl = trim((string) ($query['href'] ?? ''));
            $facebookScheme = strtolower((string) parse_url($facebookUrl, PHP_URL_SCHEME));
            $facebookHost = strtolower((string) parse_url($facebookUrl, PHP_URL_HOST));
            $facebookPath = trim((string) parse_url($facebookUrl, PHP_URL_PATH), '/');

            if (
                $facebookScheme === 'https' &&
                ($facebookHost === 'facebook.com' || str_ends_with($facebookHost, '.facebook.com')) &&
                preg_match('~^reel/\d+$~', $facebookPath)
            ) {
                return $url;
            }
        }

        return null;
    }

    private function videoThumbnailUrl(?string $embedUrl): ?string
    {
        if (! is_string($embedUrl) || trim($embedUrl) === '') {
            return null;
        }

        $embedUrl = trim($embedUrl);
        $host = strtolower((string) parse_url($embedUrl, PHP_URL_HOST));
        $path = trim((string) parse_url($embedUrl, PHP_URL_PATH), '/');

        if ($host === 'www.youtube.com' && preg_match('~^embed/([^/?#]+)$~', $path, $match)) {
            return 'https://i.ytimg.com/vi/' . rawurlencode($match[1]) . '/hqdefault.jpg';
        }

        return null;
    }
}
