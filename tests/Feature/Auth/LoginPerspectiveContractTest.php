<?php

it('keeps the landing page as the persistent Codrops perspective surface', function (): void {
    $vite = file_get_contents(base_path('vite.config.js'));
    $home = file_get_contents(resource_path('views/welcome.blade.php'));
    $runtime = file_get_contents(resource_path('js/pages/welcome-login-perspective.js'));
    $template = file_get_contents(resource_path('views/home/partials/login-perspective-template.blade.php'));
    $assets = [
        'resources/js/pages/welcome-login-perspective.js',
        'resources/css/pages/welcome-login-perspective.css',
    ];

    foreach ($assets as $asset) {
        expect(file_exists(base_path($asset)))->toBeTrue();
        expect($vite)->toContain($asset);
        expect($home)->toContain($asset);
    }

    expect($home)
        ->toContain("@include('home.partials.login-perspective-template')")
        ->and($template)
        ->toContain('data-login-perspective-state="choices"')
        ->toContain('data-login-perspective-state="admin"')
        ->toContain('data-login-perspective-state="guru"')
        ->toContain('data-login-perspective-state="murid"')
        ->toContain('data-google-popup="1"')
        ->and($runtime)
        ->toContain("activateState(button.getAttribute('data-login-role-target'))")
        ->toContain("container.addEventListener('click', closePerspective)")
        ->not->toContain("activateState(role, link.href)");
});

it('binds popup OAuth results to the active browser attempt instead of relying on opener alone', function (): void {
    $login = file_get_contents(resource_path('js/pages/public-login.js'));
    $bridge = file_get_contents(resource_path('js/pages/welcome/login-perspective-google.js'));
    $callback = file_get_contents(resource_path('views/auth/google-popup-result.blade.php'));

    expect($login)
        ->toContain("popupUrl.searchParams.set('popup_token', token)")
        ->and($bridge)
        ->toContain("payload.token !== activeToken")
        ->toContain("new BroadcastChannel('schoolai-google-auth')")
        ->toContain("event.key !== 'schoolai-google-auth'")
        ->and($callback)
        ->toContain("'token' => \$popupToken")
        ->toContain("new BroadcastChannel('schoolai-google-auth')")
        ->toContain("localStorage.setItem('schoolai-google-auth'");
});

it('does not restore the deleted import-based homepage perspective implementation', function (): void {
    $welcomeJs = file_get_contents(resource_path('js/pages/welcome.js'));
    $welcomeCss = file_get_contents(resource_path('css/pages/welcome.css'));

    expect($welcomeJs)
        ->not->toContain('login-perspective-navigation')
        ->and($welcomeCss)
        ->not->toContain('050-login-perspective-navigation');
});

it('keeps direct login URLs on the existing fallback auth shell', function (): void {
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

it('renders the homepage login state template without replacing the landing page', function (): void {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('data-login-perspective-template', false)
        ->assertSee('data-login-role-target="admin"', false)
        ->assertSee('data-login-role-target="guru"', false)
        ->assertSee('data-login-role-target="murid"', false);
});

it('serves every direct public login role through the fallback perspective shell', function (string $routeName): void {
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
