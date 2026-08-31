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
        $schoolLife = array_values(array_filter(
            config('media.static.school_life', []),
            static fn (mixed $url): bool => is_string($url) && trim($url) !== '',
        ));

        if ($schoolLife !== []) {
            return $schoolLife;
        }

        return [(string) config('media.static.hero_school')];
    }
}
