<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['gallery_items', 'gallery_page_sections', 'gallery_page_media_items', 'gallery_item_gallery_page_section'] as $name) {
            if (! Schema::hasTable('legacy_'.$name) && Schema::hasTable($name)) {
                Schema::rename($name, 'legacy_'.$name);
            }
        }
        if (! Schema::hasTable('gallery_items')) {
            Schema::create('gallery_items', function (Blueprint $table): void {
                $table->id();
                $table->string('type', 10)->default('photo');
                $table->string('media_url', 2048);
                $table->string('thumbnail_url', 2048)->nullable();
                $table->string('alt_text', 300)->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
        DB::table('legacy_gallery_items')->orderBy('id')->chunkById(100, function ($rows): void {
            foreach ($rows as $row) {
                $poster = $row->type === 'photo' ? $row->media_url : $this->videoPoster($row->media_url ?? '');
                DB::table('gallery_items')->insertOrIgnore([
                    'id' => $row->id, 'type' => $row->type,
                    'media_url' => $row->media_url ?? '', 'thumbnail_url' => $poster,
                    'alt_text' => mb_substr($row->title_id ?: $row->title ?: $row->title_en ?: $row->title_ar ?: '', 0, 300),
                    'created_at' => $row->created_at, 'updated_at' => $row->updated_at,
                    'deleted_at' => $row->deleted_at ?: (! $row->media_url || ! $row->is_published || (! $row->show_on_homepage && ! $row->show_on_gallery_page)
                        ? now()->toDateTimeString() : null),
                ]);
            }
        });
        $nextId = (int) DB::table('legacy_gallery_items')->max('id');
        DB::table('legacy_gallery_page_media_items')->orderBy('id')->chunkById(100, function ($rows) use (&$nextId): void {
            foreach ($rows as $row) {
                if (DB::table('legacy_gallery_items')->where('legacy_gallery_page_media_item_id', $row->id)->exists()) {
                    continue;
                }
                $section = DB::table('legacy_gallery_page_sections')->where('id', $row->gallery_page_section_id)->first();
                $hidden = ! $section || ! $section->is_published || $section->deleted_at;
                DB::table('gallery_items')->insertOrIgnore([
                    'id' => ++$nextId, 'type' => $row->type,
                    'media_url' => $row->media_url ?? '',
                    'thumbnail_url' => $row->type === 'photo' ? $row->media_url : $this->videoPoster($row->media_url ?? ''),
                    'alt_text' => mb_substr($row->title_id ?? '', 0, 300),
                    'created_at' => $row->created_at, 'updated_at' => $row->updated_at,
                    'deleted_at' => $row->deleted_at ?: ($hidden || ! $row->media_url || ! $row->is_published ? now()->toDateTimeString() : null),
                ]);
            }
        });
        $unmapped = DB::table('legacy_gallery_page_media_items')->whereNotIn('id', function ($query): void {
            $query->select('legacy_gallery_page_media_item_id')->from('legacy_gallery_items')->whereNotNull('legacy_gallery_page_media_item_id');
        })->count();
        if (DB::table('gallery_items')->count() < DB::table('legacy_gallery_items')->count() + $unmapped) {
            throw new RuntimeException('Gallery migration is incomplete; original media references remain archived.');
        }
    }

    private function videoPoster(string $url): ?string
    {
        $photo = match (basename(parse_url($url, PHP_URL_PATH) ?? '')) {
            'it-v1.mp4' => 'labit', 'musola-v1.mp4' => 'mushalla', 'aula-v1.mp4' => 'aula',
            'renang-v1.mp4' => 'renang', 'tekwondo-v1.mp4' => 'taekwondo', default => null,
        };
        if ($photo === null) {
            return null;
        }
        $parts = parse_url($url);

        return ($parts['scheme'] ?? 'https').'://'.($parts['host'] ?? '').'/site/school-life/'.$photo.'-v1.webp';
    }

    public function down(): void
    {
        throw new RuntimeException('Forward-only gallery migration: originals and media are retained; preserve new records when reverting.');
    }
};
