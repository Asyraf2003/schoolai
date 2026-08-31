<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\BuildsSeedArticles;
use Database\Seeders\Concerns\ProvidesPrimaryArticleSeeds;
use Database\Seeders\Concerns\ProvidesSecondaryArticleSeeds;
use Database\Seeders\Concerns\SeedsArticles;
use Illuminate\Database\Seeder;

final class ArticleSeeder extends Seeder
{
    use BuildsSeedArticles;
    use ProvidesPrimaryArticleSeeds;
    use ProvidesSecondaryArticleSeeds;
    use SeedsArticles;
}
