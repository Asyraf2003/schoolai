<?php

namespace App\Models;

use App\Models\Concerns\AuditsAdminChanges;
use App\Models\Concerns\ResolvesLocalizedContent;
use Illuminate\Database\Eloquent\Model;

final class HeroSetting extends Model
{
    use AuditsAdminChanges, ResolvesLocalizedContent;

    protected $fillable = [
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
    ];

    public function eyebrowForLocale(string $locale): string
    {
        return $this->localizedValue($locale, $this->eyebrow_id, $this->eyebrow_en, $this->eyebrow_ar);
    }

    public function titleForLocale(string $locale): string
    {
        return $this->localizedValue(
            $locale,
            $this->title_id,
            $this->title_en,
            $this->title_ar,
            'Al Mustaqbal School',
            'Al Mustaqbal School',
        );
    }

    public function descriptionForLocale(string $locale): string
    {
        return $this->localizedValue(
            $locale,
            $this->description_id,
            $this->description_en,
            $this->description_ar,
        );
    }

    public function ctaLabelForLocale(string $locale): string
    {
        return $this->localizedValue(
            $locale,
            $this->cta_label_id,
            $this->cta_label_en,
            $this->cta_label_ar,
        );
    }
}
