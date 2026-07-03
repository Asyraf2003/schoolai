<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@schoolai.test')],
            [
                'name' => env('ADMIN_NAME', 'Admin SchoolAI'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'Admin12345!')),
            ]
        );
    }
}
