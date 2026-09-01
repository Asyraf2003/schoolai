<?php

use App\Models\PpdbSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('self-hosts the public Latin font without a Google Fonts dependency', function (): void {
    $head = file_get_contents(resource_path('views/partials/site-head-meta.blade.php'));
    $latinTypography = file_get_contents(resource_path('css/public-latin-inter.css'));
    $font = resource_path('fonts/inter/inter-latin-variable.woff2');

    expect($head)
        ->not->toContain('fonts.googleapis.com')
        ->not->toContain('fonts.gstatic.com')
        ->and($latinTypography)
        ->toContain('@font-face')
        ->toContain('font-weight: 100 900')
        ->toContain('font-display: optional')
        ->not->toContain('font-display: swap')
        ->toContain('../fonts/inter/inter-latin-variable.woff2')
        ->and($font)->toBeFile()
        ->and(filesize($font))->toBeGreaterThan(0);
});

it('preserves shared public chrome semantics for every locale', function (): void {
    $localeContracts = [
        'id' => [
            'direction' => 'ltr',
            'og_locale' => 'id_ID',
            'mega_title' => 'Momen belajar, tumbuh, dan berprestasi.',
            'modal_title' => 'Pilih bahasa',
            'image_alt' => 'Lingkungan sekolah dan pembelajaran Al Mustaqbal School',
        ],
        'en' => [
            'direction' => 'ltr',
            'og_locale' => 'en_US',
            'mega_title' => 'Moments of learning, growth, and achievement.',
            'modal_title' => 'Choose language',
            'image_alt' => 'Al Mustaqbal School campus and learning environment',
        ],
        'ar' => [
            'direction' => 'rtl',
            'og_locale' => 'ar_AR',
            'mega_title' => 'لحظات التعلّم والنمو والإنجاز.',
            'modal_title' => 'اختر اللغة',
            'image_alt' => 'حرم مدرسة المستقبل وبيئة التعلم',
        ],
    ];

    foreach ($localeContracts as $locale => $contract) {
        app()->setLocale($locale);

        $home = $this
            ->withSession(['locale' => $locale])
            ->get(route('home'))
            ->assertOk();
        $public = $this
            ->withSession(['locale' => $locale])
            ->get(route('artikel'))
            ->assertOk();

        $home->assertSee($contract['mega_title'])
            ->assertSee($contract['modal_title'])
            ->assertSee('content="'.$contract['og_locale'].'"', false)
            ->assertSee('content="'.$contract['image_alt'].'"', false)
            ->assertSee('id="navMegaPanel-2"', false)
            ->assertSee('id="mobileNavMegaPanel-2"', false)
            ->assertSee('nav-language__flag--id', false)
            ->assertSee('nav-language__flag--en', false)
            ->assertSee('nav-language__flag--ar', false);

        expect($home->getContent())
            ->toContain('<html lang="'.$locale.'" dir="'.$contract['direction'].'">')
            ->toContain('class="site-footer site-footer--home-story"')
            ->toContain('href="#visi-misi"')
            ->toContain('target="_blank"')
            ->toContain('rel="noopener noreferrer"')
            ->not->toMatch('/<a[^>]+class="[^"]*navbar__cta/');

        expect($public->getContent())
            ->toContain('<html lang="'.$locale.'" dir="'.$contract['direction'].'">')
            ->toContain('href="'.route('home').'#visi-misi"')
            ->not->toContain('class="site-footer site-footer--home-story"');
    }
});

it('keeps main-menu color owned by header state across public routes', function (): void {
    $navCss = file_get_contents(resource_path(
        'css/pages/welcome-hero/002-welcome-hero-cascade-002.css'
    ));

    expect($navCss)
        ->toContain('.navbar:not(.is-scrolled):not(.has-open-menu) .nav-link:not(.active):hover')
        ->toContain('color: #ffffff')
        ->toContain('.navbar.is-scrolled .nav-link:not(.active):hover')
        ->toContain('.navbar.has-open-menu .nav-link:not(.active):hover')
        ->toContain('color: #172c28')
        ->not->toMatch('/\.nav-link:hover\s*\{[^}]*color\s*:/s');

    PpdbSetting::query()->updateOrCreate(
        ['id' => 1],
        [
            'registration_url' => PpdbSetting::DEFAULT_REGISTRATION_URL,
            'is_active' => true,
        ],
    );

    foreach (['ppdb', 'galeri', 'artikel'] as $routeName) {
        $content = $this->get(route($routeName))->assertOk()->getContent();

        expect($content)
            ->toContain('class="navbar navbar--public"')
            ->toContain('id="navbar"')
            ->toContain('class="navbar__menu"')
            ->toContain('nav-mega__trigger');
    }
});

it('keeps group one Blade owners free of view-owned PHP', function (): void {
    $bladeOwners = [
        'views/partials/site-navbar.blade.php',
        'views/partials/site-navbar/header.blade.php',
        'views/partials/site-navbar/mobile-navigation.blade.php',
        'views/partials/site-footer.blade.php',
        'views/partials/site-head-meta.blade.php',
        'views/partials/language-flag.blade.php',
        'views/layouts/admin.blade.php',
    ];

    foreach ($bladeOwners as $bladeOwner) {
        $source = file_get_contents(resource_path($bladeOwner));

        expect($source)
            ->not->toContain('<?php')
            ->not->toMatch('/@php(?:\s|\()|@endphp/');
    }

    foreach (['context.php', 'menu.php', 'presentation.php'] as $dataFile) {
        expect(resource_path('views/partials/site-navbar/data/'.$dataFile))
            ->not->toBeFile();
    }
});

it('preserves the active admin navigation contract', function (): void {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'disabled_at' => null,
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('href="'.route('admin.dashboard').'"', false)
        ->assertSee('class="admin-side-link is-active"', false)
        ->assertSee('aria-current="page"', false);

    expect(substr_count($response->getContent(), 'class="admin-side-link'))
        ->toBe(5);

    expect($response->getContent())
        ->not->toContain('admin/stats')
        ->not->toContain('admin/testimoni');
});
