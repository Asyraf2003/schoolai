<?php
/* REAL_GALLERY_CRUD_MODEL_FINAL */

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class GalleryItem extends Model
{
    use HasFactory, SoftDeletes;

    public const MAX_ITEMS = 6;
    public const MAX_PHOTO_KB = 10240;

    protected $fillable = [
        'title',
        'title_id',
        'title_en',
        'type',
        'category',
        'category_id',
        'category_en',
        'caption',
        'caption_id',
        'caption_en',
        'media_url',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

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
        return $locale === 'en'
            ? $this->firstFilled($this->title_en, $this->title_id, $this->title, 'Untitled gallery')
            : $this->firstFilled($this->title_id, $this->title, $this->title_en, 'Galeri tanpa judul');
    }

    public function categoryForLocale(string $locale): string
    {
        return $locale === 'en'
            ? $this->firstFilled($this->category_en, $this->category_id, $this->category, 'General')
            : $this->firstFilled($this->category_id, $this->category, $this->category_en, 'Umum');
    }

    public function captionForLocale(string $locale): string
    {
        return $locale === 'en'
            ? $this->firstFilled($this->caption_en, $this->caption_id, $this->caption, '')
            : $this->firstFilled($this->caption_id, $this->caption, $this->caption_en, '');
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

    public function replacementIdentity(): ?string
    {
        $normalizedMedia = self::normalizeMediaIdentity($this->media_url);

        if ($normalizedMedia === null) {
            return null;
        }

        return strtolower(trim((string) $this->type)) . '|' . $normalizedMedia;
    }

    public static function normalizeMediaIdentity(?string $mediaUrl): ?string
    {
        if (! is_string($mediaUrl)) {
            return null;
        }

        $mediaUrl = trim($mediaUrl);

        if ($mediaUrl === '') {
            return null;
        }

        if (str_starts_with($mediaUrl, '/storage/')) {
            return 'local:' . rtrim($mediaUrl, '/');
        }

        if (! filter_var($mediaUrl, FILTER_VALIDATE_URL)) {
            return 'path:' . rtrim($mediaUrl, '/');
        }

        $scheme = strtolower((string) parse_url($mediaUrl, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($mediaUrl, PHP_URL_HOST));
        $port = parse_url($mediaUrl, PHP_URL_PORT);
        $path = '/' . ltrim((string) parse_url($mediaUrl, PHP_URL_PATH), '/');
        $path = $path === '/' ? '/' : rtrim($path, '/');
        parse_str((string) parse_url($mediaUrl, PHP_URL_QUERY), $query);
        ksort($query);

        $defaultPort = ($scheme === 'https' && $port === 443) || ($scheme === 'http' && $port === 80);
        $authority = $host . (($port && ! $defaultPort) ? ':' . $port : '');
        $queryString = $query === [] ? '' : '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);

        return $scheme . '://' . $authority . $path . $queryString;
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
