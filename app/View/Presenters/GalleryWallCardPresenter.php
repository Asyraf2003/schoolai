<?php

namespace App\View\Presenters;

use Illuminate\Contracts\Translation\Translator;

final class GalleryWallCardPresenter
{
    public function __construct(private Translator $translator) {}

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    public function present(array $item): array
    {
        $type = (string) ($item['type'] ?? 'photo');
        $isVideo = $type === 'video';
        $isDirectVideo = $isVideo && (bool) ($item['is_direct_video'] ?? false);
        $title = (string) ($item['title'] ?? '');
        $label = (string) ($item['label'] ?? $title);
        $mediaUrl = $item['media_url'] ?? null;
        $thumbnailUrl = $item['thumbnail_url'] ?? null;

        if (! is_string($mediaUrl) || trim($mediaUrl) === '') {
            $fallbackKey = $label !== '' ? $label : 'Al Mustaqbal School';
            $fallbackImages = $this->fallbackImages();
            $fallbackIndex = ((int) sprintf('%u', crc32($fallbackKey)))
                % count($fallbackImages);
            $mediaUrl = $fallbackImages[$fallbackIndex];
            $thumbnailUrl = $mediaUrl;
            $type = 'photo';
            $isVideo = false;
            $isDirectVideo = false;
        }

        $gradient = $item['gradient'] ?? ['#DCF1F7', '#FFC93C'];

        return [
            'type' => $type,
            'isVideo' => $isVideo,
            'isDirectVideo' => $isDirectVideo,
            'title' => $title,
            'label' => $label,
            'mediaUrl' => $mediaUrl,
            'thumbnailUrl' => $thumbnailUrl,
            'videoProvider' => (string) ($item['video_provider'] ?? 'video'),
            'videoProviderLogoUrl' => $item['video_provider_logo_url'] ?? null,
            'videoProviderLabel' => $item['video_provider_label']
                ?? $this->translator->get('pages.common.media_video'),
            'emoji' => $item['emoji'] ?? ($isVideo ? '▶️' : '📸'),
            'badge' => $isVideo
                ? ($item['badge'] ?? $this->translator->get('pages.common.media_video'))
                : $this->translator->get('pages.common.media_photo'),
            'gradient' => is_array($gradient)
                ? $gradient
                : ['#DCF1F7', '#FFC93C'],
        ];
    }

    /** @return array<int, string> */
    private function fallbackImages(): array
    {
        return [
            'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1400&q=82',
            'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1400&q=82',
            'https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=1400&q=82',
            'https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=1400&q=82',
            'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=1400&q=82',
            'https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&w=1400&q=82',
        ];
    }
}
