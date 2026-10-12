<?php

use App\Models\Article;
use App\Models\GalleryItem;
use App\Models\User;
use App\Support\Media\MediaUrlResolver;
use App\Support\Media\R2MediaConfiguration;
use Aws\CommandInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Psr\Http\Message\RequestInterface;

uses(RefreshDatabase::class);

it('proves real admin uploads and retained media against R2 without deleting objects', function (): void {
    config()->set('media.disk', 's3');
    app(R2MediaConfiguration::class)->assertValid();
    $disk = Storage::disk('s3');
    $client = $disk->getClient();
    $client->getHandlerList()->appendInit(static function (callable $handler): callable {
        return static function (CommandInterface $command, ?RequestInterface $request = null) use ($handler): PromiseInterface {
            if (in_array($command->getName(), ['DeleteObject', 'DeleteObjects'], true)) {
                throw new LogicException('Runtime proof is forbidden from deleting R2 objects.');
            }

            return $handler($command, $request);
        };
    }, 'retain-runtime-proof');
    $videoPath = getenv('R2_PROOF_VIDEO');
    $directory = getenv('R2_PROOF_DIRECTORY');
    expect(is_file($videoPath))->toBeTrue()->and(is_dir($directory))->toBeTrue();
    $video = fn (): UploadedFile => new UploadedFile($videoPath, 'proof.mp4', 'video/mp4', test: true);
    $poster = fn (): UploadedFile => new UploadedFile(public_path('media/footer/school-map.webp'), 'proof.webp', 'image/webp', test: true);
    $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
    $this->post(route('admin.galeri.store'), [
        'media_file' => $video(), 'thumbnail_file' => $poster(), 'alt_text' => 'RC R2 runtime proof',
    ])->assertRedirect(route('admin.galeri'))->assertSessionHasNoErrors();
    $this->post(route('admin.artikel.store'), [
        'content_id' => '<p>RC R2 runtime proof</p>', 'cover_file' => $video(), 'cover_thumbnail_file' => $poster(),
    ])->assertRedirect(route('admin.artikel'))->assertSessionHasNoErrors();
    $item = GalleryItem::sole();
    $article = Article::sole();
    $urls = [$item->media_url, $item->thumbnail_url, $article->cover_url, $article->cover_thumbnail_url];
    $proof = [];
    foreach ($urls as $url) {
        $key = app(MediaUrlResolver::class)->ownedKey($url);
        expect($key)->not->toBeNull();
        $head = $client->headObject(['Bucket' => config('filesystems.disks.s3.bucket'), 'Key' => $key]);
        expect($head['CacheControl'])->toBe(config('media.cache_control'))
            ->and($head['ContentType'])->toBe(str_ends_with($key, '.mp4') ? 'video/mp4' : 'image/webp')
            ->and($head['ContentLength'])->toBeGreaterThan(0);
        $proof[] = ['url' => $url, 'type' => $head['ContentType'], 'bytes' => $head['ContentLength']];
    }
    $this->get(route('galeri'))->assertSee($item->media_url, false)->assertSee($item->thumbnail_url, false);
    $this->get($article->linkForLocale('id'))->assertSee($article->cover_url, false)->assertSee($article->cover_thumbnail_url, false);
    $this->delete(route('admin.galeri.destroy', $item))->assertRedirect(route('admin.galeri'));
    $this->delete(route('admin.artikel.destroy', $article))->assertRedirect(route('admin.artikel'));
    $this->patch(route('admin.galeri.restore', $item))->assertRedirect(route('admin.galeri'));
    $this->patch(route('admin.artikel.restore', $article))->assertRedirect(route('admin.artikel'));
    foreach ($urls as $url) {
        expect($disk->exists(app(MediaUrlResolver::class)->ownedKey($url)))->toBeTrue();
    }
    file_put_contents($directory.'/r2-admin-upload.json', json_encode([
        'date' => gmdate(DATE_ATOM), 'uploads' => $proof, 'delete_commands' => 0,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
})->skip(getenv('R2_RUNTIME_PROOF') !== '1', 'Real R2 writes require explicit runtime-proof opt-in.')
    ->group('r2-runtime');
