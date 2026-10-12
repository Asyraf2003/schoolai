<?php

use App\Enums\AccountRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;

uses(RefreshDatabase::class);

function fakePerspectiveGoogle(string $id, string $email): void
{
    $googleUser = (new GoogleUser)->map([
        'id' => $id,
        'email' => $email,
        'name' => 'Perspective Google User',
        'nickname' => null,
    ]);
    $googleUser->user = ['email_verified' => true];

    $provider = Mockery::mock();
    $provider->shouldReceive('user')->once()->andReturn($googleUser);
    Socialite::shouldReceive('driver')->with('google')->once()->andReturn($provider);
}

it('returns a successful popup bridge after authenticating an allowed admin', function (): void {
    $admin = User::query()->forceCreate([
        'name' => 'Admin Perspective',
        'email' => 'admin-perspective@example.test',
        'password' => Hash::make('unused-password'),
        'role' => AccountRole::Admin->value,
    ]);
    fakePerspectiveGoogle('admin-perspective-id', $admin->email);

    $response = $this->withSession([
        'google_login_role' => AccountRole::Admin->value,
        'google_login_popup' => true,
        'google_login_popup_token' => 'admin-popup-token',
    ])->get(route('google.callback'));

    $response
        ->assertOk()
        ->assertViewIs('auth.google-popup-result')
        ->assertSee('schoolai-google-auth', false);

    expect($response->viewData('ok'))->toBeTrue()
        ->and($response->viewData('role'))->toBe(AccountRole::Admin->value)
        ->and($response->viewData('redirect'))->toBe(route('admin.dashboard'))
        ->and($response->viewData('message'))->toBe(__('app.auth.success.logged_in'))
        ->and($response->viewData('popupToken'))->toBe('admin-popup-token');
    $this->assertAuthenticatedAs($admin);
});

it('returns a failed popup bridge without authenticating an unavailable account', function (): void {
    fakePerspectiveGoogle('outsider-perspective-id', 'outsider-perspective@example.test');

    $response = $this->withSession([
        'google_login_role' => AccountRole::Guru->value,
        'google_login_popup' => true,
        'google_login_popup_token' => 'guru-popup-token',
    ])->get(route('google.callback'));

    $response
        ->assertOk()
        ->assertViewIs('auth.google-popup-result')
        ->assertSee('schoolai-google-auth', false);

    expect($response->viewData('ok'))->toBeFalse()
        ->and($response->viewData('role'))->toBe(AccountRole::Guru->value)
        ->and($response->viewData('redirect'))->toBeNull()
        ->and($response->viewData('message'))->toBe(__('app.auth.errors.access_unavailable'))
        ->and($response->viewData('fallbackUrl'))->toBe(route('guru.login'))
        ->and($response->viewData('popupToken'))->toBe('guru-popup-token');
    $this->assertGuest();
});
