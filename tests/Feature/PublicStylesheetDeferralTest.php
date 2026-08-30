<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('defers shared welcome styles only on the homepage', function (): void {
    $home = $this->get(route('home'))->assertOk()->getContent();

    expect($home)
        ->toContain('data-home-deferred-style')
        ->toContain('media="print"');

    foreach (['ppdb', 'artikel', 'galeri'] as $routeName) {
        $content = $this->get(route($routeName))->assertOk()->getContent();

        expect($content)
            ->not->toContain('data-home-deferred-style')
            ->not->toContain('media="print"');
    }
});
