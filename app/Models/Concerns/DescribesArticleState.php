<?php

namespace App\Models\Concerns;

use App\Models\Concerns\AuditsAdminChanges;
use App\Models\Concerns\ResolvesLocalizedContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

trait DescribesArticleState
{
    public function isNative(): bool
    {
        return $this->article_source === self::SOURCE_NATIVE;
    }

    public function isDraft(): bool
    {
        return $this->isNative() && $this->article_status === self::STATUS_DRAFT;
    }

    public function isPubliclyVisibleNow(): bool
    {
        if ($this->trashed() || $this->published_at === null || $this->published_at->isFuture()) {
            return false;
        }

        if (! $this->isNative()) {
            return true;
        }

        return in_array($this->article_status, [self::STATUS_PUBLISHED, self::STATUS_SCHEDULED], true);
    }

    public function statusLabel(): string
    {
        if ($this->trashed()) {
            return 'Dihapus';
        }

        if (! $this->isNative()) {
            return $this->isPubliclyVisibleNow() ? 'Eksternal' : 'Terjadwal';
        }

        if ($this->article_status === self::STATUS_DRAFT) {
            return 'Draft';
        }

        if ($this->article_status === self::STATUS_SCHEDULED && ! $this->isPubliclyVisibleNow()) {
            return 'Terjadwal';
        }

        return 'Terbit';
    }
}
