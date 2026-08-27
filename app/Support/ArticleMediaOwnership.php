<?php

namespace App\Support;

use App\Models\Article;
use App\Support\Media\MediaUrlResolver;
use App\Support\Media\R2MediaStorage;
use Illuminate\Database\Eloquent\Builder;

final class ArticleMediaOwnership
{
    private const THUMBNAIL_PREFIX = 'articles/thumbnails/';

    public function __construct(
        private readonly MediaUrlResolver $urlResolver,
        private readonly R2MediaStorage $storage,
    ) {}

    public function deleteUnreferencedThumbnail(?string $url): bool
    {
        $key = $this->urlResolver->ownedKey($url);

        if ($key === null || ! str_starts_with($key, self::THUMBNAIL_PREFIX)) {
            return false;
        }

        if ($this->isReferenced((string) $url)) {
            return false;
        }

        return $this->storage->deleteKey($key);
    }

    private function isReferenced(string $url): bool
    {
        return Article::query()
            ->withTrashed()
            ->where(function (Builder $query) use ($url): void {
                $query->where('thumbnail_url', $url)
                    ->orWhere('content_id', 'like', '%'.$url.'%')
                    ->orWhere('content_en', 'like', '%'.$url.'%')
                    ->orWhere('content_ar', 'like', '%'.$url.'%');
            })
            ->exists();
    }
}
