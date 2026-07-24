<?php

namespace App\Providers\Concerns;

use App\Http\Controllers\Admin\HeroSlideAdminController;
use App\Models\Article;
use App\Models\HeroSlide;
use App\Models\PpdbSetting;
use App\Support\HeroVideoUrl;
use App\Support\PublicUrl;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;

trait NormalizesHeroPresentation
{
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

    private function heroLinkUrl(mixed $url): ?string
    {
        if (! is_string($url) || trim($url) === '') {
            return null;
        }

        $url = trim($url);

        if (preg_match('/^#[A-Za-z][A-Za-z0-9_-]*$/', $url) === 1) {
            return $url;
        }

        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return $url;
        }

        return PublicUrl::normalize($url, ['/admin', '/login', '/auth']);
    }

    private function heroFocalPosition(mixed $position): string
    {
        if (! is_string($position)) {
            return 'center center';
        }

        $position = trim($position);

        return preg_match('/^[a-z0-9%.\s-]{1,60}$/i', $position) === 1
            ? $position
            : 'center center';
    }

    private function heroOverlayStrength(mixed $strength): float
    {
        $strength = is_numeric($strength) ? (float) $strength : 0.58;

        return round(min(0.88, max(0.28, $strength)), 2);
    }

    private function heroVideoMimeType(?string $url): string
    {
        $extension = strtolower(pathinfo(
            (string) parse_url((string) $url, PHP_URL_PATH),
            PATHINFO_EXTENSION,
        ));

        return match ($extension) {
            'webm' => 'video/webm',
            'ogv', 'ogg' => 'video/ogg',
            default => 'video/mp4',
        };
    }

    private function currentPpdbSetting(): PpdbSetting
    {
        if (! Schema::hasTable('ppdb_settings')) {
            return new PpdbSetting([
                'registration_url' => PpdbSetting::DEFAULT_REGISTRATION_URL,
                'is_active' => true,
            ]);
        }

        return PpdbSetting::query()->first() ?? new PpdbSetting([
            'registration_url' => PpdbSetting::DEFAULT_REGISTRATION_URL,
            'is_active' => true,
        ]);
    }
}
