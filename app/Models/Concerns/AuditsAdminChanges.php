<?php

namespace App\Models\Concerns;

use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

trait AuditsAdminChanges
{
    public static function bootAuditsAdminChanges(): void
    {
        static::created(
            fn (Model $model) => $model->recordAdminChange(
                'created'
            )
        );

        static::updated(
            fn (Model $model) => $model->recordAdminChange(
                'updated'
            )
        );

        static::deleted(
            fn (Model $model) => $model->recordAdminChange(
                'deleted'
            )
        );

        if (
            in_array(
                SoftDeletes::class,
                class_uses_recursive(static::class),
                true
            )
        ) {
            static::restored(
                fn (Model $model) => $model->recordAdminChange(
                    'restored'
                )
            );
        }
    }

    private function recordAdminChange(string $action): void
    {
        $actor = Auth::user();

        if (
            ! $actor instanceof User
            || ! $actor->isAdmin()
        ) {
            return;
        }

        $metadata = [];

        if ($action === 'updated') {
            $changedFields = array_values(
                array_diff(
                    array_keys($this->getChanges()),
                    [
                        'created_at',
                        'updated_at',
                        'deleted_at',
                    ]
                )
            );

            if ($changedFields === []) {
                return;
            }

            $metadata['changed_fields'] = $changedFields;
        }

        app(AuditLogger::class)->record(
            'content.'
                .Str::snake(class_basename($this))
                .'.'
                .$action,
            actor: $actor,
            subject: $this,
            metadata: $metadata,
        );
    }
}
