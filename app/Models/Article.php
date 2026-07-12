<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Article extends Model
{
    use HasFactory;

    public const DEFAULT_AUTHOR = 'Admin';

    protected $fillable = [
        'title_id',
        'title_en',
        'description_id',
        'description_en',
        'thumbnail_url',
        'link_id',
        'link_en',
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

    public function getAdminTitleAttribute(): string
    {
        return $this->firstFilled($this->title_id, $this->title_en, 'Artikel tanpa judul');
    }

    public function titleForLocale(string $locale): string
    {
        return $locale === 'en'
            ? $this->firstFilled($this->title_en, $this->title_id, 'Untitled article')
            : $this->firstFilled($this->title_id, $this->title_en, 'Artikel tanpa judul');
    }

    public function descriptionForLocale(string $locale): string
    {
        return $locale === 'en'
            ? $this->firstFilled($this->description_en, $this->description_id)
            : $this->firstFilled($this->description_id, $this->description_en);
    }

    public function linkForLocale(string $locale): string
    {
        return $locale === 'en'
            ? $this->firstFilled($this->link_en, $this->link_id)
            : $this->firstFilled($this->link_id, $this->link_en);
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
