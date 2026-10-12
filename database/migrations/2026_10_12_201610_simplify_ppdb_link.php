<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['ppdb_settings', 'ppdb_showcase_items'] as $name) {
            if (! Schema::hasTable('legacy_'.$name) && Schema::hasTable($name)) {
                Schema::rename($name, 'legacy_'.$name);
            }
        }
        if (! Schema::hasTable('ppdb_settings')) {
            Schema::create('ppdb_settings', function (Blueprint $table): void {
                $table->unsignedTinyInteger('id')->primary();
                $table->string('registration_url', 2048)->nullable();
                $table->timestamps();
            });
        }
        $old = DB::table('legacy_ppdb_settings')->orderBy('id')->first();
        $url = $old?->registration_url;
        $parts = is_string($url) ? parse_url($url) : [];
        $host = strtolower($parts['host'] ?? '');
        $path = $parts['path'] ?? '';
        $safe = is_string($url) && filter_var($url, FILTER_VALIDATE_URL) && $old?->is_active
            && ($parts['scheme'] ?? '') === 'https' && ! isset($parts['user']) && ! isset($parts['port'])
            && ! preg_match('/[\x00-\x20\x7f]/', $url)
            && (($host === 'forms.gle' && preg_match('~^/[A-Za-z0-9_-]+/?$~D', $path))
                || ($host === 'docs.google.com' && preg_match('~^/forms/(?:u/\d+/)?d/(?:e/)?[A-Za-z0-9_-]+/viewform/?$~D', $path)));
        DB::table('ppdb_settings')->insertOrIgnore([
            'id' => 1, 'registration_url' => $safe ? $url : null,
            'created_at' => $old?->created_at ?? now(), 'updated_at' => $old?->updated_at ?? now(),
        ]);
    }

    public function down(): void
    {
        throw new RuntimeException('Forward-only PPDB migration: archived settings remain available for a safe forward restore.');
    }
};
