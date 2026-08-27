<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SeedsGalleryItems;
use Database\Seeders\Concerns\SeedsGalleryPageSections;
use Illuminate\Database\Seeder;

final class GallerySeeder extends Seeder
{
    use SeedsGalleryItems;
    use SeedsGalleryPageSections;
}
