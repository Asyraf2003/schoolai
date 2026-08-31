<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

it('keeps Google OAuth stateful and removes bootstrap admin configuration', function (): void {
    $authSource = file_get_contents(app_path('Http/Controllers/Auth/Concerns/AuthenticatesGoogleUsers.php'));
    $resolverSource = file_get_contents(app_path('Http/Controllers/Auth/Concerns/ResolvesGoogleUsers.php'));
    $servicesSource = file_get_contents(config_path('services.php'));

    expect($authSource)->not->toContain('stateless()')
        ->and($resolverSource)->not->toContain('new User')
        ->and($servicesSource)->not->toContain('bootstrap_admin');
});

it('sets separate admin and guru OAuth intents without using them as authorization', function (): void {
    $provider = Mockery::mock();
    $provider->shouldReceive('redirect')->twice()->andReturn(redirect('https://accounts.google.com'));
    Socialite::shouldReceive('driver')->with('google')->twice()->andReturn($provider);

    $this->get(route('google.redirect'))
        ->assertRedirect('https://accounts.google.com')
        ->assertSessionHas('google_login_role', 'admin')
        ->assertSessionHas('google_login_popup', false)
        ->assertSessionHas('google_login_popup_token', '');

    $this->get(route('google.guru.redirect'))
        ->assertRedirect('https://accounts.google.com')
        ->assertSessionHas('google_login_role', 'guru')
        ->assertSessionHas('google_login_popup', false)
        ->assertSessionHas('google_login_popup_token', '');
});

it('stores popup presentation state separately from the authorized Google role', function (): void {
    $provider = Mockery::mock();
    $provider->shouldReceive('redirect')->twice()->andReturn(redirect('https://accounts.google.com'));
    Socialite::shouldReceive('driver')->with('google')->twice()->andReturn($provider);

    $this->get(route('google.redirect', ['popup' => 1, 'popup_token' => 'admin-attempt-token']))
        ->assertRedirect('https://accounts.google.com')
        ->assertSessionHas('google_login_role', 'admin')
        ->assertSessionHas('google_login_popup', true)
        ->assertSessionHas('google_login_popup_token', 'admin-attempt-token');

    $this->get(route('google.guru.redirect', ['popup' => 1, 'popup_token' => 'guru-attempt-token']))
        ->assertRedirect('https://accounts.google.com')
        ->assertSessionHas('google_login_role', 'guru')
        ->assertSessionHas('google_login_popup', true)
        ->assertSessionHas('google_login_popup_token', 'guru-attempt-token');
});

it('applies the named limiter to every Google OAuth endpoint', function (): void {
    foreach (['google.redirect', 'google.guru.redirect', 'google.callback'] as $name) {
        expect(Route::getRoutes()->getByName($name)?->gatherMiddleware())
            ->toContain('throttle:google-oauth');
    }

    $provider = file_get_contents(app_path('Providers/AppServiceProvider.php'));
    expect($provider)->toContain("hash_hmac('sha256'")
        ->not->toContain("'google-oauth:ip:'.\$request->ip()")
        ->not->toContain("'google-oauth:session:'.\$sessionId");
});

it('does not seed a default admin email or reusable admin password', function (): void {
    $source = file_get_contents(database_path('seeders/AdminUserSeeder.php'));

    expect($source)
        ->not->toContain('ADMIN_PASSWORD')
        ->not->toContain('admin@gmail.com')
        ->not->toContain('12345678')
        ->toContain("env('ADMIN_EMAIL')")
        ->toContain('User::ROLE_ADMIN');
});
