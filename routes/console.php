<?php

use App\Actions\Auth\BootstrapAdmin;
use App\Support\Media\LegacyMediaMigrator;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Command\Command;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command(
    'auth:bootstrap-admin {--name= : Official admin display name} {--email= : Verified Google email for the first admin}',
    function (): int {
        $name = trim((string) $this->option('name'));
        $email = trim((string) $this->option('email'));

        if ($name === '') {
            $name = trim((string) $this->ask('Official admin name'));
        }

        if ($email === '') {
            $email = trim((string) $this->ask('Verified Google email'));
        }

        try {
            $admin = app(BootstrapAdmin::class)->create($name, $email);
        } catch (InvalidArgumentException|LogicException $exception) {
            $this->error($exception->getMessage());

            return Command::FAILURE;
        }

        $this->info('First admin account provisioned successfully.');
        $this->line('Admin email: '.$admin->email);
        $this->comment(
            'Next, open /login/admin and sign in with exactly this verified Google account. '
            .'The Google ID will bind on the first successful login.'
        );

        return Command::SUCCESS;
    }
)->purpose('Provision the only bootstrap admin before first Google sign-in');

Artisan::command('media:migrate-r2 {owner?} {--dry-run}', function (): int {
    $stats = app(LegacyMediaMigrator::class)->migrate(
        $this->argument('owner'),
        (bool) $this->option('dry-run'),
    );

    $this->table(['scanned', 'eligible', 'migrated', 'skipped', 'failed'], [[
        $stats['scanned'],
        $stats['eligible'],
        $stats['migrated'],
        $stats['skipped'],
        $stats['failed'],
    ]]);

    foreach ($stats['errors'] as $error) {
        $this->error($error);
    }

    return $stats['failed'] === 0 ? Command::SUCCESS : Command::FAILURE;
})->purpose('Migrate bounded legacy first-party media owners to canonical R2 URLs');
