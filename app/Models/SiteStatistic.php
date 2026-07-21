<?php

namespace App\Models;

use App\Models\Concerns\AuditsAdminChanges;
use App\Models\Concerns\ResolvesLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class SiteStatistic extends Model
{
    use AuditsAdminChanges, HasFactory, ResolvesLocalizedContent, SoftDeletes;

    public const MAX_ITEMS = 4;

    protected $fillable = [
        'value',
        'value_en',
        'value_ar',
        'label',
        'label_en',
        'label_ar',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function valueForLocale(string $locale): string
    {
        return $this->localizedValue(
            $locale,
            $this->value,
            $this->value_en,
            $this->value_ar,
        );
    }

    public function labelForLocale(string $locale): string
    {
        return $this->localizedValue(
            $locale,
            $this->label,
            $this->label_en,
            $this->label_ar,
        );
    }

    public function replacementIdentity(): ?string
    {
        return self::normalizeIdentityText($this->label);
    }

    public static function normalizeIdentityText(?string $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = preg_replace('/\s+/u', ' ', trim($value));

        if (! is_string($value) || $value === '') {
            return null;
        }

        return mb_strtolower($value);
    }
}
