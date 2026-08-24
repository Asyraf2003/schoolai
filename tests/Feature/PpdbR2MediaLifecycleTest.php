<?php

use App\Models\PpdbShowcaseItem;
use App\Models\User;
use App\Support\Media\MediaUrlResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('public');
    PpdbShowcaseItem::query()->delete();

    $this->actingAs(User::query()->forceCreate([
        'name' => 'PPDB R2 Admin',
        'email' => 'ppdb-r2@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'role' => User::ROLE_ADMIN,
    ]));
});

it('publishes replaces archives and restores PPDB photos through R2', function (): void {
    $this->post(route('admin.ppdb.showcase.store'), [
        'audience' => PpdbShowcaseItem::AUDIENCE_PARENTS,
        'title_id' => 'PPDB R2',
        'description_id' => 'Media PPDB yang dimiliki R2.',
        'media_type' => PpdbShowcaseItem::MEDIA_PHOTO,
        'media_file' => UploadedFile::fake()->image('ppdb.jpg'),
    ])->assertRedirect(route('admin.ppdb').'#ppdb-showcase-admin');

    $item = PpdbShowcaseItem::query()->where('title_id', 'PPDB R2')->firstOrFail();
    $oldKey = app(MediaUrlResolver::class)->ownedKey($item->media_url);

    expect($oldKey)->toStartWith('ppdb/showcase/new/');
    Storage::disk('public')->assertExists($oldKey);

    $this->put(route('admin.ppdb.showcase.update', $item), [
        'audience' => PpdbShowcaseItem::AUDIENCE_PARENTS,
        'title_id' => 'PPDB R2 Baru',
        'description_id' => 'Media PPDB pengganti.',
        'media_type' => PpdbShowcaseItem::MEDIA_PHOTO,
        'media_file' => UploadedFile::fake()->image('ppdb-new.webp'),
    ])->assertRedirect(route('admin.ppdb').'#ppdb-showcase-admin');

    $newKey = app(MediaUrlResolver::class)->ownedKey($item->fresh()->media_url);

    expect($newKey)->toStartWith('ppdb/showcase/'.$item->getKey().'/');
    Storage::disk('public')->assertMissing($oldKey);
    Storage::disk('public')->assertExists($newKey);

    $this->delete(route('admin.ppdb.showcase.destroy', $item))->assertRedirect();
    Storage::disk('public')->assertExists($newKey);
    $this->patch(route('admin.ppdb.showcase.restore', $item->getKey()))->assertRedirect();
    Storage::disk('public')->assertExists($newKey);
});
