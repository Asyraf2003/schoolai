<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->timestamp('disabled_at')
                ->nullable()
                ->index()
                ->after('role');

            $table->timestamp('last_login_at')
                ->nullable()
                ->after('disabled_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex(['disabled_at']);
            $table->dropColumn([
                'disabled_at',
                'last_login_at',
            ]);
        });
    }
};
