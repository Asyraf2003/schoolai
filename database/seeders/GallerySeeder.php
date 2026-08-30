<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SeedsGalleryItems;
use Database\Seeders\Concerns\SeedsGalleryPageSections;
use Illuminate\Database\Seeder;

final class GallerySeeder extends Seeder
{
    use SeedsGalleryItems;
    use SeedsGalleryPageSections;

    private function galleryMotionUrl(string $filename): string
    {
        return rtrim((string) config('media.public_url'), '/')
            .'/gallery/media/'.ltrim($filename, '/');
    }
}
