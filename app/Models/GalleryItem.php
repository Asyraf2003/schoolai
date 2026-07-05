<?php
/* REAL_GALLERY_CRUD_MODEL_FINAL */

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class GalleryItem extends Model
{
    use HasFactory;

    public const MAX_ITEMS = 6;
    public const MAX_PHOTO_KB = 10240;

    protected $fillable = [
        'title',
        'type',
        'category',
        'caption',
        'media_url',
        'sort_order',
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

    public function getIsEmbedAttribute(): bool
    {
        return $this->type === 'embed';
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->is_embed ? 'Embed' : 'Foto';
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
}
