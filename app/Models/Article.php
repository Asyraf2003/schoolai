<?php

namespace App\Models;

use App\Models\Concerns\AuditsAdminChanges;
use App\Models\Concerns\ResolvesLocalizedContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Article extends Model
{
    use AuditsAdminChanges, HasFactory, ResolvesLocalizedContent, SoftDeletes;

    public const DEFAULT_AUTHOR = 'Admin';
    public const SOURCE_EXTERNAL = 'external';
    public const SOURCE_NATIVE = 'native';
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_SCHEDULED = 'scheduled';
    public const PLACEHOLDER_THUMBNAIL = '/images/article-placeholder.svg';

    protected $fillable = [
        'article_source',
        'article_status',
        'slug',
        'title_id',
        'title_en',
        'title_ar',
        'subtitle_id',
        'subtitle_en',
        'description_id',
        'description_en',
        'description_ar',
        'content_id',
        'content_en',
        'tags',
        'word_count',
        'thumbnail_url',
        'link_id',
        'link_en',
        'link_ar',
        'author',
        'published_date',
        'published_at',
        'scheduled_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'word_count' => 'integer',
        'published_date' => 'date',
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
    ];

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
        if (! $this->isNative()) {
            return true;
        }

        return in_array($this->article_status, [self::STATUS_PUBLISHED, self::STATUS_SCHEDULED], true)
            && $this->published_at !== null
            && $this->published_at->lessThanOrEqualTo(now());
    }

    public function statusLabel(): string
    {
        if ($this->trashed()) {
            return 'Dihapus';
        }

        if (! $this->isNative()) {
            return 'Eksternal';
        }

        if ($this->article_status === self::STATUS_DRAFT) {
            return 'Draft';
        }

        if ($this->article_status === self::STATUS_SCHEDULED && ! $this->isPubliclyVisibleNow()) {
            return 'Terjadwal';
        }

        return 'Terbit';
    }

    public function getAdminTitleAttribute(): string
    {
        return $this->firstFilled($this->title_id, $this->title_en, 'Artikel tanpa judul');
    }

    public function titleForLocale(string $locale): string
    {
        return $this->localizedValue(
            $locale,
            $this->title_id,
            $this->title_en,
            $this->title_ar,
            'Artikel tanpa judul',
            'Untitled article',
        );
    }

    public function subtitleForLocale(string $locale): string
    {
        return $locale === 'en'
            ? $this->firstFilled($this->subtitle_en, $this->subtitle_id)
            : $this->firstFilled($this->subtitle_id, $this->subtitle_en);
    }

    public function descriptionForLocale(string $locale): string
    {
        return $this->localizedValue(
            $locale,
            $this->description_id,
            $this->description_en,
            $this->description_ar,
        );
    }

    public function contentForLocale(string $locale): string
    {
        return $locale === 'en'
            ? $this->firstFilled($this->content_en, $this->content_id)
            : $this->firstFilled($this->content_id, $this->content_en);
    }

    public function linkForLocale(string $locale): string
    {
        if ($this->isNative() && $this->slug) {
            return route('artikel.native', ['article' => $this->slug]);
        }

        return $this->localizedValue(
            $locale,
            $this->link_id,
            $this->link_en,
            $this->link_ar,
        );
    }

    public function authorForDisplay(): string
    {
        return $this->firstFilled($this->author, self::DEFAULT_AUTHOR);
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
