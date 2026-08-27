<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            ArticleSeeder::class,
            HeroArticleSeeder::class,
            GallerySeeder::class,
            PpdbSeeder::class,
        ]);
    }
}
