<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('gallery_items')) {
            return;
        }

        foreach ($this->motionReplacements() as $key => $filename) {
            DB::table('gallery_items')
                ->whereIn('media_url', [
                    $this->schoolLifeUrl($key),
                    $this->galleryMotionUrl($filename),
                ])
                ->update([
                    'type' => 'video',
                    'media_url' => $this->galleryMotionUrl($filename),
                    'show_on_homepage' => true,
                    'updated_at' => now(),
                ]);
        }

        DB::table('gallery_items')
            ->whereIn('media_url', array_map(
                fn (string $key): string => $this->schoolLifeUrl($key),
                $this->nonFeaturedFacilityKeys(),
            ))
            ->update([
                'show_on_homepage' => false,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('gallery_items')) {
            return;
        }

        foreach ($this->motionReplacements() as $key => $filename) {
            DB::table('gallery_items')
                ->where('media_url', $this->galleryMotionUrl($filename))
                ->update([
                    'type' => 'photo',
                    'media_url' => $this->schoolLifeUrl($key),
                    'show_on_homepage' => $key !== 'taekwondo',
                    'updated_at' => now(),
                ]);
        }

        DB::table('gallery_items')
            ->whereIn('media_url', array_map(
                fn (string $key): string => $this->schoolLifeUrl($key),
                $this->nonFeaturedFacilityKeys(),
            ))
            ->update([
                'show_on_homepage' => true,
                'updated_at' => now(),
            ]);
    }

    /** @return array<string, string> */
    private function motionReplacements(): array
    {
        return [
            'labit' => 'it-v1.mp4',
            'mushalla' => 'musola-v1.mp4',
            'aula' => 'aula-v1.mp4',
            'renang' => 'renang-v1.mp4',
            'taekwondo' => 'tekwondo-v1.mp4',
        ];
    }

    /** @return array<int, string> */
    private function nonFeaturedFacilityKeys(): array
    {
        return [
            'perpustakaan',
            'psikolog',
            'parenting',
            'gigianak',
            'fullday',
        ];
    }

    private function schoolLifeUrl(string $key): string
    {
        return (string) config('media.static.school_life.'.$key);
    }

    private function galleryMotionUrl(string $filename): string
    {
        return rtrim((string) config('media.public_url'), '/')
            .'/gallery/media/'.$filename;
    }
};
