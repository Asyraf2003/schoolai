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
            'stats' => $this->statsData(),
            'quickInfo' => $home['quick_info']['items'] ?? [],
            'ppdb' => $home['ppdb'] ?? [],
            'visiMisi' => $home['visi_misi'] ?? [],
            'schoolValues' => $home['nilai_sekolah'] ?? [],
            'featuredPrograms' => $home['program_unggulan'] ?? [],
            'gallerySection' => $this->gallerySectionData(),
            'articlesSection' => $this->articlesSectionData(),
            'footerSection' => $home['footer'] ?? [],
        ]);
    }

    private function homeData(): array
    {
        $base = __('home');
        $parity = __('home_parity');
        $base = is_array($base) ? $base : [];
        $parity = is_array($parity) ? $parity : [];

        return array_replace_recursive($base, $parity);
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
