<?php

namespace App\Models\Concerns;

trait ResolvesArticlePresentation
{
    public function getAdminTitleAttribute(): string
    {
        return $this->firstFilled($this->title_id, $this->title_en, $this->title_ar, 'Artikel tanpa judul');
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
        return $this->localizedValue(
            $locale,
            $this->subtitle_id,
            $this->subtitle_en,
            $this->subtitle_ar,
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

    public function contentForLocale(string $locale): string
    {
        return $this->localizedValue(
            $locale,
            $this->content_id,
            $this->content_en,
            $this->content_ar,
        );
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
