<?php

namespace App\Models;

use App\Models\Concerns\AuditsAdminChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class GalleryItem extends Model
{
    use AuditsAdminChanges, SoftDeletes;

    protected $fillable = ['type', 'media_url', 'thumbnail_url', 'alt_text'];

    public function getIsVideoAttribute(): bool
    {
        return $this->type === 'video';
    }
}
