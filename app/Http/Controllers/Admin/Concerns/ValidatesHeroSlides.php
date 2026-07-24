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

trait ValidatesHeroSlides
{
    /** @return array<string, mixed> */
    private function validatedData(Request $request, ?HeroSlide $heroSlide = null): array
    {
        $type = (string) $request->input('type', 'image');
        $mediaFileRules = $type === 'video'
            ? ['nullable', 'file', 'mimes:mp4,webm,ogg,ogv', 'max:'.self::MAX_VIDEO_KB]
            : ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', new SafeImageUpload, 'max:'.self::MAX_IMAGE_KB];

        $data = $request->validate([
            'article_id' => [
                'required',
                'integer',
                Rule::exists('articles', 'id')->whereNull('deleted_at'),
                Rule::unique('hero_slides', 'article_id')->ignore($heroSlide?->getKey()),
            ],
            'type' => ['required', Rule::in(['image', 'video'])],
            'media_file' => $mediaFileRules,
            'media_url' => ['nullable', 'string', 'max:2048'],
            'poster_file' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', new SafeImageUpload, 'max:'.self::MAX_IMAGE_KB],
            'poster_url' => ['nullable', 'string', 'max:2048'],
            'media_alt_id' => ['nullable', 'string', 'max:255'],
            'media_alt_en' => ['nullable', 'string', 'max:255'],
            'media_alt_ar' => ['nullable', 'string', 'max:255'],
            'eyebrow_id' => ['nullable', 'string', 'max:160'],
            'eyebrow_en' => ['nullable', 'string', 'max:160'],
            'eyebrow_ar' => ['nullable', 'string', 'max:160'],
            'title_id' => ['nullable', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'description_id' => ['nullable', 'string', 'max:2000'],
            'description_en' => ['nullable', 'string', 'max:2000'],
            'description_ar' => ['nullable', 'string', 'max:2000'],
            'cta_label_id' => ['nullable', 'string', 'max:160'],
            'cta_label_en' => ['nullable', 'string', 'max:160'],
            'cta_label_ar' => ['nullable', 'string', 'max:160'],
            'cta_url' => ['nullable', 'string', 'max:2048'],
            'cta_action' => ['nullable', Rule::in(['anchor', 'link', 'admission'])],
            'focal_position' => ['nullable', 'string', 'max:60', 'regex:/^[a-z0-9%.\s-]+$/i'],
            'overlay_strength' => ['nullable', 'numeric', 'min:0.28', 'max:0.88'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['focal_position'] = trim((string) ($data['focal_position'] ?? '')) ?: 'center center';
        $data['overlay_strength'] = round((float) ($data['overlay_strength'] ?? 0.46), 2);
        $data['cta_action'] = $data['cta_action'] ?? null;
        $data['cta_url'] = $this->normalizeHeroLink($data['cta_url'] ?? null);
        $article = Article::query()->findOrFail((int) $data['article_id']);

        if (! $article->isPubliclyVisibleNow()) {
            throw ValidationException::withMessages([
                'article_id' => 'Pilih artikel yang sudah terbit dan dapat dibaca publik.',
            ]);
        }

        $data = array_replace($data, [
            'title_id' => $article->title_id ?: $article->admin_title,
            'title_en' => $article->title_en,
            'title_ar' => $article->title_ar,
            'description_id' => $article->description_id,
            'description_en' => $article->description_en,
            'description_ar' => $article->description_ar,
            'media_alt_id' => $article->title_id ?: $article->admin_title,
            'media_alt_en' => $article->title_en,
            'media_alt_ar' => $article->title_ar,
            'eyebrow_id' => 'Artikel Pilihan',
            'eyebrow_en' => 'Featured Story',
            'eyebrow_ar' => 'مقال مميز',
            'cta_label_id' => 'Baca Artikel',
            'cta_label_en' => 'Read Article',
            'cta_label_ar' => 'اقرأ المقال',
            'cta_url' => $article->link_id,
            'cta_action' => 'link',
        ]);

        if ($type === 'video' && ! $request->hasFile('media_file')) {
            $candidate = trim((string) ($data['media_url'] ?? ''));

            if ($candidate === '') {
                $candidate = trim((string) ($heroSlide?->media_url ?? ''));
            }

            $normalizedVideo = $this->normalizeVideoUrl($candidate);

            if ($normalizedVideo === null) {
                throw ValidationException::withMessages([
                    'media_url' => 'Upload file MP4/WebM/OGG atau gunakan URL HTTPS yang langsung berakhir dengan ekstensi video. Link YouTube tidak didukung pada hero.',
                ]);
            }

            $data['media_url'] = $normalizedVideo;
        } elseif (! $request->hasFile('media_file')) {
            $candidate = trim((string) ($data['media_url'] ?? ''));

            if ($candidate === '') {
                $candidate = trim((string) ($heroSlide?->media_url ?? ''));
            }

            if (HeroVideoUrl::isYoutubeAsset($candidate)) {
                $candidate = trim((string) $article->thumbnail_url);
            }

            if ($candidate === '') {
                $candidate = trim((string) $article->thumbnail_url);
            }

            if ($candidate === '') {
                throw ValidationException::withMessages([
                    'media_file' => 'Artikel belum memiliki thumbnail. Upload gambar hero atau isi URL gambar publik.',
                ]);
            }

            $data['media_url'] = $this->normalizeMediaUrl($candidate, 'media_url');
        }

        if (! $request->hasFile('poster_file')) {
            $poster = trim((string) ($data['poster_url'] ?? ''));
            $data['poster_url'] = $poster === '' || HeroVideoUrl::isYoutubeAsset($poster)
                ? $article->thumbnail_url
                : $this->normalizeMediaUrl($poster, 'poster_url');
        }

        unset($data['media_file'], $data['poster_file']);

        return $data;
    }
}
