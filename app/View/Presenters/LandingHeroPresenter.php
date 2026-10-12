<?php

namespace App\View\Presenters;

use App\Services\PpdbAccess;

final class LandingHeroPresenter
{
    public function __construct(private PpdbAccess $ppdb) {}

    /** @return array<string, mixed> */
    public function present(): array
    {
        $hero = array_replace_recursive(__('home.hero'), __('home_parity.hero'));
        $fallback = $hero['slides'][0] ?? [];
        $url = $this->ppdb->current()->publicRegistrationUrl();
        $hero['slides'] = [[
            'type' => 'video', 'render_type' => 'video',
            'media_url' => config('media.homepage_hero_video_url'),
            'poster_url' => config('media.static.hero_school'),
            'media_alt' => $fallback['media_alt'] ?? 'Al Mustaqbal School',
            'eyebrow' => $url ? __('runtime.home.ppdb_campaign_eyebrow') : ($fallback['eyebrow'] ?? ''),
            'title' => $url ? __('runtime.home.ppdb_campaign_title') : ($fallback['title'] ?? 'Al Mustaqbal School'),
            'description' => $url ? __('runtime.home.ppdb_campaign_description') : ($fallback['description'] ?? ''),
            'eyebrow_href' => $url, 'title_href' => $url, 'description_href' => $url,
            'campaign_link_label' => $url ? __('runtime.home.ppdb_campaign_link_label') : null,
            'is_ppdb_campaign' => $url !== null, 'is_primary_slide' => true,
            'cta' => ['label' => $url ? __('runtime.home.ppdb_campaign_link_label') : null, 'href' => $url, 'action' => $url ? 'link' : null],
            'focal_position' => 'center center', 'overlay_strength' => 0.34,
            'video_mime_type' => 'video/mp4', 'is_media_fallback' => false, 'is_opening' => true,
        ]];

        return $hero;
    }
}
