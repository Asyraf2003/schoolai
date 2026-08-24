<?php

use App\Models\TestimonialMedia;
use App\Models\User;
use App\Support\Media\MediaUrlResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('public');
    TestimonialMedia::query()->delete();

    $this->actingAs(User::query()->forceCreate([
        'name' => 'Testimonial R2 Admin',
        'email' => 'testimonial-r2@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'role' => User::ROLE_ADMIN,
    ]));
});

it('publishes replaces archives and restores testimonial uploads through R2', function (): void {
    $this->post(route('admin.testimoni.store'), [
        'type' => 'photo',
        'source' => 'upload',
        'media_file' => UploadedFile::fake()->image('testimonial.jpg'),
        'is_published' => '1',
    ])->assertRedirect(route('admin.testimoni.index'));

    $item = TestimonialMedia::query()->firstOrFail();
    $oldKey = app(MediaUrlResolver::class)->ownedKey($item->media_url);

    expect($oldKey)->toStartWith('testimonials/photos/new/');
    Storage::disk('public')->assertExists($oldKey);

    $this->put(route('admin.testimoni.update', $item), [
        'type' => 'photo',
        'source' => 'upload',
        'media_file' => UploadedFile::fake()->image('testimonial-new.webp'),
        'is_published' => '1',
    ])->assertRedirect(route('admin.testimoni.index'));

    $newKey = app(MediaUrlResolver::class)->ownedKey($item->fresh()->media_url);

    expect($newKey)->toStartWith('testimonials/photos/'.$item->getKey().'/');
    Storage::disk('public')->assertMissing($oldKey);
    Storage::disk('public')->assertExists($newKey);

    $this->delete(route('admin.testimoni.destroy', $item))->assertRedirect(route('admin.testimoni.index'));
    Storage::disk('public')->assertExists($newKey);
    $this->patch(route('admin.testimoni.restore', $item->getKey()))->assertRedirect(route('admin.testimoni.index'));
    Storage::disk('public')->assertExists($newKey);
});

it('retains an owned object while an archived testimonial still references it', function (): void {
    $sharedKey = 'testimonials/photos/shared/owned.jpg';
    $sharedUrl = 'https://media.almustaqbal.sch.id/'.$sharedKey;
    Storage::disk('public')->put($sharedKey, 'shared');

    $archived = TestimonialMedia::query()->create([
        'type' => 'photo',
        'source' => 'upload',
        'media_url' => $sharedUrl,
        'sort_order' => 1,
        'is_published' => true,
    ]);
    $archived->delete();
    $active = TestimonialMedia::query()->create([
        'type' => 'photo',
        'source' => 'upload',
        'media_url' => $sharedUrl,
        'sort_order' => 1,
        'is_published' => true,
    ]);

    $this->put(route('admin.testimoni.update', $active), [
        'type' => 'photo',
        'source' => 'upload',
        'media_file' => UploadedFile::fake()->image('replacement.jpg'),
        'is_published' => '1',
    ])->assertRedirect(route('admin.testimoni.index'));

    Storage::disk('public')->assertExists($sharedKey);
});
