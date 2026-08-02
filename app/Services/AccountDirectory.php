<?php

namespace App\Services;

use App\Enums\AccountRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class AccountDirectory
{
    /** @return Collection<int, User> */
    public function list(?string $search, ?string $role, ?string $status): Collection
    {
        $query = User::query();
        $this->applySearch($query, $search);
        $this->applyRole($query, $role);
        $this->applyStatus($query, $status);

        return $query
            ->orderByRaw('CASE WHEN role IS NULL THEN 1 ELSE 0 END')
            ->orderBy('role')
            ->orderBy('name')
            ->limit(200)
            ->get();
    }

    private function applySearch(Builder $query, ?string $search): void
    {
        $needle = mb_strtolower(trim((string) $search));

        if ($needle === '') {
            return;
        }

        $like = '%'.$needle.'%';
        $query->where(function (Builder $searchQuery) use ($like): void {
            $searchQuery
                ->whereRaw('LOWER(name) LIKE ?', [$like])
                ->orWhere('email_normalized', 'like', $like)
                ->orWhere('student_id_normalized', 'like', $like);
        });
    }

    private function applyRole(Builder $query, ?string $role): void
    {
        if ($role === 'inert') {
            $query->whereNull('role');

            return;
        }

        $validRoles = array_column(AccountRole::cases(), 'value');
        if (in_array($role, $validRoles, true)) {
            $query->where('role', $role);
        }
    }

    private function applyStatus(Builder $query, ?string $status): void
    {
        if ($status === 'active') {
            $query->whereNull('disabled_at');
        } elseif ($status === 'inactive') {
            $query->whereNotNull('disabled_at');
        }
    }
}
