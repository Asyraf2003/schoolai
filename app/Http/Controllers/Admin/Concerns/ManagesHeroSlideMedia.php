<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\HeroSlide;
use App\Rules\SafeImageUpload;
use App\Support\HeroVideoUrl;
use App\Support\PublicUrl;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

trait ManagesHeroSlideMedia
{
    /** @return Collection<int, Article> */
    private function articleOptions(): Collection
    {
        return Article::query()
            ->latestPublished()
            ->limit(100)
            ->get();
    }

    /** @param array<string, mixed> $data
     *  @return array{0: array<string, mixed>, 1: array{media: ?string, poster: ?string}}
     */
    private function applyMedia(Request $request, array $data, ?HeroSlide $heroSlide = null): array
    {
        $storedPaths = ['media' => null, 'poster' => null];

        if ($request->hasFile('media_file')) {
            $path = $request->file('media_file')->store('hero/slides', 'public');
            $storedPaths['media'] = $path;
            $data['media_url'] = '/storage/'.$path;
        } elseif (! array_key_exists('media_url', $data) && $heroSlide) {
            $data['media_url'] = $heroSlide->media_url;
        }

        if ($request->hasFile('poster_file')) {
            $path = $request->file('poster_file')->store('hero/posters', 'public');
            $storedPaths['poster'] = $path;
            $data['poster_url'] = '/storage/'.$path;
        }

        return [$data, $storedPaths];
    }

    private function normalizeMediaUrl(string $url, string $field): string
    {
        if (HeroVideoUrl::isYoutubeAsset($url)) {
            throw ValidationException::withMessages([
                $field => 'Media YouTube tidak didukung pada hero. Gunakan gambar atau video native.',
            ]);
        }

        if (str_starts_with($url, '/storage/')) {
            return $url;
        }

        $relativePath = ltrim($url, '/');

        if (
            $relativePath !== ''
            && ! str_contains($relativePath, '..')
            && preg_match('/^[A-Za-z0-9_\/. -]+$/', $relativePath) === 1
            && file_exists(public_path($relativePath))
        ) {
            return $relativePath;
        }

        $normalized = PublicUrl::normalize($url);

        if ($normalized === null || strtolower((string) parse_url($normalized, PHP_URL_SCHEME)) !== 'https') {
            throw ValidationException::withMessages([
                $field => 'URL media harus menggunakan HTTPS publik yang valid.',
            ]);
        }

        return $normalized;
    }

    private function normalizeVideoUrl(string $url): ?string
    {
        if ($url === '' || ! HeroVideoUrl::isDirectVideo($url)) {
            return null;
        }

        if (str_starts_with($url, '/storage/')) {
            return $url;
        }

        $relativePath = ltrim($url, '/');

        if (
            ! str_contains($relativePath, '..')
            && preg_match('/^[A-Za-z0-9_\/. -]+$/', $relativePath) === 1
            && file_exists(public_path($relativePath))
        ) {
            return $relativePath;
        }

        return HeroVideoUrl::normalize($url);
    }
}
