<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class SiteStatistic extends Model
{
    use HasFactory;

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
