<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\AccountIdentity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = AccountIdentity::email(env('ADMIN_EMAIL'));

        if ($email === null) {
            return;
        }

        $admin = User::query()->firstOrNew(['email_normalized' => $email]);
        $admin->forceFill([
            'name' => trim((string) env('ADMIN_NAME', 'Admin SchoolAI')),
            'email' => $email,
            'role' => User::ROLE_ADMIN,
        ]);

        if (! $admin->exists) {
            $admin->password = Str::random(64);
        }

        $admin->save();
    }
}
