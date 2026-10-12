<?php

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

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
        ->toContain('media:migrate-r2 gallery --dry-run')
        ->toContain('r2 bucket cors set almustaqbal')
        ->toContain('https://media.almustaqbal.sch.id/...')
        ->toContain('Media first-party tidak')
        ->toContain('memiliki pengecualian storage lokal pada release ini');
});

it('keeps cPanel media on R2 without requiring a public storage symlink or embedding credentials', function (): void {
    $runner = file_get_contents(base_path('deploy/cpanel/clear.php.template'));
    $builder = file_get_contents(base_path('scripts/build-cpanel-package.sh'));
    $environment = file_get_contents(base_path('deploy/cpanel/.env.production.example'));

    expect($runner)->toContain('R2MediaConfiguration::class')->not->toContain('symlink(');
    expect($builder)->toContain('"MEDIA_DISK" "s3"')->toContain('--exclude "/media/***"')
        ->not->toMatch('/set_env_value.*"APP_KEY" "base64:[^"]+"/')
        ->not->toMatch('/set_env_value.*"DB_PASSWORD" "[^"]+"/');
    expect($environment)->toContain('FILESYSTEM_DISK=local')->toContain('MEDIA_DISK=s3')
        ->toContain('AWS_DEFAULT_REGION=auto')->toContain('AWS_URL=https://media.almustaqbal.sch.id')
        ->toMatch('/^AWS_ACCESS_KEY_ID=$/m')->toMatch('/^AWS_SECRET_ACCESS_KEY=$/m');
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

it('runs the cPanel preflight and migrations in an isolated sandbox without a storage symlink', function (): void {
    $files = new Filesystem;
    $root = sys_get_temp_dir().'/schoolai-cpanel-proof-'.Str::uuid();
    $application = $root.'/schoolai';
    $public = $root.'/public_html';
    $files->ensureDirectoryExists($application);
    $files->ensureDirectoryExists($public);
    try {
        foreach (['app', 'bootstrap', 'config', 'database/migrations', 'lang', 'resources/views', 'routes'] as $directory) {
            $files->copyDirectory(base_path($directory), $application.'/'.$directory);
        }
        $files->copyDirectory(public_path('build'), $public.'/build');
        foreach (['artisan', 'composer.json', 'composer.lock'] as $file) {
            $files->copy(base_path($file), $application.'/'.$file);
        }
        symlink(base_path('vendor'), $application.'/vendor');
        file_put_contents($application.'/.public-path', '../public_html');
        file_put_contents($application.'/.env', "APP_ENV=production\nAPP_DEBUG=false\n");
        $database = $root.'/proof.sqlite';
        touch($database);
        $token = Str::random(32);
        $runner = str_replace(['__APP_DIR_NAME__', '__DEPLOY_TOKEN_HASH__'], ['schoolai', hash('sha256', $token)],
            file_get_contents(base_path('deploy/cpanel/clear.php.template')));
        file_put_contents($public.'/clear.php', $runner);
        $process = new Process([PHP_BINARY, '-r', '$_GET["token"]=$argv[1]; require $argv[2];', $token, $public.'/clear.php'], $root, [
            'APP_ENV' => 'production', 'APP_DEBUG' => 'false', 'APP_KEY' => 'base64:'.base64_encode(random_bytes(32)),
            'APP_URL' => 'https://almustaqbal.sch.id', 'DB_CONNECTION' => 'sqlite', 'DB_DATABASE' => $database, 'DB_URL' => '',
            'CACHE_STORE' => 'array', 'SESSION_DRIVER' => 'array', 'FILESYSTEM_DISK' => 'local', 'MEDIA_DISK' => 's3',
            'MEDIA_PUBLIC_URL' => 'https://media.almustaqbal.sch.id', 'AWS_URL' => 'https://media.almustaqbal.sch.id',
            'AWS_ENDPOINT' => 'https://test-account.r2.cloudflarestorage.com', 'AWS_DEFAULT_REGION' => 'auto',
            'AWS_ACCESS_KEY_ID' => 'test-key', 'AWS_SECRET_ACCESS_KEY' => 'test-secret', 'AWS_BUCKET' => 'test-bucket',
        ], timeout: 60);
        $process->mustRun();
        expect($process->getOutput())->toContain('MAINTENANCE COMPLETE')
            ->toContain('OK: php artisan migrate')->toContain('no public storage symlink is required');
        expect(file_exists($public.'/storage'))->toBeFalse()
            ->and(file_exists($public.'/clear.php'))->toBeFalse()
            ->and(is_dir($application.'/storage/app/private'))->toBeTrue();
        $marker = json_decode(file_get_contents($application.'/storage/framework/deploy-complete.json'), true, flags: JSON_THROW_ON_ERROR);
        expect($marker['media_disk'])->toBe('s3')->and($marker['media_public_url'])->toBe('https://media.almustaqbal.sch.id');
    } finally {
        $files->deleteDirectory($root);
    }
});
