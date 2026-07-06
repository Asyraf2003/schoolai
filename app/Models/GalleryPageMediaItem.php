<?php
/* GALLERY_PAGE_MEDIA_ITEM_MODEL_FINAL */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class GalleryPageMediaItem extends Model
{
    use HasFactory;

    public const MAX_PHOTO_KB = 10240;

    protected $fillable = [
        'gallery_page_section_id',
        'title_id',
        'title_en',
        'description_id',
        'description_en',
        'type',
        'media_url',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(GalleryPageSection::class, 'gallery_page_section_id');
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
        return $this->type_label . ' halaman galeri';
    }

    public function getAdminDescriptionAttribute(): string
    {
        return $this->media_label;
    }

    public function titleForLocale(string $locale): string
    {
        return '';
    }

    public function descriptionForLocale(string $locale): string
    {
        return '';
    }

    public function typeLabelForLocale(string $locale): string
    {
        if ($this->is_video) {
            return 'Video';
        }

        return $locale === 'en' ? 'Photo' : 'Foto';
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
