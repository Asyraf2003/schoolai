<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

final class HeroArticleSeeder extends Seeder
{
    private const CREATIVE_PROJECT_SLUG = 'proyek-kreatif-yang-melatih-keberanian-anak';

    public function run(): void
    {
        if (! Schema::hasColumn('articles', 'hero_position')) {
            return;
        }

        Article::query()->update(['hero_position' => null]);

        $slugs = [
            'belajar-bermakna-dimulai-dari-rasa-ingin-tahu',
            'prestasi-tumbuh-dari-proses-yang-konsisten',
            'qiii-menjadi-kompas-kehidupan-sekolah',
            'sekolah-dan-keluarga-bertumbuh-sebagai-satu-tim',
            self::CREATIVE_PROJECT_SLUG,
        ];

        $articles = Article::query()
            ->whereIn('slug', $slugs)
            ->latestPublished()
            ->get()
            ->sortBy(
                fn (Article $article): int => array_search(
                    $article->slug,
                    $slugs,
                    true,
                ),
            );

        $articles->values()->each(
            fn (Article $article, int $index) => $article->update(['hero_position' => $index + 1]),
        );
    }
}
