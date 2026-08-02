<?php

use App\Models\PpdbSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders localized Login submenus on desktop and mobile without a public PPDB menu link', function (string $locale, string $login, string $teacher, string $student): void {
    PpdbSetting::query()->firstOrFail()->update(['is_active' => false]);

    $response = $this->withSession(['locale' => $locale])->get(route('home'))->assertOk();
    $content = $response->getContent();

    expect($content)
        ->toContain('dir="'.($locale === 'ar' ? 'rtl' : 'ltr').'"')
        ->toContain($login)
        ->toContain($teacher)
        ->toContain($student)
        ->not->toContain('href="'.route('ppdb').'"')
        ->not->toContain('href="/ppdb"');
    expect(substr_count($content, 'href="'.route('guru.login').'"'))->toBeGreaterThanOrEqual(2)
        ->and(substr_count($content, 'href="'.route('murid.login').'"'))->toBeGreaterThanOrEqual(2)
        ->and(substr_count($content, 'nav-login'))->toBeGreaterThanOrEqual(2);
})->with([
    'Indonesia' => ['id', 'LOGIN', 'Guru', 'Murid'],
    'English' => ['en', 'LOGIN', 'Teacher', 'Student'],
    'Arabic' => ['ar', 'تسجيل الدخول', 'المعلم', 'الطالب'],
]);

it('uses the same setting to expose the hero campaign and PPDB route only while open', function (): void {
    $setting = PpdbSetting::query()->firstOrFail();
    $setting->update([
        'registration_url' => 'https://apply.example.test/form',
        'is_active' => true,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('href="'.route('ppdb').'"', escape: false)
        ->assertSee('data-hero-ppdb-description-link', escape: false)
        ->assertDontSee('data-hero-ppdb-cta', escape: false);
    $this->get(route('ppdb'))
        ->assertOk()
        ->assertSee('https://apply.example.test/form', escape: false);

    $setting->update(['is_active' => false]);
    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee('href="'.route('ppdb').'"', escape: false)
        ->assertDontSee('data-hero-ppdb-description-link', escape: false);
    $this->get(route('ppdb'))->assertNotFound();
});

it('returns a localized informational 404 when PPDB is closed', function (string $locale, string $heading): void {
    PpdbSetting::query()->firstOrFail()->update(['is_active' => false]);

    $this->withSession(['locale' => $locale])
        ->get(route('ppdb'))
        ->assertNotFound()
        ->assertSee('dir="'.($locale === 'ar' ? 'rtl' : 'ltr').'"', escape: false)
        ->assertSee($heading);
})->with([
    ['id', 'Pendaftaran saat ini sedang ditutup'],
    ['en', 'Admissions are currently closed'],
    ['ar', 'التسجيل مغلق حاليًا'],
]);

it('renders localized teacher and student login pages in the correct direction', function (string $locale, string $direction, string $teacher, string $student): void {
    $adminPage = $this->withSession(['locale' => $locale])->get(route('login'));
    $teacherPage = $this->withSession(['locale' => $locale])->get(route('guru.login'));
    $studentPage = $this->withSession(['locale' => $locale])->get(route('murid.login'));

    $adminPage->assertOk()
        ->assertSee('dir="'.$direction.'"', escape: false)
        ->assertSee(route('google.redirect'), escape: false);
    $teacherPage->assertOk()
        ->assertSee('dir="'.$direction.'"', escape: false)
        ->assertSee($teacher)
        ->assertSee(route('google.guru.redirect'), escape: false);
    $studentPage->assertOk()
        ->assertSee('dir="'.$direction.'"', escape: false)
        ->assertSee($student)
        ->assertSee('pattern="[A-Za-z0-9]{1,32}"', escape: false);
})->with([
    ['id', 'ltr', 'Login Guru', 'Login Murid'],
    ['en', 'ltr', 'Teacher Login', 'Student Login'],
    ['ar', 'rtl', 'تسجيل دخول المعلم', 'تسجيل دخول الطالب'],
]);
