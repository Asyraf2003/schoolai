<?php

it('keeps the homepage independent from the auth perspective implementation', function (): void {
    $welcomeJs = file_get_contents(resource_path('js/pages/welcome.js'));
    $welcomeCss = file_get_contents(resource_path('css/pages/welcome.css'));

    expect($welcomeJs)
        ->not->toContain('login-perspective-navigation')
        ->and($welcomeCss)
        ->not->toContain('050-login-perspective-navigation')
        ->and(file_exists(resource_path('js/pages/welcome/login-perspective-navigation.js')))
        ->toBeFalse()
        ->and(file_exists(resource_path('css/pages/welcome/050-login-perspective-navigation.css')))
        ->toBeFalse();
});

it('registers one dedicated perspective asset graph for public auth', function (): void {
    $vite = file_get_contents(base_path('vite.config.js'));
    $view = file_get_contents(resource_path('views/auth/login-perspective.blade.php'));
    $assets = [
        'resources/js/pages/public-auth-perspective.js',
        'resources/css/pages/public-auth-perspective.css',
    ];

    foreach ($assets as $asset) {
        expect(file_exists(base_path($asset)))->toBeTrue();
        expect($vite)->toContain($asset);
        expect($view)->toContain($asset);
    }
});

it('keeps perspective css modules within the source structure limit', function (): void {
    $entry = resource_path('css/pages/public-auth-perspective.css');
    $source = file_get_contents($entry);
    preg_match_all('/@import\s+["\']([^"\']+)["\']/', $source, $matches);

    expect($matches[1])->toHaveCount(3);

    foreach ($matches[1] as $request) {
        $path = realpath(dirname($entry).'/'.$request);
        expect($path)->not->toBeFalse();
        expect(count(file($path)))->toBeLessThanOrEqual(200);
    }
});

it('runs source verification inside the build command itself', function (): void {
    $package = json_decode(file_get_contents(base_path('package.json')), true, flags: JSON_THROW_ON_ERROR);

    expect($package['scripts']['build'] ?? null)
        ->toBe('node scripts/verify-source-structure.mjs && vite build')
        ->and($package['scripts'])
        ->not->toHaveKey('prebuild');
});

it('serves every public login role through the same perspective shell', function (string $routeName): void {
    $this->get(route($routeName))
        ->assertOk()
        ->assertSee('data-auth-perspective', false)
        ->assertSee('data-auth-role-target="admin"', false)
        ->assertSee('data-auth-role-target="guru"', false)
        ->assertSee('data-auth-role-target="murid"', false);
})->with([
    'portal' => 'portal.login',
    'admin' => 'login',
    'guru' => 'guru.login',
    'murid' => 'murid.login',
]);
