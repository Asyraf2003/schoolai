<?php

use App\Support\Media\LegacyMediaMigrator;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Command\Command;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

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
