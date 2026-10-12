<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class StoryArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $sources = [
                ['menjelajah-dunia-digital', 'labit', 'it'],
                ['belajar-nilai-dalam-keseharian', 'mushalla', 'musola'],
                ['berani-mencoba-hal-baru', 'renang', 'renang'],
                ['disiplin-dalam-gerak', 'taekwondo', 'tekwondo'],
            ];
            if (Article::withTrashed()->whereIn('slug', array_column($sources, 0))->exists()) {
                return;
            }

            Article::query()->delete();
            foreach (array_reverse($sources, true) as $index => [$slug, $photo, $video]) {
                $data = [
                    'cover_url' => rtrim(config('media.public_url'), '/').'/gallery/media/'.$video.'-v1.mp4',
                    'cover_thumbnail_url' => config('media.static.school_life.'.$photo),
                ];
                foreach (['id', 'en', 'ar'] as $locale) {
                    $data['title_'.$locale] = trans('stories.features.'.$index.'.title', [], $locale);
                    $data['description_'.$locale] = trans('stories.features.'.$index.'.description', [], $locale);
                    $data['content_'.$locale] = '<p>'.htmlspecialchars($data['description_'.$locale], ENT_QUOTES, 'UTF-8').'</p>';
                }
                (new Article($data))->forceFill(['slug' => $slug])->save();
            }
        });
    }
}
