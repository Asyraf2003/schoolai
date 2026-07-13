<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('google_id', 255)
                ->nullable()
                ->unique()
                ->after('email');

            $table->string('role', 20)
                ->default('user')
                ->index()
                ->after('password');
        });

        Schema::create(
            'auth_bootstrap_states',
            function (Blueprint $table): void {
                $table->string('key', 80)->primary();
                $table->unsignedBigInteger('claimed_user_id')
                    ->nullable();
                $table->timestamps();
            }
        );

        DB::table('auth_bootstrap_states')->insert([
            'key' => 'first_google_admin',
            'claimed_user_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_bootstrap_states');

        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['google_id']);
            $table->dropIndex(['role']);

            $table->dropColumn([
                'google_id',
                'role',
            ]);
        });
    }
};
