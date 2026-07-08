<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ppdb_settings', function (Blueprint $table): void {
            $table->string('information_url', 2048)->nullable()->after('registration_url');
        });
    }

    public function down(): void
    {
        Schema::table('ppdb_settings', function (Blueprint $table): void {
            $table->dropColumn('information_url');
        });
    }
};
