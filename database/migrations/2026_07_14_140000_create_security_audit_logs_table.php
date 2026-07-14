<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'security_audit_logs',
            function (Blueprint $table): void {
                $table->id();
                $table->foreignId('actor_user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
                $table->string('event', 120);
                $table->string(
                    'auditable_type',
                    160
                )->nullable();
                $table->string(
                    'auditable_id',
                    64
                )->nullable();
                $table->string(
                    'ip_address',
                    45
                )->nullable();
                $table->string(
                    'user_agent',
                    500
                )->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('created_at');

                $table->index(
                    ['event', 'created_at'],
                    'security_audit_event_time_idx'
                );
                $table->index(
                    [
                        'auditable_type',
                        'auditable_id',
                    ],
                    'security_audit_subject_idx'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('security_audit_logs');
    }
};
