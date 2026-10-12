<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class InstagramGalleryItem extends Model
{
    protected $fillable = [
        'instagram_id', 'media_type', 'caption', 'media_url',
        'thumbnail_url', 'permalink', 'posted_at', 'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'posted_at' => 'datetime',
            'synced_at' => 'datetime',
        ];
    }

    public function getIsVideoAttribute(): bool
    {
        return $this->media_type === 'VIDEO';
    }
}
