<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('keeps the same education gallery and article mega menus on every public page', function (): void {
    foreach (['home', 'ppdb', 'artikel', 'galeri'] as $routeName) {
        $response = $this
            ->withSession(['locale' => 'id'])
            ->get(route($routeName));

        $response
            ->assertOk()
            ->assertSee('nav-shell', false)
            ->assertSee('data-nav-mega', false)
            ->assertDontSee('data-nav-mega-video', false)
            ->assertDontSee('youtube-nocookie.com', false)
            ->assertSee('Pendidikan')
            ->assertSee('Galeri')
            ->assertSee('Artikel')
            ->assertSee('Kegiatan')
            ->assertSee('Prestasi')
            ->assertSee('Program')
            ->assertSee('Pendidikan');

        expect(substr_count($response->getContent(), 'data-nav-mega'))->toBeGreaterThanOrEqual(3);
    }
});
