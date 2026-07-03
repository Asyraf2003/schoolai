<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

final class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('welcome', [
            'meta' => __('home.meta'),
            'hero' => $this->heroData(),
            'navbar' => __('home.navbar'),
            'stats' => __('home.stats.items'),
            'quickInfo' => __('home.quick_info.items'),
            'ppdb' => __('home.ppdb'),
            'visiMisi' => __('home.visi_misi'),
            'schoolValues' => __('home.nilai_sekolah'),
            'featuredPrograms' => __('home.program_unggulan'),
            'extracurricular' => __('home.ekstrakurikuler'),
            'gallerySection' => __('home.galeri'),
            'articlesSection' => __('home.artikel'),
            'announcementsSection' => __('home.pengumuman'),
            'facilitiesSection' => __('home.fasilitas'),
            'contactSection' => __('home.kontak'),
            'footerSection' => __('home.footer'),
        ]);
    }

    /**
     * Menyiapkan data hero dari file bahasa dan hanya mengaktifkan URL gambar
     * jika file fisiknya memang tersedia di public/.
     */
    private function heroData(): array
    {
        $hero = __('home.hero');

        if (! is_array($hero)) {
            return [];
        }

        $hero['background_image_url'] = $this->publicAssetUrl($hero['background_image'] ?? null);
        $hero['visual_image_url'] = $this->publicAssetUrl($hero['visual_image'] ?? null);

        $hero['badges'] = array_map(function (array $badge): array {
            $badge['image_url'] = $this->publicAssetUrl($badge['image'] ?? null);

            return $badge;
        }, $hero['badges'] ?? []);

        return $hero;
    }

    private function publicAssetUrl(mixed $path): ?string
    {
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        $relativePath = ltrim($path, '/');

        if (! file_exists(public_path($relativePath))) {
            return null;
        }

        return asset($relativePath);
    }
}
