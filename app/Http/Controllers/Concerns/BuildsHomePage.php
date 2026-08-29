<?php

namespace App\Http\Controllers\Concerns;

use App\Support\PublicUrl;
use Illuminate\Contracts\View\View;

trait BuildsHomePage
{
    public function __invoke(): View
    {
        $home = $this->homeData();

        return view('welcome', [
            'meta' => $home['meta'] ?? [],
            'hero' => $this->heroData(),
            'navbar' => $this->navbarData(),
            'visiMisi' => $home['visi_misi'] ?? [],
            'schoolValues' => $home['nilai_sekolah'] ?? [],
            'featuredPrograms' => $home['program_unggulan'] ?? [],
            'gallerySection' => $this->gallerySectionData(),
            'testimonialSection' => $this->testimonialSectionData(),
            'footerSection' => $home['footer'] ?? [],
        ]);
    }

    private function homeData(): array
    {
        $base = __('home');
        $parity = __('home_parity');
        $base = is_array($base) ? $base : [];
        $parity = is_array($parity) ? $parity : [];

        return $this->canonicalizeHomeMedia(array_replace_recursive($base, $parity));
    }

    /** @param array<string, mixed> $home */
    private function canonicalizeHomeMedia(array $home): array
    {
        $hero = is_array($home['hero'] ?? null) ? $home['hero'] : [];
        $slides = is_array($hero['slides'] ?? null) ? $hero['slides'] : [];
        $imageKeys = ['hero_school', 'fullday', 'aula'];
        $imageIndex = 0;

        foreach ($slides as &$slide) {
            if (! is_array($slide)) {
                continue;
            }

            if (($slide['type'] ?? null) === 'video') {
                $slide['media'] = config('media.homepage_hero_video_url');
                $slide['poster'] = config('media.static.hero_school');
            } else {
                $key = $imageKeys[min($imageIndex, count($imageKeys) - 1)];
                $slide['media'] = $key === 'hero_school'
                    ? config('media.static.hero_school')
                    : config('media.static.school_life.'.$key);
                $slide['poster'] = null;
                $imageIndex++;
            }

            $slide['source'] = ['name' => 'Al Mustaqbal School', 'url' => null];
        }
        unset($slide);

        $hero['slides'] = $slides;
        $hero['fallback_image'] = config('media.static.hero_school');
        $home['hero'] = $hero;

        $gallery = is_array($home['galeri'] ?? null) ? $home['galeri'] : [];
        $items = is_array($gallery['items'] ?? null) ? $gallery['items'] : [];
        $keys = [
            'labit', 'mushalla', 'aula', 'renang', 'perpustakaan',
            'psikolog', 'parenting', 'gigianak', 'fullday',
        ];

        foreach ($items as $index => &$item) {
            if (is_array($item) && isset($keys[$index])) {
                $item['thumbnail_url'] = config('media.static.school_life.'.$keys[$index]);
            }
        }
        unset($item);

        $gallery['items'] = $items;
        $home['galeri'] = $gallery;

        return $home;
    }

    private function homeSection(string $key): array
    {
        $section = $this->homeData()[$key] ?? [];

        return is_array($section) ? $section : [];
    }

    private function publicAssetUrl(mixed $path): ?string
    {
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        $path = trim($path);

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return PublicUrl::normalize($path);
        }

        $relativePath = ltrim($path, '/');

        if (str_starts_with($relativePath, 'storage/')) {
            return asset($relativePath);
        }

        if (! file_exists(public_path($relativePath))) {
            return null;
        }

        return asset($relativePath);
    }
}
