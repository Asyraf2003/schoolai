<?php

use App\Models\HeroSetting;
use App\Models\PpdbSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders localized Login and PPDB links while other navigation links remain placeholders', function (string $locale, string $login): void {
    PpdbSetting::query()->firstOrFail()->update(['is_active' => false]);

    $response = $this->withSession(['locale' => $locale])->get(route('home'))->assertOk();
    $content = $response->getContent();
    $items = $response->viewData('navigation')['menuItems'];

    expect($content)
        ->toContain('dir="'.($locale === 'ar' ? 'rtl' : 'ltr').'"')
        ->toContain($login)
        ->toContain('href="'.route('portal.login').'"')
        ->toContain('href="'.route('ppdb').'"')
        ->not->toContain('href="'.route('guru.login').'"')
        ->not->toContain('href="'.route('murid.login').'"');

    expect(collect($items)->firstWhere('type', 'login')['href'])->toBe(route('portal.login'))
        ->and(collect($items)->firstWhere('type', 'ppdb')['href'])->toBe(route('ppdb'));
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

it('uses PPDB settings to control its dedicated route and homepage campaign ownership', function (): void {
    $hero = HeroSetting::query()->firstOrFail();
    $hero->update([
        'title_id' => 'Opening Admin Saat PPDB Tutup',
        'cta_url' => '#program',
    ]);

    $setting = PpdbSetting::query()->firstOrFail();
    $setting->update([
        'registration_url' => 'https://apply.example.test/form',
        'is_active' => true,
    ]);

    $this->get(route('ppdb'))
        ->assertOk()
        ->assertSee('https://apply.example.test/form', escape: false);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee(__('runtime.home.ppdb_campaign_title'))
        ->assertSee('href="/ppdb"', escape: false)
        ->assertDontSee('Opening Admin Saat PPDB Tutup');

    $setting->update(['is_active' => false]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Opening Admin Saat PPDB Tutup')
        ->assertDontSee(__('runtime.home.ppdb_campaign_title'))
        ->assertDontSee('href="/ppdb"', escape: false);

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
