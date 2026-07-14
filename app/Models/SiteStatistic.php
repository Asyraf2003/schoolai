<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class SiteStatistic extends Model
{
    use HasFactory, SoftDeletes;

    public const MAX_ITEMS = 4;

    protected $fillable = [
        'value',
        'value_en',
        'label',
        'label_en',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function valueForLocale(string $locale): string
    {
        return $locale === 'en'
            ? $this->firstFilled($this->value_en, $this->value)
            : $this->firstFilled($this->value, $this->value_en);
    }

    public function labelForLocale(string $locale): string
    {
        return $locale === 'en'
            ? $this->firstFilled($this->label_en, $this->label)
            : $this->firstFilled($this->label, $this->label_en);
    }

    public function replacementIdentity(): ?string
    {
        $labelId = self::normalizeIdentityText($this->label);
        $labelEn = self::normalizeIdentityText($this->label_en);

        if ($labelId === null || $labelEn === null) {
            return null;
        }

        return $labelId . '|' . $labelEn;
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

    private function firstFilled(mixed ...$values): string
    {
        foreach ($values as $value) {
            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return '';
    }
}
