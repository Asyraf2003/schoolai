<?php

use App\Models\PpdbSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders a localized Login link without direct role or public PPDB menu links', function (string $locale, string $login): void {
    PpdbSetting::query()->firstOrFail()->update(['is_active' => false]);

    $response = $this->withSession(['locale' => $locale])->get(route('home'))->assertOk();
    $content = $response->getContent();

    expect($content)
        ->toContain('dir="'.($locale === 'ar' ? 'rtl' : 'ltr').'"')
        ->toContain($login)
        ->not->toContain('href="'.route('guru.login').'"')
        ->not->toContain('href="'.route('murid.login').'"')
        ->not->toContain('href="'.route('ppdb').'"')
        ->not->toContain('href="/ppdb"');
    expect(substr_count($content, 'href="'.route('portal.login').'"'))->toBeGreaterThanOrEqual(2)
        ->and(substr_count($content, 'nav-login'))->toBeGreaterThanOrEqual(2);
})->with([
    'Indonesia' => ['id', 'Login'],
    'English' => ['en', 'Login'],
    'Arabic' => ['ar', 'تسجيل الدخول'],
]);

it('renders localized role choices before the teacher and student forms', function (string $locale, string $direction, string $heading, string $teacher, string $student): void {
    $this->withSession(['locale' => $locale])
        ->get(route('portal.login'))
        ->assertOk()
        ->assertSee('dir="'.$direction.'"', escape: false)
        ->assertSee($heading)
        ->assertSee($teacher)
        ->assertSee($student)
        ->assertSee('href="'.route('guru.login').'"', escape: false)
        ->assertSee('href="'.route('murid.login').'"', escape: false)
        ->assertDontSee(route('google.redirect'), escape: false)
        ->assertDontSee(route('google.guru.redirect'), escape: false);
})->with([
    ['id', 'ltr', 'Pilih jenis akun', 'Guru', 'Murid'],
    ['en', 'ltr', 'Choose your account type', 'Teacher', 'Student'],
    ['ar', 'rtl', 'اختر نوع الحساب', 'المعلم', 'الطالب'],
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

it('renders localized admin, teacher, and student login pages in the correct direction', function (string $locale, string $direction, string $teacher, string $student): void {
    $adminPage = $this->withSession(['locale' => $locale])->get(route('login'));
    $teacherPage = $this->withSession(['locale' => $locale])->get(route('guru.login'));
    $studentPage = $this->withSession(['locale' => $locale])->get(route('murid.login'));

    $adminPage->assertOk()
        ->assertSee('dir="'.$direction.'"', escape: false)
        ->assertSee(route('google.redirect'), escape: false);
    $teacherPage->assertOk()
        ->assertSee('dir="'.$direction.'"', escape: false)
        ->assertSee($teacher)
        ->assertSee(route('portal.login'), escape: false)
        ->assertSee(route('google.guru.redirect'), escape: false);
    $studentPage->assertOk()
        ->assertSee('dir="'.$direction.'"', escape: false)
        ->assertSee($student)
        ->assertSee(route('portal.login'), escape: false)
        ->assertSee('pattern="[A-Za-z0-9]{1,32}"', escape: false);
})->with([
    ['id', 'ltr', 'Login Guru', 'Login Murid'],
    ['en', 'ltr', 'Teacher Login', 'Student Login'],
    ['ar', 'rtl', 'تسجيل دخول المعلم', 'تسجيل دخول الطالب'],
]);
