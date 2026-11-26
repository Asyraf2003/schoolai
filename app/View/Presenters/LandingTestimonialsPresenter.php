<?php

namespace App\View\Presenters;

final class LandingTestimonialsPresenter
{
    /** @return array<string, mixed> */
    public function present(): array
    {
        $section = __('testimonials');
        $photos = config('media.static.testimonials', []);
        $cards = [];

        foreach ($section['rows'] as $row) {
            foreach ($row['cards'] as $card) {
                $card['avatar_url'] = $photos ? $photos[count($cards) % count($photos)] : null;
                $card['source_url'] = $this->instagramUrl($card['source_url'] ?? null);
                $card['source_type'] = $card['source_type'] ?? null;
                $card['source_label'] = $section['source_types'][$card['source_type']] ?? $section['source_types']['sample'];
                $cards[] = $card;
            }
        }

        return [
            'title' => $section['title'],
            'description' => $section['description'],
            'rows' => array_chunk($cards, max(1, (int) ceil(count($cards) / 2))),
        ];
    }

    private function instagramUrl(?string $url): ?string
    {
        $url = trim($url ?? '');
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $parts = parse_url($url);

        if (
            strtolower($parts['scheme'] ?? '') !== 'https' ||
            ! in_array(strtolower($parts['host'] ?? ''), ['instagram.com', 'www.instagram.com'], true) ||
            ($parts['port'] ?? 443) !== 443 ||
            isset($parts['user']) ||
            isset($parts['pass']) ||
            ! preg_match('~^/(?:(?:p|reel|tv|share)/[\w-]+|stories/[\w.-]+/[\w-]+)/?$~D', $parts['path'] ?? '')
        ) {
            return null;
        }

        return $url;
    }
}
