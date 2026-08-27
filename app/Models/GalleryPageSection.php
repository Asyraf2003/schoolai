<?php

/* GALLERY_PAGE_SECTION_MODEL_FINAL */

namespace App\Models;

use App\Models\Concerns\AuditsAdminChanges;
use App\Models\Concerns\ResolvesLocalizedContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class GalleryPageSection extends Model
{
    use AuditsAdminChanges, HasFactory, ResolvesLocalizedContent, SoftDeletes;

    protected $fillable = [
        'title_id',
        'title_en',
        'title_ar',
        'description_id',
        'description_en',
        'description_ar',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(GalleryItem::class)
            ->withPivot(['sort_order', 'is_published'])
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function legacyMediaItems(): HasMany
    {
        return $this->hasMany(GalleryPageMediaItem::class);
    }

    public function legacyMediaItemsWithTrashed(): HasMany
    {
        return $this->hasMany(GalleryPageMediaItem::class)->withTrashed();
    }

    public function getAdminTitleAttribute(): string
    {
        return $this->firstFilled($this->title_id, $this->title_en, 'Bagian tanpa judul');
    }

    public function getAdminDescriptionAttribute(): string
    {
        return $this->firstFilled($this->description_id, $this->description_en, '');
    }

    public function titleForLocale(string $locale): string
    {
        return $this->localizedValue(
            $locale,
            $this->title_id,
            $this->title_en,
            $this->title_ar,
            'Bagian tanpa judul',
            'Untitled section',
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

    public function replacementIdentity(): ?string
    {
        return self::normalizeTitleIdentity($this->title_id);
    }

    public static function normalizeTitleIdentity(?string $title): ?string
    {
        if (! is_string($title)) {
            return null;
        }

        $title = trim($title);

        if ($title === '') {
            return null;
        }

        $title = preg_replace('/\s+/u', ' ', $title) ?? $title;

        return mb_strtolower($title, 'UTF-8');
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
