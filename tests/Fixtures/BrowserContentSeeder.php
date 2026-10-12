<?php

namespace Tests\Fixtures;

use App\Models\Article;
use App\Models\GalleryItem;
use App\Models\PpdbSetting;
use App\Models\User;
use App\Services\ActiveSessionManager;
use Database\Seeders\StoryArticleSeeder;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use LogicException;

final class BrowserContentSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('testing')) {
            throw new LogicException('Browser fixtures must only run in a testing database.');
        }
        $sources = [['labit', 'it'], ['mushalla', 'musola'], ['aula', 'aula'], ['renang', 'renang'], ['taekwondo', 'tekwondo'],
            ['perpustakaan', null], ['psikolog', null], ['parenting', null], ['gigianak', null], ['fullday', null]];
        GalleryItem::query()->delete();
        foreach (array_reverse($sources) as [$photo, $video]) {
            $poster = config('media.static.school_life.'.$photo);
            GalleryItem::create(['type' => $video ? 'video' : 'photo', 'thumbnail_url' => $poster,
                'media_url' => $video ? config('media.public_url').'/gallery/media/'.$video.'-v1.mp4' : $poster,
                'alt_text' => 'Browser fixture '.$photo]);
        }
        Article::query()->delete();
        Article::onlyTrashed()->whereIn('slug', ['menjelajah-dunia-digital', 'belajar-nilai-dalam-keseharian', 'berani-mencoba-hal-baru', 'disiplin-dalam-gerak'])->restore();
        $this->call(StoryArticleSeeder::class);
        PpdbSetting::query()->updateOrCreate(['id' => 1], ['registration_url' => 'https://forms.gle/BrowserFixture']);
        $admin = User::query()->firstOrNew(['email' => 'browser-proof@example.test']);
        $admin->forceFill(['name' => 'Browser Proof', 'role' => User::ROLE_ADMIN, 'password' => Str::random(64)])->save();
        $session = app('session')->driver();
        $session->start();
        $session->put(Auth::guard()->getName(), $admin->id);
        $session->put(ActiveSessionManager::SESSION_KEY, $admin->session_version);
        $session->save();
        $name = config('session.cookie');
        $value = CookieValuePrefix::create($name, app('encrypter')->getKey()).$session->getId();
        $cookie = app('encrypter')->encrypt($value, false);
        $path = getenv('SCHOOLAI_BROWSER_STATE') ?: '/tmp/schoolai-browser-state.json';
        file_put_contents($path, json_encode(['cookies' => [['name' => $name, 'value' => $cookie,
            'domain' => '127.0.0.1', 'path' => '/', 'httpOnly' => true, 'secure' => false, 'sameSite' => 'Lax']], 'origins' => []]));
        chmod($path, 0600);
    }
}
