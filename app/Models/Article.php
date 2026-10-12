<?php

namespace App\Models;

use App\Models\Concerns\AuditsAdminChanges;
use App\Models\Concerns\ResolvesLocalizedContent;
use App\Support\ArticleContentSanitizer;
use App\Support\HeroVideoUrl;
use App\Support\PublicUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

final class Article extends Model
{
    use AuditsAdminChanges, ResolvesLocalizedContent, SoftDeletes;

    protected $fillable = [
        'title_id', 'title_en', 'title_ar', 'description_id', 'description_en', 'description_ar',
        'content_id', 'content_en', 'content_ar', 'cover_url', 'cover_thumbnail_url', 'published_date',
    ];

    protected function casts(): array
    {
        return ['published_date' => 'date'];
    }

    protected static function booted(): void
    {
        self::creating(function (Article $article): void {
            $article->slug ??= 'artikel-'.Str::uuid();
        });
        self::saving(function (Article $article): void {
            $sanitizer = app(ArticleContentSanitizer::class);
            foreach (['id', 'en', 'ar'] as $locale) {
                $column = 'content_'.$locale;
                if ($article->isDirty($column)) {
                    $article->{$column} = $sanitizer->sanitize($article->{$column}) ?: null;
                }
            }
        });
    }

    public function scopeLatestPublished(Builder $query): Builder
    {
        return $query->orderByDesc('id');
    }

    public function titleForLocale(string $locale): string
    {
        return $this->localizedValue($locale, $this->title_id, $this->title_en, $this->title_ar, (string) $this->title_ar);
    }

    public function descriptionForLocale(string $locale): string
    {
        return $this->localizedValue($locale, $this->description_id, $this->description_en, $this->description_ar, (string) $this->description_ar);
    }

    public function contentForLocale(string $locale): string
    {
        return $this->localizedValue($locale, $this->content_id, $this->content_en, $this->content_ar, (string) $this->content_ar);
    }

    public function contentLocale(string $locale): string
    {
        foreach (array_unique([$locale, 'id', 'en', 'ar']) as $candidate) {
            if (trim((string) $this->{'content_'.$candidate}) !== '') {
                return $candidate;
            }
        }

        return $locale;
    }

    public function coverPosterUrl(): ?string
    {
        if ($this->cover_url === null || ! PublicUrl::isSafe($this->cover_url)) {
            return null;
        }

        return HeroVideoUrl::isDirectVideo($this->cover_url)
            ? PublicUrl::normalize((string) $this->cover_thumbnail_url)
            : $this->cover_url;
    }

    public function linkForLocale(string $locale): string
    {
        return route('artikel.native', ['article' => $this->slug]);
    }

    public function getAdminTitleAttribute(): string
    {
        return $this->titleForLocale('id') ?: 'Artikel tanpa judul';
    }
}
