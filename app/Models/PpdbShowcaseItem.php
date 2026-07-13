<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class PpdbShowcaseItem extends Model
{
    use HasFactory, SoftDeletes;

    public const AUDIENCE_PARENTS = 'parents';
    public const AUDIENCE_SCHOOL = 'school';
    public const MEDIA_PHOTO = 'photo';
    public const MEDIA_VIDEO = 'video';
    public const MAX_PHOTO_KB = 10240;

    public const AUDIENCES = [
        self::AUDIENCE_PARENTS,
        self::AUDIENCE_SCHOOL,
    ];

    public const MEDIA_TYPES = [
        self::MEDIA_PHOTO,
        self::MEDIA_VIDEO,
    ];

    protected $fillable = [
        'audience',
        'title_id',
        'title_en',
        'description_id',
        'description_en',
        'media_type',
        'media_url',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('audience')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function scopeForAudience(Builder $query, string $audience): Builder
    {
        return $query->where('audience', $audience);
    }

    public function getIsPhotoAttribute(): bool
    {
        return $this->media_type === self::MEDIA_PHOTO;
    }

    public function getIsVideoAttribute(): bool
    {
        return $this->media_type === self::MEDIA_VIDEO;
    }

    public function getAdminTitleAttribute(): string
    {
        return $this->firstFilled($this->title_id, $this->title_en, 'Item PPDB tanpa judul');
    }

    public function getAudienceLabelAttribute(): string
    {
        return $this->audience === self::AUDIENCE_SCHOOL
            ? 'Untuk sekolah'
            : 'Untuk orang tua';
    }

    public function getMediaTypeLabelAttribute(): string
    {
        return $this->is_video ? 'Video embed' : 'Foto';
    }

    public function titleForLocale(string $locale): string
    {
        return $locale === 'en'
            ? $this->firstFilled($this->title_en, $this->title_id, 'Untitled admission item')
            : $this->firstFilled($this->title_id, $this->title_en, 'Item PPDB tanpa judul');
    }

    public function descriptionForLocale(string $locale): string
    {
        return $locale === 'en'
            ? $this->firstFilled($this->description_en, $this->description_id, '')
            : $this->firstFilled($this->description_id, $this->description_en, '');
    }

    public function getMediaLabelAttribute(): string
    {
        if (! $this->media_url) {
            return 'Tanpa media';
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
        $title = self::normalizeIdentityText($this->title_id);
        $audience = strtolower(trim((string) $this->audience));

        if ($title === null || ! in_array($audience, self::AUDIENCES, true)) {
            return null;
        }

        return $audience . '|' . $title;
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
