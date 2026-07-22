<?php

namespace App\Models;

use App\Models\Concerns\AuditsAdminChanges;
use App\Models\Concerns\ResolvesLocalizedContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class HeroSlide extends Model
{
    use AuditsAdminChanges, HasFactory, ResolvesLocalizedContent;

    protected $fillable = [
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
}
