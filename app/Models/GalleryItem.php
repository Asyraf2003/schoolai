<?php
/* REAL_GALLERY_CRUD_MODEL_FINAL */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

final class GalleryItem extends Model
{
    use HasFactory;

    public const MAX_ITEMS = 6;
    public const MAX_VIDEO_SECONDS = 180;

    protected $fillable = [
        'title',
        'type',
        'category',
        'caption',
        'thumbnail_url',
        'media_url',
        'duration_seconds',
        'sort_order',
        'is_published',
        'fallback_icon',
        'accent',
        'published_at',
    ];

    protected $casts = [
        'duration_seconds' => 'integer',
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

    public function getIsVideoAttribute(): bool
    {
        return in_array($this->type, ['video', 'reel'], true);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'video' => 'Video',
            'reel' => 'Reel',
            default => 'Foto',
        };
    }

    public function getDurationLabelAttribute(): string
    {
        if (! $this->is_video || ! $this->duration_seconds) {
            return '-';
        }

        $minutes = intdiv($this->duration_seconds, 60);
        $seconds = $this->duration_seconds % 60;

        return sprintf('%d:%02d', $minutes, $seconds);
    }
}
