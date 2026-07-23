<?php

namespace App\Models;

use App\Models\Concerns\AuditsAdminChanges;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class TestimonialMedia extends Model
{
    use AuditsAdminChanges, HasFactory, SoftDeletes;

    public const MAX_ITEMS = 9;
    public const MAX_PHOTO_KB = 10240;
    public const MAX_VIDEO_KB = 102400;

    protected $table = 'testimonial_media';

    protected $fillable = [
        'type',
        'source',
        'media_url',
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_published' => 'boolean',
    ];

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('sort_order')
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

    public function getIsEmbedAttribute(): bool
    {
        return $this->source === 'embed';
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->is_video ? 'Video' : 'Foto';
    }

    public function getSourceLabelAttribute(): string
    {
        return $this->is_embed ? 'Embed' : 'Upload';
    }

    public function getMediaLabelAttribute(): string
    {
        $host = parse_url((string) $this->media_url, PHP_URL_HOST);

        if (is_string($host) && $host !== '') {
            return $host;
        }

        $path = parse_url((string) $this->media_url, PHP_URL_PATH);

        return basename(is_string($path) ? $path : (string) $this->media_url);
    }
}
