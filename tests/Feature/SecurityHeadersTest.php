<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('adds nonce based security headers to public pages', function (): void {
    $pages = [
        [route('home'), true],
        [route('ppdb'), false],
        [route('galeri'), false],
    ];

    foreach ($pages as [$url, $allowsThreeRuntime]) {
        $response = $this->get($url)->assertOk();

        $response
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader(
                'Referrer-Policy',
                'strict-origin-when-cross-origin'
            )
            ->assertHeader(
                'Permissions-Policy',
                'camera=(), microphone=(), geolocation=(), payment=(), usb=()'
            )
            ->assertHeader('X-Frame-Options', 'DENY');

        $csp = (string) $response->headers->get(
            'Content-Security-Policy'
        );

        expect($csp)
            ->toContain("default-src 'self'")
            ->toContain("frame-ancestors 'none'")
            ->toContain("script-src-attr 'none'")
            ->toContain("img-src 'self' data: blob: https://media.almustaqbal.sch.id")
            ->toContain("connect-src 'self' https://media.almustaqbal.sch.id")
            ->toContain("media-src 'self' blob: https://media.almustaqbal.sch.id")
            ->toContain(
                "frame-src 'self' https://www.youtube.com https://www.youtube-nocookie.com https://www.tiktok.com https://www.instagram.com https://www.facebook.com https://player.vimeo.com"
            )
            ->not->toContain('frame-src *')
            ->not->toContain('frame-src https:');

        if ($allowsThreeRuntime) {
            expect($csp)
                ->toContain('https://cdn.jsdelivr.net')
                ->toContain(
                    "connect-src 'self' https://media.almustaqbal.sch.id https://cdn.jsdelivr.net"
                );
        } else {
            expect($csp)->not->toContain('https://cdn.jsdelivr.net');
        }

        expect(
            preg_match(
                "/script-src 'self' 'nonce-([^']+)'/",
                $csp,
                $matches
            )
        )->toBe(1);

        $nonce = $matches[1];

        expect($response->getContent())
            ->toContain('nonce="'.$nonce.'"')
            ->not->toContain('data-google-analytics');

        preg_match_all(
            '/<(?:script|style)\b(?![^>]*\bnonce=)[^>]*>/i',
            $response->getContent(),
            $missingNonces
        );

        expect($missingNonces[0])->toBe([]);
    }
});

it('allows Cloudflare Web Analytics only on production public pages', function (): void {
    config(['app.env' => 'production']);

    $response = $this->get(route('home'))->assertOk();
    $content = $response->getContent();
    $csp = (string) $response->headers->get('Content-Security-Policy');

    expect($content)
        ->not->toContain('data-google-analytics')
        ->not->toContain('googletagmanager.com');

    expect($csp)
        ->toContain('https://static.cloudflareinsights.com/beacon.min.js')
        ->not->toContain('googletagmanager.com')
        ->not->toContain('google-analytics.com')
        ->not->toContain('fonts.googleapis.com')
        ->not->toContain('fonts.gstatic.com');
});


it('adds the same security policy to authenticated admin pages', function (): void {
    $admin = User::query()->forceCreate([
        'name' => 'Security Header Admin',
        'email' => 'security-header-admin@example.test',
        'email_verified_at' => now(),
        'password' => Hash::make('unused-password'),
        'role' => User::ROLE_ADMIN,
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'DENY');

    $csp = (string) $response->headers->get(
        'Content-Security-Policy'
    );

    expect($csp)
        ->toContain("frame-ancestors 'none'")
        ->toContain("script-src 'self' 'nonce-")
        ->toContain("style-src 'self' 'nonce-")
        ->not->toContain('https://cdn.jsdelivr.net');

    preg_match_all(
        '/<(?:script|style)\b(?![^>]*\bnonce=)[^>]*>/i',
        $response->getContent(),
        $missingNonces
    );

    expect($missingNonces[0])->toBe([]);
});

it('sends HSTS only for secure production requests', function (): void {
    config(['app.env' => 'production']);

    $this->get(route('home'))
        ->assertOk()
        ->assertHeaderMissing('Strict-Transport-Security');

    $this->get('https://localhost/')
        ->assertOk()
        ->assertHeader(
            'Strict-Transport-Security',
            'max-age=31536000'
        );
});
