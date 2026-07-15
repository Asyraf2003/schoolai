<?php

use App\Http\Controllers\Auth\GoogleAuthController;

it('does not expose preference or logout mutations through GET', function (): void {
    $this->get('/bahasa/en')->assertMethodNotAllowed();
    $this->get('/logout')->assertMethodNotAllowed();
});

it('requires a valid CSRF token for the language mutation', function (): void {
    app()->detectEnvironment(fn (): string => 'production');

    try {
        $this->post(route('language.switch', 'en'))
            ->assertStatus(419);

        $token = 'known-csrf-token';

        $this->withSession(['_token' => $token])
            ->post(route('language.switch', 'en'), ['_token' => $token])
            ->assertRedirect();
    } finally {
        app()->detectEnvironment(fn (): string => 'testing');
    }
});

it('keeps the OAuth callback stateful', function (): void {
    $source = file_get_contents((new ReflectionClass(GoogleAuthController::class))->getFileName());

    expect($source)
        ->not->toBeFalse()
        ->not->toContain('stateless()');
});
