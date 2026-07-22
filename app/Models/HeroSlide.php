<?php

namespace App\Models;

use App\Models\Concerns\AuditsAdminChanges;
use App\Models\Concerns\ResolvesLocalizedContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class HeroSlide extends Model
{
    use AuditsAdminChanges, HasFactory, ResolvesLocalizedContent;

    protected $fillable = [
        'article_id',
        'type',
        'media_url',
        'poster_url',
        'media_alt_id',
        'media_alt_en',
        'media_alt_ar',
        'eyebrow_id',
        'eyebrow_en',
        'eyebrow_ar',
        'title_id',
        'title_en',
        'title_ar',
        'description_id',
        'description_en',
        'description_ar',
        'cta_label_id',
        'cta_label_en',
        'cta_label_ar',
        'cta_url',
        'cta_action',
        'focal_position',
        'overlay_strength',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'overlay_strength' => 'float',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActiveOrdered(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function titleForLocale(string $locale): string
    {
        return $this->localizedValue($locale, $this->title_id, $this->title_en, $this->title_ar);
    }

    public function eyebrowForLocale(string $locale): string
    {
        return $this->localizedValue($locale, $this->eyebrow_id, $this->eyebrow_en, $this->eyebrow_ar);
    }

    public function descriptionForLocale(string $locale): string
    {
        return $this->localizedValue($locale, $this->description_id, $this->description_en, $this->description_ar);
    }

    public function mediaAltForLocale(string $locale): string
    {
        return $this->localizedValue(
            $locale,
            $this->media_alt_id,
            $this->media_alt_en,
            $this->media_alt_ar,
            'Al Mustaqbal School',
            'Al Mustaqbal School',
        );
    }

    public function ctaLabelForLocale(string $locale): string
    {
        return $this->localizedValue($locale, $this->cta_label_id, $this->cta_label_en, $this->cta_label_ar);
    }

    public function getAdminTitleAttribute(): string
    {
        if ($this->article instanceof Article) {
            return $this->article->admin_title;
        }

        foreach ([$this->title_id, $this->title_en, $this->title_ar] as $value) {
            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return 'Hero tanpa judul';
    }

    /** @return array<string, mixed> */
    public function toHeroArray(string $locale): array
    {
        if ($this->article instanceof Article && $this->article->isPubliclyVisibleNow()) {
            $article = $this->article;
            $articleTitle = $article->titleForLocale($locale);
            $articleDescription = $article->descriptionForLocale($locale);
            $articleTag = collect($article->tags ?? [])
                ->first(fn (mixed $tag): bool => is_string($tag) && trim($tag) !== '');

            return [
                'type' => $this->type,
                'media' => $this->media_url ?: $article->thumbnail_url,
                'poster' => $this->poster_url ?: $article->thumbnail_url,
                'media_alt' => $articleTitle,
                'eyebrow' => is_string($articleTag) && trim($articleTag) !== ''
                    ? trim($articleTag)
                    : $this->articleEyebrow($locale),
                'title' => $articleTitle,
                'description' => $articleDescription,
                'cta' => [
                    'label' => $this->articleCtaLabel($locale),
                    'href' => $article->linkForLocale($locale),
                    'action' => 'link',
                ],
                'focal_position' => $this->focal_position ?: 'center center',
                'overlay_strength' => $this->overlay_strength ?? 0.46,
                'article_id' => $article->getKey(),
            ];
        }

        return [
            'type' => $this->type,
            'media' => $this->media_url,
            'poster' => $this->poster_url,
            'media_alt' => $this->mediaAltForLocale($locale),
            'eyebrow' => $this->eyebrowForLocale($locale),
            'title' => $this->titleForLocale($locale),
            'description' => $this->descriptionForLocale($locale),
            'cta' => [
                'label' => $this->ctaLabelForLocale($locale),
                'href' => $this->cta_url,
                'action' => $this->cta_action,
            ],
            'focal_position' => $this->focal_position ?: 'center center',
            'overlay_strength' => $this->overlay_strength ?? 0.46,
        ];
    }

    private function articleEyebrow(string $locale): string
    {
        return match ($locale) {
            'ar' => 'مقال مميز',
            'en' => 'Featured story',
            default => 'Artikel Pilihan',
        };
    }

    private function articleCtaLabel(string $locale): string
    {
        return match ($locale) {
            'ar' => 'اقرأ المقال',
            'en' => 'Read article',
            default => 'Baca Artikel',
        };
    }
}
