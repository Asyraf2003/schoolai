<?php

namespace Database\Seeders\Concerns;

use App\Models\Article;

trait SeedsArticles
{
    public function run(): void
    {
        foreach ($this->articles() as $index => $attributes) {
            $article = Article::withTrashed()->firstOrNew([
                'slug' => $attributes['slug'],
            ]);

            $article->fill(array_merge($attributes, [
                'article_source' => Article::SOURCE_NATIVE,
                'article_status' => Article::STATUS_PUBLISHED,
                'published_at' => now()->subDays($index),
                'scheduled_at' => null,
                'author' => 'Tim Al Mustaqbal',
            ]));

            $article->save();

            if ($article->trashed()) {
                $article->restore();
            }
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function articles(): array
    {
        return array_merge(
            $this->articlesFirstHalf(),
            $this->articlesSecondHalf(),
        );
    }
}
