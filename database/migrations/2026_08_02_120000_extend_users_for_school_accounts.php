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
            $table->string('email')->nullable()->change();
            $table->string('role', 20)->nullable()->default(null)->change();
            $table->string('email_normalized')->nullable()->after('email');
            $table->string('student_id', 32)->nullable()->after('google_id');
            $table->string('student_id_normalized', 32)->nullable()->after('student_id');
            $table->timestamp('password_changed_at')->nullable()->after('last_login_at');
            $table->unsignedBigInteger('session_version')->default(0)->after('password_changed_at');
        });

        DB::table('users')
            ->select(['id', 'email'])
            ->orderBy('id')
            ->each(function (object $user): void {
                DB::table('users')->where('id', $user->id)->update([
                    'email' => $this->normalizeEmail($user->email),
                    'email_normalized' => $this->normalizeEmail($user->email),
                ]);
            });

        DB::table('users')->where('role', 'user')->update(['role' => null]);

        Schema::table('users', function (Blueprint $table): void {
            $table->unique('email_normalized', 'users_email_normalized_unique');
            $table->unique('student_id_normalized', 'users_student_id_normalized_unique');
            $table->index('student_id', 'users_student_id_index');
        });

        Schema::dropIfExists('auth_bootstrap_states');
    }

    public function down(): void
    {
        Schema::create('auth_bootstrap_states', function (Blueprint $table): void {
            $table->string('key', 80)->primary();
            $table->unsignedBigInteger('claimed_user_id')->nullable();
            $table->timestamps();
        });

        DB::table('auth_bootstrap_states')->insert([
            'key' => 'first_google_admin',
            'claimed_user_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique('users_email_normalized_unique');
            $table->dropUnique('users_student_id_normalized_unique');
            $table->dropIndex('users_student_id_index');
        });

        DB::table('users')->whereNull('role')->update(['role' => 'user']);
        DB::table('users')->whereIn('role', ['guru', 'murid'])->update(['role' => 'user']);

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'email_normalized',
                'student_id',
                'student_id_normalized',
                'password_changed_at',
                'session_version',
            ]);
            $table->string('role', 20)->nullable(false)->default('user')->change();
        });
    }

    private function normalizeEmail(mixed $email): ?string
    {
        $normalized = mb_strtolower(trim((string) $email));

        return $normalized === '' ? null : $normalized;
    }
};
