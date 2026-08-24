<?php

it('keeps the R2 environment contract secret-free and deployment-explicit', function (): void {
    $environment = file_get_contents(base_path('.env.example'));
    $deployment = file_get_contents(base_path('deploy/cpanel/README.md'));

    expect($environment)
        ->toContain('MEDIA_DISK=s3')
        ->toContain('MEDIA_PUBLIC_URL=https://media.almustaqbal.sch.id')
        ->toContain('MEDIA_CACHE_CONTROL="public, max-age=31536000, immutable"')
        ->toMatch('/^AWS_ACCESS_KEY_ID=$/m')
        ->toMatch('/^AWS_SECRET_ACCESS_KEY=$/m')
        ->toMatch('/^AWS_ENDPOINT=$/m');

    expect($deployment)
        ->toContain('media:migrate-r2 hero --dry-run')
        ->toContain('r2 bucket cors set almustaqbal')
        ->toContain('https://media.almustaqbal.sch.id/...')
        ->toContain('Pengecualian H6 yang eksplisit')
        ->toContain('Upload konten first-party baru tidak termasuk pengecualian dan wajib masuk R2');
});

it('declares exact-origin read-only CORS for WebGL textures and ranged media', function (): void {
    $policy = json_decode(
        file_get_contents(base_path('deploy/cloudflare/r2-cors.json')),
        true,
        flags: JSON_THROW_ON_ERROR,
    );
    $rule = $policy['rules'][0];

    expect($rule['allowed']['origins'])->toBe([
        'https://almustaqbal.sch.id',
        'http://localhost',
        'http://localhost:8000',
        'http://127.0.0.1:8000',
    ])->not->toContain('*')
        ->and($rule['allowed']['methods'])->toBe(['GET', 'HEAD'])
        ->and($rule['allowed']['headers'])->toBe(['Range'])
        ->and($rule['exposeHeaders'])->toContain('Content-Range', 'cf-cache-status')
        ->and($rule)->not->toHaveKey('allowCredentials');
});
