<?php

use App\Models\PpdbSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('links Header and Hero directly to the saved Google Form in every locale', function (string $locale): void {
    $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
    $url = 'https://forms.gle/SchoolForm123';
    $this->put(route('admin.ppdb.update'), ['registration_url' => $url])->assertRedirect(route('admin.ppdb'))->assertSessionHasNoErrors();
    expect(PpdbSetting::count())->toBe(1)->and(PpdbSetting::sole()->registration_url)->toBe($url);
    $this->get(route('admin.ppdb'))->assertOk()->assertSee($url, false);
    $this->withSession(['locale' => $locale])->get(route('home'))->assertOk()
        ->assertSee('href="'.$url.'"', false)->assertDontSee('href="/ppdb"', false)
        ->assertViewHas('hero', fn ($hero) => $hero['slides'][0]['cta']['href'] === $url);
    $this->get(route('ppdb'))->assertRedirect($url);
    $replacement = 'https://docs.google.com/forms/d/e/SchoolForm456/viewform?usp=sf_link';
    $this->put(route('admin.ppdb.update'), ['registration_url' => $replacement])->assertSessionHasNoErrors();
    expect(PpdbSetting::count())->toBe(1)->and(PpdbSetting::sole()->registration_url)->toBe($replacement);
})->with(['id', 'en', 'ar']);

it('hides PPDB when the setting is missing blank or invalid without a default fallback', function (): void {
    $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
    PpdbSetting::query()->delete();
    $this->get(route('home'))->assertViewHas('hero', fn ($hero) => $hero['slides'][0]['cta']['href'] === null);
    foreach ([null, '', 'javascript:alert(1)', 'https://evil.example/form'] as $url) {
        PpdbSetting::query()->updateOrCreate(['id' => 1], ['registration_url' => $url]);
        $response = $this->get(route('home'))->assertOk();
        expect(collect($response->viewData('navigation')['menuItems'])->firstWhere('type', 'ppdb'))->toBeNull();
        expect($response->viewData('hero')['slides'][0]['cta']['href'])->toBeNull();
        $this->get(route('ppdb'))->assertNotFound();
    }
    $this->put(route('admin.ppdb.update'), ['registration_url' => ''])->assertSessionHasNoErrors();
    expect(PpdbSetting::sole()->registration_url)->toBeNull();
});

it('rejects unsafe URLs without changing the current Form', function (string $url): void {
    $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]));
    PpdbSetting::query()->updateOrCreate(['id' => 1], ['registration_url' => 'https://forms.gle/ValidForm']);
    $this->put(route('admin.ppdb.update'), ['registration_url' => $url])->assertSessionHasErrors('registration_url');
    expect(PpdbSetting::sole()->registration_url)->toBe('https://forms.gle/ValidForm');
})->with(['javascript:alert(1)', 'http://forms.gle/a', '//forms.gle/a', 'https://forms.gle.evil.com/a',
    'https://forms.gle@evil.com/a', 'https://evil.com@forms.gle/a', 'https://forms.gle:444/a',
    'https://docs.google.com/document/d/a/edit', 'https://docs.google.com/forms/d/a/edit',
    'https://127.0.0.1/form', 'https://forms.gle/', 'https://forms.gle/a/../evil']);
