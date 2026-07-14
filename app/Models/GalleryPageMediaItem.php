<?php
/* GALLERY_PAGE_MEDIA_ITEM_MODEL_FINAL */

namespace App\Models;

use App\Models\Concerns\AuditsAdminChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class GalleryPageMediaItem extends Model
{
    use AuditsAdminChanges, HasFactory, SoftDeletes;

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

    public function sectionWithTrashed(): BelongsTo
    {
        return $this->belongsTo(GalleryPageSection::class, 'gallery_page_section_id')->withTrashed();
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
}
