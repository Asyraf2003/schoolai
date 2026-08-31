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

    $this->withSession([
        'google_login_role' => AccountRole::Admin->value,
        'google_login_popup' => true,
    ])->get(route('google.callback'))
        ->assertOk()
        ->assertSee('schoolai-google-auth', false)
        ->assertSee(route('admin.dashboard'), false)
        ->assertSee(__('app.auth.success.logged_in'));

    $this->assertAuthenticatedAs($admin);
});

it('returns a failed popup bridge without authenticating an unavailable account', function (): void {
    fakePerspectiveGoogle('outsider-perspective-id', 'outsider-perspective@example.test');

    $this->withSession([
        'google_login_role' => AccountRole::Guru->value,
        'google_login_popup' => true,
    ])->get(route('google.callback'))
        ->assertOk()
        ->assertSee('schoolai-google-auth', false)
        ->assertSee(__('app.auth.errors.access_unavailable'))
        ->assertSee(route('guru.login'), false);

    $this->assertGuest();
});
