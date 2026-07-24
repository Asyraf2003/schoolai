<?php

namespace App\Models\Concerns;

use App\Models\Article;
use App\Models\Concerns\AuditsAdminChanges;
use App\Models\Concerns\ResolvesLocalizedContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

trait BuildsArticleQueries
{
    protected static function booted(): void
    {
        static::creating(function (Article $article): void {
            if (! $article->author) {
                $article->author = self::DEFAULT_AUTHOR;
            }

            $article->article_source ??= self::SOURCE_EXTERNAL;
            $article->article_status ??= self::STATUS_PUBLISHED;
        });

        static::saving(function (Article $article): void {
            if (! $article->published_at) {
                $article->published_at = now();
            }

            $article->published_date = $article->published_at
                ->copy()
                ->timezone(config('app.timezone'))
                ->toDateString();
        });
    }

    public function scopeLatestPublished(Builder $query): Builder
    {
        return $query
            ->publiclyVisible()
            ->orderByDesc('published_at')
            ->orderByDesc('id');
    }

    public function scopeLatestForAdmin(Builder $query): Builder
    {
        return $query
            ->orderByDesc('updated_at')
            ->orderByDesc('id');
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query->where(function (Builder $visibility): void {
            $visibility
                ->where('article_source', self::SOURCE_EXTERNAL)
                ->orWhere(function (Builder $native): void {
                    $native
                        ->where('article_source', self::SOURCE_NATIVE)
                        ->whereIn('article_status', [self::STATUS_PUBLISHED, self::STATUS_SCHEDULED])
                        ->whereNotNull('published_at')
                        ->where('published_at', '<=', now());
                });
        });
    }

    public static function normalizedLinkIdentity(?string $url): ?string
    {
        if (! is_string($url) || trim($url) === '') {
            return null;
        }

        $parts = parse_url(trim($url));

        if (! is_array($parts)) {
            return null;
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower((string) ($parts['host'] ?? ''));

        if (! in_array($scheme, ['http', 'https'], true) || $host === '') {
            return null;
        }

        $port = isset($parts['port']) ? (int) $parts['port'] : null;
        $authority = $host;

        if ($port !== null && ! (($scheme === 'http' && $port === 80) || ($scheme === 'https' && $port === 443))) {
            $authority .= ':' . $port;
        }

        $path = '/' . ltrim((string) ($parts['path'] ?? ''), '/');
        $path = $path === '/' ? '/' : rtrim($path, '/');
        $query = '';

        if (isset($parts['query']) && $parts['query'] !== '') {
            parse_str($parts['query'], $queryParameters);
            ksort($queryParameters);
            $query = http_build_query($queryParameters, '', '&', PHP_QUERY_RFC3986);
        }

        return $scheme . '://' . $authority . $path . ($query !== '' ? '?' . $query : '');
    }
}
