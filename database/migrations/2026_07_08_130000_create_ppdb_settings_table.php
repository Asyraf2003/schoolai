<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppdb_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('registration_url', 2048)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('ppdb_settings')->insert([
            'registration_url' => 'https://forms.gle/1huqPo24Et6pgUNh6',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('ppdb_settings');
    }
};