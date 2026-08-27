<?php

/* REAL_GALLERY_CRUD_MODEL_FINAL */

namespace App\Models;

use App\Models\Concerns\AuditsAdminChanges;
use App\Models\Concerns\ResolvesLocalizedContent;
use App\Support\Media\MediaReferenceIdentity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class GalleryItem extends Model
{
    use AuditsAdminChanges, HasFactory, ResolvesLocalizedContent, SoftDeletes;

    public const MAX_HOMEPAGE_ITEMS = 6;

    public const MAX_ITEMS = self::MAX_HOMEPAGE_ITEMS;

    public const MAX_PHOTO_KB = 10240;

    protected $fillable = [
        'title',
        'title_id',
        'title_en',
        'title_ar',
        'type',
        'category',
        'category_id',
        'category_en',
        'category_ar',
        'caption',
        'caption_id',
        'caption_en',
        'caption_ar',
        'media_url',
        'is_published',
        'show_on_homepage',
        'show_on_gallery_page',
        'published_at',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_published' => 'boolean',
        'show_on_homepage' => 'boolean',
        'show_on_gallery_page' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(GalleryPageSection::class)
            ->withPivot(['sort_order', 'is_published'])
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function scopeHomepage(Builder $query): Builder
    {
        return $query->where('show_on_homepage', true);
    }

    public function scopeGalleryPage(Builder $query): Builder
    {
        return $query->where('show_on_gallery_page', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->orderBy('id');
    }

    public function getIsPhotoAttribute(): bool
    {
        return $this->type === 'photo';
    }

    public function getIsVideoAttribute(): bool
    {
        return $this->type === 'video';
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->is_video ? 'Video' : 'Foto';
    }

    public function getAdminTitleAttribute(): string
    {
        return $this->firstFilled($this->title_id, $this->title, $this->title_en, 'Galeri tanpa judul');
    }

    public function getAdminCategoryAttribute(): string
    {
        return $this->firstFilled($this->category_id, $this->category, $this->category_en, 'Umum');
    }

    public function getAdminCaptionAttribute(): string
    {
        return $this->firstFilled($this->caption_id, $this->caption, $this->caption_en, '');
    }

    public function titleForLocale(string $locale): string
    {
        return $this->localizedValue(
            $locale,
            $this->firstFilled($this->title_id, $this->title),
            $this->title_en,
            $this->title_ar,
            'Galeri tanpa judul',
            'Untitled gallery',
        );
    }

    public function categoryForLocale(string $locale): string
    {
        return $this->localizedValue(
            $locale,
            $this->firstFilled($this->category_id, $this->category),
            $this->category_en,
            $this->category_ar,
            'Umum',
            'General',
        );
    }

    public function captionForLocale(string $locale): string
    {
        return $this->localizedValue(
            $locale,
            $this->firstFilled($this->caption_id, $this->caption),
            $this->caption_en,
            $this->caption_ar,
        );
    }

    public function typeLabelForLocale(string $locale): string
    {
        return trans(
            $this->is_video ? 'pages.common.media_video' : 'pages.common.media_photo',
            [],
            $locale,
        );
    }

    public function getMediaLabelAttribute(): string
    {
        if (! $this->media_url) {
            return '-';
        }

        $host = parse_url($this->media_url, PHP_URL_HOST);

        if (is_string($host) && $host !== '') {
            return $host;
        }

        $path = parse_url($this->media_url, PHP_URL_PATH);

        return basename(is_string($path) ? $path : $this->media_url);
    }

    public function replacementIdentity(): ?string
    {
        $normalizedMedia = MediaReferenceIdentity::normalize($this->media_url);

        if ($normalizedMedia === null) {
            return null;
        }

        return strtolower(trim((string) $this->type)).'|'.$normalizedMedia;
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
