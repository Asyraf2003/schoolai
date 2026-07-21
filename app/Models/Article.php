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

    protected $fillable = [
        'title_id',
        'title_en',
        'title_ar',
        'description_id',
        'description_en',
        'description_ar',
        'thumbnail_url',
        'link_id',
        'link_en',
        'link_ar',
        'author',
        'published_date',
        'published_at',
    ];

    protected $casts = [
        'published_date' => 'date',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Article $article): void {
            if (! $article->author) {
                $article->author = self::DEFAULT_AUTHOR;
            }
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
            ->orderByDesc('published_at')
            ->orderByDesc('id');
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

    public function descriptionForLocale(string $locale): string
    {
        return $this->localizedValue(
            $locale,
            $this->description_id,
            $this->description_en,
            $this->description_ar,
        );
    }

    public function linkForLocale(string $locale): string
    {
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
